 <?php
App::import('Model', 'CaracoleDocuments.Document');

// Extending Document to save some variables to clean up mess
class TestDocument extends Document {
	var $name = 'Document';
	var $alias = 'Document';
	// Override the upload method to grab the file name
	function upload($filedata) {
		return $this->uploadedFile = parent::upload($filedata);
	}
	// Override the directory creation method to get the directory created
	function getOrCreateUploadDirectory() {
		return $this->uploadDirectory = call_user_func_array(array('parent', 'getOrCreateUploadDirectory'), func_get_args());
	}
	// Use a classic rename instead of move_uploaded_file
	function __moveUploadedFile($source, $destination) {
		return rename($source, $destination);
	}
}

// Dummy Page class : related to a document
class Page extends CakeTestModel {
	var $name = 'Page';
	var $actsAs = array('Documentable', 'Containable'); // Important that Containable is last
	var $belongsTo = array('File' => array('className' => 'CaracoleDocuments.Document', 'foreignKey' => 'document_file'));
	var $adminSettings = array();
}

// Dummy Collection class : hasAndBelongsToMany Document
class Collection extends CakeTestModel {
	var $name = 'Collection';
	var $actsAs = array('Documentable', 'Containable'); // Important that Containable is last
	var $hasAndBelongsToMany = array('Document' => array('className' => 'CaracoleDocuments.Document'));
	var $adminSettings = array();
}


class DocumentTestCase extends CakeTestCase {
	var $fixtures = array(
		'plugin.caracole_documents.document',
		'plugin.caracole_documents.metadata',
		'plugin.caracole_documents.document_page',
		'plugin.caracole_documents.collection',
		'plugin.caracole_documents.collections_document',

	);

	function startTest() {
		ClassRegistry::flush();

		// Models to test
		$this->model = ClassRegistry::init('TestDocument');
		$this->page = ClassRegistry::init('Page');
		$this->collection = ClassRegistry::init('Collection');

		// Creating a temp file that we will use to simulate an upload
		$this->baseDir = APP.'plugins'.DS.'caracole'.DS.'plugins'.DS.'caracole_documents'.DS.'tests'.DS.'uploads'.DS;

		// Dummy txt file
		$this->originalFile = $this->baseDir.'foo.txt';
		$this->tmpFile = $this->baseDir.'tmp_txt-file.tmp';
		if (!file_exists($this->tmpFile)) copy($this->originalFile, $this->tmpFile);
		$this->filedata = array('name'=>'foo.txt','type'=>'text/plain','tmp_name'=>$this->tmpFile,'error'=>0,'size'=>3);

		// Dummy php file
		$this->originalPhpFile = $this->baseDir.'foo.php';
		$this->tmpPhpFile = $this->baseDir.'tmp_php-file.tmp';
		if (!file_exists($this->tmpPhpFile)) copy($this->originalPhpFile, $this->tmpPhpFile);
		$this->phpFiledata = array('name'=>'foo.php','type'=>'application/octet-stream','tmp_name'=>$this->tmpPhpFile,'error'=>0,'size'=>19);

		// Php file disguised as a jpg
		$this->tmpFakeJpgFile = $this->baseDir.'tmp_php-disguised-as-jpg.tmp';
		if (!file_exists($this->tmpFakeJpgFile)) copy($this->originalPhpFile, $this->tmpFakeJpgFile);
		$this->fakeJpgFiledata = array('name'=>'kitten.jpg','type'=>'image/jpeg','tmp_name'=>$this->tmpFakeJpgFile,'error'=>0,'size'=>19);

		// Full data to be posted
		$this->data = array(
			'Page' => array(
				'name' => 'Bar',
				'document_file' => null,
				'upload_document_file' => $this->filedata
			)
		);

	}

	// Cleaning our mess after each test
	// Warning : Be aware that any fatal execution error will not trigger this method and thus won't clean the mess
	function endTest() {
		// removing any created files
		if (!empty($this->model->uploadedFile) && !empty($this->model->uploadedFile['path'])) {
			// Deleting element
			if (file_exists($this->model->uploadedFile['path'])) {
				unlink($this->model->uploadedFile['path']);
			}
		}

		// removing created directory
		if (!empty($this->model->uploadDirectory)) {
			// Deleting paths
			$paths = explode('/', trim($this->model->uploadDirectory, '/'));
			while(count($paths)>1) {
				@rmdir(WWW_ROOT.implode(DS, $paths));
				array_pop($paths);
			}
		}
	}

	// Correctly create an upload directory structure
	function testCreateUploadDirectoryForArbitraryDate() {
		$result = $this->model->getOrCreateUploadDirectory('foo', 'bar', 'baz');
		$this->assertTrue(file_exists(WWW_ROOT.'files/foo/bar/baz'));
	}

