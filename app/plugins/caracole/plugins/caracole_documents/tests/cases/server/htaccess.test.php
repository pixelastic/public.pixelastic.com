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
	// Will always upload in foo/bar/baz
	function getOrCreateUploadDirectory() {
		return $this->uploadDirectory = call_user_func_array(array('parent', 'getOrCreateUploadDirectory'), array('foo', 'bar', 'baz'));
	}
	// Use a classic rename instead of move_uploaded_file
	function __moveUploadedFile($source, $destination) {
		return rename($source, $destination);
	}
}
class DocumentHtaccessTestCase extends CakeWebTestCase {
	var $fixtures = array(
		'plugin.caracole_documents.document',
		'plugin.caracole_documents.metadata',
	);

	function setUp() {
		//Base url
		$this->baseUrl = Configure::read('SiteUrl.default');

		// Models to test
		$this->model = ClassRegistry::init('TestDocument');

		// Creating a temp file that we will use to simulate an upload
		$this->baseDir = APP.'plugins'.DS.'caracole'.DS.'plugins'.DS.'caracole_documents'.DS.'tests'.DS.'uploads'.DS;

		// Dummy txt file
		$this->originalFile = $this->baseDir.'foo.txt';
		$this->tmpFile = $this->baseDir.'tmp_txt-file.tmp';
		if (!file_exists($this->tmpFile)) copy($this->originalFile, $this->tmpFile);
		$this->filedata = array('name'=>'foo.txt','type'=>'text/plain','tmp_name'=>$this->tmpFile,'error'=>0,'size'=>3);

		// Uploading file
		$this->model->upload($this->filedata);

	}

	// Cleaning our mess after each test
	// Warning : Be aware that any fatal execution error will not trigger this method and thus won't clean the mess
	function tearDown() {
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

	// Can get directly a file from its path
	function testGetDirectFileFromPath() {
		$result = $this->get($this->baseUrl.$this->model->uploadedFile['path']);
		$this->assertEqual($result, 'foo');
	}

	// Can get a file with any filename
	function testGetFileWithArbitraryFilename() {
		$url = implode('/', array(
			trim($this->baseUrl, '/'),
			'files',
			'foo',
			'bar',
			'baz',
			$this->model->uploadedFile['id'],
			'arbitraryName.'.$this->model->uploadedFile['ext']
		));

		$result = $this->get($url);
		$this->assertEqual($result, 'foo');
	}

	// Can't browse any subdirectories
	function testCantBrowseSubdirectories() {
		$result = $this->get($this->baseUrl.'files/foo/bar/baz/'.$this->model->uploadedFile['id']);
		$this->assertResponse(404);
		$result = $this->get($this->baseUrl.'files/foo/bar/baz/');
		$this->assertResponse(404);
		$result = $this->get($this->baseUrl.'files/foo/bar/');
		$this->assertResponse(404);
		$result = $this->get($this->baseUrl.'files/foo/');
		$this->assertResponse(404);
		$result = $this->get($this->baseUrl.'files/');
		$this->assertResponse(404);
	}


}
  ?>