	// Returns a file extension
	function testGetFileExtension() {
		$this->filedata['name'] = 'foo.bar.baz';
		$result = $this->model->ext($this->filedata);
		$this->assertEqual($result, 'baz');
	}

	// Finds txt file as text/plain
	function testFindCorrectMimetypeForTxtFile() {
		$result = $this->model->mimetype($this->filedata);
		$this->assertEqual($result, 'text/plain');
	}

	// Gets a file size
	function testGetFilesize() {
		$result = $this->model->filesize($this->filedata);
		$this->assertEqual($result, 3);
	}

	// Gets a file original name
	function testGetOriginalName() {
		$result = $this->model->filename($this->filedata);
		$this->assertEqual($result, 'foo');
	}

	// Extracting metadata will set a filesize, ext, mimetype and filename
	function testExtractMetadata() {
		$this->filedata['filesize'] = 42;
		$this->filedata['mimetype'] = 'foo/bar';

		$result = $this->model->extractMetadata($this->filedata);
		$expected = array(
			'filename' => 'foo',
			'filesize' => 3,
			'mimetype' => 'text/plain',
			'ext' => 'txt'
		);
		$this->assertEqual($result, $expected);
	}


	// Accept TXT when only TXT is accepted
	function testOnlyAcceptSpecifiedExtension() {
		$data = $this->model->extractMetadata($this->filedata);
		$result = $this->model->isOfType($data, 'txt');
		$this->assertTrue($result);
	}

	// Refuse PHP when only TXT is accepted
	function testRefuseOtherThanOnlyAcceptSpecifiedExtension() {
		$data = $this->model->extractMetadata($this->phpFiledata);
		$result = $this->model->isOfType($data, 'txt');
		$this->assertFalse($result);
	}

	// Can't upload specified file extensions
	function testCantUploadSpecifiedExtension() {
		$data = $this->model->extractMetadata($this->filedata);
		$result = $this->model->isNotOfType($data, 'txt');
		$this->assertFalse($result);
	}

	// Can't upload if one or various file extensions
	function testCantUploadIfOnOfVariousExtensions() {
		$data = $this->model->extractMetadata($this->filedata);
		$result = $this->model->isNotOfType($data, array('txt', 'psd'));
		$this->assertFalse($result);
	}

	// Can't upload file not big enough
	function testCantAcceptNotBigEnoughFiles() {
		$data = $this->model->extractMetadata($this->filedata);
		$result = $this->model->minFilesize($data, 1000);
		$this->assertFalse($result);
	}

	// Accept big enough files
	function testOnlyAcceptBigEnoughFiles() {
		$data = $this->model->extractMetadata($this->phpFiledata);
		$result = $this->model->minFilesize($data, 10);
		$this->assertTrue($result);
	}

	// Can't upload file too big
	function testCantAcceptFilesTooSmall() {
		$data = $this->model->extractMetadata($this->phpFiledata);
		$result = $this->model->maxFilesize($data, 10);
		$this->assertFalse($result);
	}

	// Can't upload file too big
	function testOnlyAcceptSmallEnoughFiles() {
		$data = $this->model->extractMetadata($this->filedata);
		$result = $this->model->maxFilesize($data, 10);
		$this->assertTrue($result);
	}



	// PHP files can't be uploaded as a default measure
	function testCantUploadPHPFiles() {
		$data = $this->model->extractMetadata($this->phpFiledata);
		$result = $this->model->validatesUpload($data);
		$this->assertFalse($result);
	}

	// Set a default return message in case of validation fail
	function testSetDefaultUploadValidationFailMessage() {
		$this->model->validate['upload'] = array('noTxtFile' => array('rule' => array('isNotOfType', 'txt')));
		$data = $this->model->extractMetadata($this->filedata);
		$this->model->validatesUpload($data);
		$this->assertNotNull($this->model->uploadValidationError);
	}

	// Set a custom message in case of validation fail
	function testSetCustomUploadValidationFailMessage() {
		$this->model->validate['upload'] = array('noTxtFile' => array('rule' => array('isNotOfType', 'txt'), 'message' => 'fail'));
		$data = $this->model->extractMetadata($this->filedata);
		$this->model->validatesUpload($data);
		$result = $this->model->uploadValidationError;
		$this->assertEqual($result, 'fail');
	}

	// Will only return one error even if several validation failed
	function testFailOnFirstValidationError() {
		$this->model->validate['upload'] = array(
			'noTxtFile' => array('rule' => array('isNotOfType', 'txt'), 'message' => 'txt file'),
			'minSize' => array('rule' => array('minFilesize', 1000), 'message' => 'not big enough')
		);
		$data = $this->model->extractMetadata($this->filedata);
		$this->model->validatesUpload($data);
		$this->assertEqual($this->model->uploadValidationError, 'txt file');
	}


	// Uploaded file gets saved on disk
	function testUploadSavesFileOnDisk() {
		$data = $this->model->upload($this->filedata);

		$this->assertTrue($data['id']);
		$this->assertTrue(file_exists($data['path']));
	}


	// Error gets returned if there was an error while uploading
	function testUploadReturnAnErrorIfUploadError() {

		$this->filedata['error'] = UPLOAD_ERR_INI_SIZE;
		$data = $this->model->upload($this->filedata);
		$this->assertTrue($data['error']);

		$this->filedata['error'] = UPLOAD_ERR_FORM_SIZE;
		$data = $this->model->upload($this->filedata);
		$this->assertTrue($data['error']);

		$this->filedata['error'] = UPLOAD_ERR_PARTIAL;
		$data = $this->model->upload($this->filedata);
		$this->assertTrue($data['error']);

		$this->filedata['error'] = UPLOAD_ERR_NO_FILE;
		$data = $this->model->upload($this->filedata);
		$this->assertTrue($data['error']);

		$this->filedata['error'] = UPLOAD_ERR_NO_TMP_DIR;
		$data = $this->model->upload($this->filedata);
		$this->assertTrue($data['error']);

		$this->filedata['error'] = UPLOAD_ERR_CANT_WRITE;
		$data = $this->model->upload($this->filedata);
		$this->assertTrue($data['error']);
	}

	// Error gets returned if we can't move the uploaded file to its place on disk
	function testUploadReturnErrorIfCantSaveFileOnDisk() {
		Mock::generatePartial('TestDocument', 'MockTestDocumentMoveUploadedFile', array('__moveUploadedFile'));
		$this->model = new MockTestDocumentMoveUploadedFile();
		$this->model->setReturnValue('__moveUploadedFile', false);

		$data = $this->model->upload($this->filedata);
		$this->assertTrue($data['error']);
	}

	// Error gets returned if there is no such file uploaded
	function testUploadReturnsErrorIfNoFileUploaded() {
		$data = $this->model->upload(array());
		$this->assertTrue($data['error']);
	}

	// Error returned if unable to create the correct upload directory
	function testUploadReturnsErrorIfNoUploadDirectoryCreated() {
		Mock::generatePartial('TestDocument', 'MockDocumentGetOrCreateUploadDirectory', array('getOrCreateUploadDirectory'));
		$this->model = new MockDocumentGetOrCreateUploadDirectory();
		$this->model->setReturnValue('getOrCreateUploadDirectory', false);

		$data = $this->model->upload($this->filedata);
		$this->assertTrue($data['error']);
	}

	// Error when uploading a PHP file
	function testUploadReturnsErrorIfUploadingPhpFile() {
		$data = $this->model->upload($this->phpFiledata);
		$this->assertTrue($data['error']);
	}



	// Uploaded file gets saved in database
	function testInsertNewDocumentInDatabase() {
		$data = $this->model->insert($this->filedata);
		$result = $this->model->find('first', array('conditions' => array('Document.id' => $data['Document']['id'])));
		$this->assertTrue($result);
	}

	// Uploaded files that do not validate should be deleted
	function testDeletingFilesThatDoNotValidateWhenInsertingThem() {
		$_schema = $this->model->_schema;
		Mock::generatePartial('TestDocument', 'MockDocumentDoNotValidate', array('validates'));
		$this->model = new MockDocumentDoNotValidate();
		$this->model->_schema = $_schema;
		$this->model->setReturnValue('validates', false);

		$data = $this->model->insert($this->filedata);
		$result = file_exists(WWW_ROOT.$this->model->uploadedFile['path']);
		$this->assertFalse($result);
	}



	// When files are uploaded, we fill the model data with corresponding ids
	function testSetIdInsteadOfUpload() {
		$this->page->create($this->data);
		$this->page->File->loadIDFromUpload($this->page);

		$result = $this->page->data;
		$this->assertNotNull($result['Page']['document_file']);
	}

	// When loading id instead of uploads, we remove the upload fields
	function testRemoveUploadFieldsWhenLoadingIdFromUpload() {
		$this->page->create($this->data);
		$this->page->File->loadIDFromUpload($this->page);
		$result = $this->page->data;
		$this->assertTrue(empty($result['Page']['upload_document_file']));
	}

	// If document key set but no document data loaded, we load it
	function testLoadFullDocumentDataIfDocumentKeySet() {
		// No upload data but a document key set
		$this->data['Page']['document_file'] = 'testId';
		$this->data['Page']['upload_document_file'] = array('name' => null, 'type' => null, 'tmp_name' => null, 'error' => null, 'size' => null);
		unset($this->data['File']);

		$this->page->create($this->data);
		$this->page->File->loadIDFromUpload($this->page);
		$result = $this->page->data['File'];

		$this->assertEqual($result['id'], 'testId');
	}

	// When loading id from upload, failed upload should populate the validationErrors variable of the upload field
	function testFailedLoadIdWillSetValidationErrorsForFailedFields() {
		$this->data['Page']['upload_document_file'] = $this->phpFiledata;
		$this->page->create($this->data);
		$this->page->File->loadIDFromUpload($this->page);
		$result = $this->page->validationErrors['upload_document_file'];
		$this->assertTrue($result);
	}

	// Failed uploads replace existing document id with a null value
	function testFailedUploadsReplaceDocumentId() {
		// Forcing to fail the insert
		Mock::generatePartial('TestDocument', 'MockTestDocumentFailedInsert', array('insert'));
		$this->page->File = &new MockTestDocumentFailedInsert();
		$this->page->File->setReturnValue('insert', array('error' => 'Error!'));

		$this->data['Page']['document_file'] = 'foo'; // Setting a value

		// Creating the instance
		$this->page->create($this->data);
		$this->page->File->loadIDFromUpload($this->page);

		$result = $this->page->data;
		$this->assertNull($result['Page']['document_file']);
	}



	// We can add validation rules on upload fields directly from the main model
	function testValidationRulesCanBeAddedFromTheMainModel() {
		$this->page->validate = array(
			'upload_document_file' => array(
				'noTxtFile' => array('rule' => array('isNotOfType', 'txt'))
			)
		);

		$this->page->create($this->data);
		$this->page->File->loadIdFromUpload($this->page);
		$result = $this->page->validates();
		$this->assertFalse($result);
	}

	// Custom message of main model are passed to validation errors
	function testCustomValidateMessagesCanBePassedFromMainModel() {
		$this->page->validate = array(
			'upload_document_file' => array(
				'noTxtFile' => array('rule' => array('isNotOfType', 'txt'), 'message' => 'my fail')
			)
		);

		$this->page->create($this->data);
		$this->page->File->loadIdFromUpload($this->page);
		$this->page->validates();
		$result = $this->page->validationErrors;
		$this->assertEqual($result['upload_document_file'], 'my fail');
	}


	// Saving a Document with custom fields will create new entries in the associated Metadata table
	function testSavingDocumentWithCustomFieldsCreateMetadatas() {
		$data = array('Document' => array('id' => 'foo', 'path' => 'path', 'custom' => 'baz'));
		$this->model->create($data);
		$this->model->save();
		$result = $this->model->Metadata->find('count', array('conditions' => array('Metadata.name' => 'custom')));
		$this->assertEqual($result, 1);
	}

	// Saving a Document with custom existing fields will update them
	function testSavingDocumentWithExistingCustomFieldsUpdateThem() {
		// Saving a first time
		$data = array('Document' => array('id' => 'foo', 'path' => 'path', 'custom' => 'bar'));
		$this->model->create($data);
		$this->model->save();

		// Saving a second time and changing the custom attribute
		$data = array('Document' => array('id' => 'foo', 'path' => 'path', 'custom' => 'baz'));
		$this->model->create($data);
		$this->model->save();


		$result = $this->model->Metadata->find('count', array('conditions' => array('Metadata.name' => 'custom')));
		$this->assertEqual($result, 1);
	}




	 // Fetching exactly one document copies the metadata in the main document
	function testGettingMetadataDirectCallFirst() {
		$item = $this->model->find('first', array('conditions' => array('id' => 'testId')));
		$this->assertEqual($item['Document']['author'], 'myself');
	}

	 // Fetching several documents copies the metadata in the each document
	function testGettingMetadataDirectCallAll() {
		$item = $this->model->find('all');
		$this->assertEqual($item[0]['Document']['author'], 'myself');
	}

	// Fetching one model that has a Document related
	function testGettingMetadataFromMainModelFirst() {
		$item = $this->page->find('first', array('conditions' => array('Page.id' => 2)));;
		$this->assertEqual($item['File']['author'], 'myself');
	}

	// Fetching several models that have a Document related
	function testGettingMetadataFromMainModelAll() {
		$item = $this->page->find('all', array('conditions' => array('Page.id' => 2)));
		$this->assertEqual($item[0]['File']['author'], 'myself');
	}

	// Fetching a model that has Documents binded in a HABTM
	function testGettingMetadataFromMainModelHABTMFirst() {
		$item = $this->collection->find('first', array('conditions' => array('Collection.id' => 1)));
		$this->assertEqual($item['Document'][0]['author'], 'myself');
	}

	// Fetching various items that have a HABTM relation with Documents
	function testGettingMetadataFromMainModelHABTMAll() {
		$item = $this->collection->find('all', array('conditions' => array('Collection.id' => 1)));
		$this->assertEqual($item[0]['Document'][0]['author'], 'myself');
	}


	// Tester les versions sous image.test.php


}
  ?>