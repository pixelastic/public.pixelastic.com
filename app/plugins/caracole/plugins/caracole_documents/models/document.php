<?php
/**
 *	Document
 *	Main model for handling file upload and display.
 *
 *	TODO : Uploaded documents should really be served from an other subdomains, without cookies. This would prevent XSS
 *	attacks through the documents vector. Just creating a new CNAME is not enough if the files are still accessible
 *	through the first server, tho.
 **/
class Document extends AppModel {
	// Will hold first validation upload error
	var $uploadValidationError = null;
	// Metadatas
	var $hasMany = array(
		'Metadata' => array(
			'className' => 'CaracoleDocuments.Metadata',
			'foreignKey' => 'document_id',
			'dependent' => true
		)
	);


	/**
	 *	__construct
	 *	Creates the model. We need to use this method to define special translateable strings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
		$this->adminSettings = array(
			'toolbar' => array(
				'main' => array(
					'index' => false,
				)
			),
			'index' => array(
				'headers' => array(
					'Document.ext' => __d('caracole_documents', 'Ext', true),
					'Document.filename' => __d('caracole_documents', 'Name', true),
				),
				'paginate' => array(
					'Document' => array(
						'fields' => array('Document.id'),
						'order' => array('Document.created' => 'DESC'),
						'conditions' => array(
							'Document.parent_id' => 0,
							// Trick to find everything except images. We can't add conditions on the Metadata from this model
							array('Document.path NOT LIKE' => '%.jpg'),
							array('Document.path NOT LIKE' => '%.jpeg'),
							array('Document.path NOT LIKE' => '%.gif'),
							array('Document.path NOT LIKE' => '%.png')
						),
						'contain' => array('Metadata')
					),
				)
			),
			'fields' => array(
				'document_document' => array(
					'label' => __d('caracole_documents', 'Document', true),
					'type' => 'document',
					'plain' => true
				),
				'filename' => array(
					'label' => __d('caracole_documents', 'Filename', true),
					'plain' => true
				),
				'filesize' => array(
					'label' => __d('caracole_documents', 'Filesize', true),
					'plain' => true
				),
				'ext' => array(
					'label' => __d('caracole_documents', 'Extension', true),
					'plain' => true
				),
				'mimetype' => array(
					'label' => __d('caracole_documents', 'Mimetype', true),
					'plain' => true
				)
			),
			'views' => array('upload', 'index', 'edit')
		);

		//	Validation
		$this->validate = array(
			'upload' => array(
				'noPhpFiles' => array(
					'rule' => array('isNotOfType', array('php', 'php3', 'php4', 'php5', 'php6', 'phtml')),
					'message' => __d('caracole_documents', 'Upload of php files is strictly forbidden for security reasons.', true)
				)
			),
		);

		parent::__construct($id, $table, $ds);
	}

	/**
	 *	afterSave
	 *	Callback used to automatically save custom fields in the Metadata table when saving a Document
	 **/
	function afterSave($created) {
		// Getting custom fields
		$customFields = array_diff_key($this->data[$this->alias], $this->schema());
		if (empty($customFields)) return true;

		foreach($customFields as $customField => $customValue) {
			$metadata = $this->Metadata->find('first', array(
				'conditions' => array('Metadata.document_id' => $this->id, 'Metadata.name' => $customField),
				'fields' => array('Metadata.id'),
				'contain' => false
			));
			$this->Metadata->create($metadata);
			// Filling default values when creating
			if (empty($metadata)) {
				$this->Metadata->set(array('document_id' => $this->id, 'name' => $customField));
			}
			// Updating the value field
			$this->Metadata->set('value', $customValue);
			$this->Metadata->save();
		}
	}


	/**
	 *	afterFind
	 *	Callback used to copy all Metadata directly into the main model data.
	 *
	 *	Results are presented in either of two ways
	 *		$primary = true
	 *
	 *
	 *	$results will be formatted like :
	 *	For direct calls on Document model ($primary = false)
	 *	[
	 		[Document] =>
							[id]
	 *		[Metadata] =>
	 *						[id]
	 *		[Version] =>
	 *						[id]
	 *						[Metadata] =>
	 *										[id]
	 *
	 *	]
	 *	For binded calls on model that have a belongsTo Document :
	 *	[
	 *		[Model] =>
	 *					[id]
	 *					[Document] =>
	 *									[Metadata] =>
	 *													[id]
	 *									[Version] =>
	 *													[id]
	 *													[Metadata] => [id]
	 *	]
	 *
	 *	Calls that matches several items will have them in a numeric indexed array
	 **/
	 function afterFind($results, $primary = false)  {
		// Fast fail if no document on a direct request
		if (empty($primary) && isset($results['id']) && empty($results['id'])) return null;
		// Fast fail if no Document nor Metadatas
		if (!Set::matches('/Metadata', $results)) return $results;

		// Results are presented in either of two ways :
		// $primary : 	In a numeric-indexed array where each key contains a Document, Metadata and possible Version key
		//				This array will have only one key for a find('first')
		// !primary:	a/ In a document array (id, parent_id, path, etc) and a Metadata and possible Version subarrays. For belongsTo
		//				b/ In a numeric-indexed array of a/ arrays, for HABTM


		// We guess if we have a list of items or only one item


		// Direct request on Document
		if (!empty($primary)) {
			// Copying metadatas back into main model
			foreach($results as $i => &$item) {
				// Merging metadatas
				$this->__mergeMetadata($item['Metadata'], $item[$this->alias]);
				unset($item['Metadata']);
				// Merging version metadatas
				if (!empty($item['Version'])) {
					foreach($item['Version'] as &$version) {
						if (empty($version['Metadata'])) continue;
						$this->__mergeMetadata($version['Metadata'], $version);
						unset($version['Metadata']);
					}
				}
			}
		} else {
			// Multiples results
			if (!empty($results[0])) {
				foreach($results as $i => &$item) {
					// Merging metadatas
					$this->__mergeMetadata($item['Metadata'], $item);
					unset($item['Metadata']);
					// Merging version metadatas
					if (!empty($item['Version'])) {
						foreach($item['Version'] as &$version) {
							if (empty($version['Metadata'])) continue;
							$this->__mergeMetadata($version['Metadata'], $version);
							unset($version['Metadata']);
						}
						// To be more readable, we will move the Version key to the end of the array
						$_Version = $item['Version'];
						unset($item['Version']);
						$item['Version'] = $_Version;
					}
				}
			} else {
				// Merging metadatas
				$this->__mergeMetadata($results['Metadata'], $results);
				unset($results['Metadata']);
				// Merging version metadatas
				if (!empty($results['Version'])) {
					foreach($results['Version'] as &$version) {
						if (empty($version['Metadata'])) continue;
						$this->__mergeMetadata($version['Metadata'], $version);
						unset($version['Metadata']);
					}
					// To be more readable, we will move the Version key to the end of the array
					$_Version = $results['Version'];
					unset($results['Version']);
					$results['Version'] = $_Version;
				}
			}
		}

		return $results;
	}

	/**
	 *	__mergeMetadata
	 *	Convenient method to merge a whole [Metadata] data array into an other array where name => value.
	 *	Metadata is formed like : [0] => ([name] => 'width', 'value' => '800')
	 *	And will be merged into $destination so $destination[width] = 800
	 *	Used in the afterFind() call on various placed.
	 *
	 *	@param	$metadatas		[Metadata] array block
	 *	@param	$destination	Main array where to merge the metadatas
	 **/
	 function __mergeMetadata($metadatas, &$destination) {
		foreach($metadatas as $metadata) {
			$destination[$metadata['name']] = $metadata['value'];
		}
	}





	/**
	 *	beforeDelete
	 *	When deleting a Document, we need to delete all the files on disk
	 **/
	function beforeDelete($cascade = true) {
		// Reading content to get the data and then removing the files from disk
		$this->read();
		$this->deleteFile();
		return true;
	}





	/**
	 * loadIDFromUpload
	 * Applied to a model instance, will convert every upload to corresponding document id in the model data
	 * Will also fill the validationErrors array with errors if some are found
	 * You should call this method after every model->create() when dealing with models that have related documents
	 *
	 * If no upload is defined but a document id is set, we grab the document data from the database. This is useful to
	 * display the uploaded file after a form validation error.
	 *
	 * You can define validation rules for your uploads directly in your main model by adding them to the upload_document_*foo*
	 * You can use the isOfType, isNotOfType, maxFilesize and minFilesize methods
	 **/
	function loadIDFromUpload(&$model) {
		// Getting the list of document fields by checking the model associated and finding those of our type (Document/Image)
		$documentFields = array();
		foreach($model->belongsTo as $belongsToName => $belongsToOptions) {
			if ($belongsToOptions['className']!=$this->alias) continue;
			$documentFields[$belongsToName] = $belongsToOptions['foreignKey'];
		}
		// Fast stop if no related documents
		if (empty($documentFields)) return;

		// Keeping a reference to the default validate array because we will mess with it
		$_validate = $this->validate;

		// Will now insert every uploaded field in our database and their newly created id in our main model
		foreach($documentFields as $documentName => $fieldName) {
			// Fast fail if the document key is not submitted
			if (!array_key_exists($fieldName, $model->data[$model->alias])) continue;

			// Is an upload set for this field ?
			$uploadFieldName = 'upload_'.$fieldName;
			$isUploading = (array_key_exists($uploadFieldName, $model->data[$model->alias]) && !empty($model->data[$model->alias][$uploadFieldName]['name']));

			// If not uploading
			if (!$isUploading) {
				// If the Document data is already set, we're all good
				if (!empty($model->data[$documentName])) continue;
				// Otherwise, we try to get document data from the database
				if (!empty($model->data[$model->alias][$fieldName])) {
					$documentData = $this->find('first', array('conditions' => array($this->alias.'.id' => $model->data[$model->alias][$fieldName])));
					if (!empty($documentData)) $model->data[$documentName] = $documentData[$this->alias];
					continue;
				}
			}

			// Copying main model validation rules to Document model
			if (array_key_exists($uploadFieldName, $model->validate)) {
				$validationRule = $model->validate[$uploadFieldName];
				$this->validate['upload'] = array_merge($this->validate['upload'], $validationRule);
			}

			// Getting upload data (and inserting document in database)
			$uploadData = $this->insert($model->data[$model->alias][$uploadFieldName]);

			// Error while inserting, we add a validation error and clear the fields
			if (!empty($uploadData['error'])) {
				$model->validationErrors[$uploadFieldName] = $uploadData['error'];
				$model->data[$model->alias][$fieldName] = null;
				unset($model->data[$model->alias][$uploadFieldName]);
				continue;
			}

			// Ok, so we change the initial field to the matching value
			$model->data[$model->alias][$fieldName] = $uploadData[$this->alias]['id'];
			unset($model->data[$model->alias][$uploadFieldName]);
		}

		// Reverting to default validate array
		$this->validate = $_validate;


	}

	/**
	 *	insert
	 *	Will save on disk and in the database a given uploaded filedata array
	 *	This will automatically handle validation and will return an array with an error key containing the error message
	 *	if it does not validate.
	 **/
	function insert($filedata) {
		// Upload the file
		$uploadedFile = $this->upload($filedata);
		if (!empty($uploadedFile['error'])) {
			return $uploadedFile;
		}

		// Creating the model instance
		$this->create(array($this->alias => $uploadedFile));

		// Does not validate
		if (!$this->validates()) {
			$this->deleteFile();
			return array('error' => $this->validationErrors);
		}

		// Saving
		$this->data = $this->save();

		return $this->data;

	}

	/**
	 *	upload
	 *	Will save a file upload in the app directory structure and return an array of its data, ready to be save in the DB
	 *	In case of upload error, will return an array with the error
	 *
	 *	@param	$filedata	array :
	 *							- error : The id of the upload error
	 **/
	function upload($filedata) {
		// Error if no file to upload
		if (empty($filedata)) {
			return array('error' => __d('caracole_documents', 'No file uploaded', true));
		}
		// Error during upload
		if (!empty($filedata['error'])) {
			return array('error' => $this->__getUploadError($filedata['error']));
		}
		// Getting data from the file
		$data = $this->extractMetadata($filedata);

		// Validating the upload (type, filesize, etc)
		if (!$this->validatesUpload($data)) {
			return array('error' => $this->uploadValidationError);
		}

		// Creating the complete filepath
		$uploadPath = $this->getOrCreateUploadDirectory(date('Y'), date('m'), date('d'));
		if ($uploadPath===false) {
			return array('error' => __d('caracole_documents', 'Unable to create upload directory. Check that your files/ directory is writable.', true));
		}

		$data['id'] = String::uuid();
		$data['parent_id'] = 0;
		$filepath = $uploadPath.$data['id'].'.'.$data['ext'];

		// Moving the uploaded file to its upload directory
		if (!$this->__moveUploadedFile($filedata['tmp_name'], $filepath)) {
			return array('error' => __d('caracole_documents', 'Unable to write file on disk. Check that your files/ directory is writable.', true));
		}

		$data['path'] = $filepath;

		// Returning complete upload data
		return $data;
	}

	/**
	 *	validatesUpload
	 *	Validate the filedata against the validate rules defined in the 'upload' key of the model
	 **/
	function validatesUpload($data) {
		// Fast success if no rules
		if (empty($this->validate['upload'])) return true;

		// Checking each rule, stopping whenever one fails
		foreach($this->validate['upload'] as $ruleName => $ruleOptions) {
			// Getting rule method to call and arguments
			$ruleMethod = array_shift($ruleOptions['rule']);
			$ruleArguments = array_merge(array($data),$ruleOptions['rule']);

			// Continue if validates
			if (call_user_func_array(array(&$this, $ruleMethod), $ruleArguments)) continue;

			// Setting custom error message if such is defined
			if (!empty($ruleOptions['message'])) $this->uploadValidationError = $ruleOptions['message'];
			return false;
		}

		return true;
	}

	/**
	 *	deleteFile
	 *	Will delete from disk the file corresponding to the current data.
	 **/
	function deleteFile() {
		if (file_exists($this->data[$this->alias]['path'])) {
			unlink($this->data[$this->alias]['path']);
		}
	}




	//				VALIDATE RULES


	/**
	 *	isOfType
	 *	Validates a file upload if it is in the list of allowed extensions.
	 *	We just do a simple check on the file extension, we do not check deeper for the real mimetype as mimetypes can be
	 *	easily spoofed and their support is not universal
	 **/
	function isOfType($data, $types) {
		// Multiple types
		if (!is_array($types)) $types = array($types);

		// Fails if not in the list of allowed extensions
		if (!in_array($data['ext'], $types)) {
			$this->uploadValidationError = sprintf(__d('caracole_documents', 'This file extension, %1$s, is not allowed.', true), $data['ext']);
			return false;
		}

		return true;

	}

	/**
	 *	isNotOfType
	 *	Validates that the uploaded file is NOT one of the passed extensions
	 **/
	function isNotOfType($data, $types) {
		// Error if one of the specified types
		if ($this->isOfType($data, $types)) {
			$this->uploadValidationError = sprintf(__d('caracole_documents', 'This file extension, %1$s, is forbidden.', true), $data['ext']);
			return false;
		}
		return true;
	}

	/**
	 *	maxFileSize
	 *	Validates that the specified file is not bigger than a specified size (in bytes)
	 **/
	function maxFileSize($data, $maxFileSize) {
		return $data['filesize']<=$maxFileSize;
	}

	/**
	 *	minFileSize
	 *	Validates that the specified file is bigger than a specified size (in bytes)
	 **/
	function minFileSize($data, $minFileSize) {
		return $data['filesize']>=$minFileSize;
	}






	//			UPLOAD SPECIFIC METHODS


	/**
	 *	getOrCreateUploadDirectory
	 *	Will create a tree structure of directories for each arguments passed.
	 *	root is webroot/files.
	 *	The default structure is YYYY/MM/DD
	 *	Will only return it if already existing, otherwise will also create it
	 **/
	function getOrCreateUploadDirectory() {
		$structure = func_get_args();
		// Constructing the whole structure, starting at files/
		$fullPath = 'files/';
		$tmpUmask = umask(0); // Little trick to correctly set the chmod
		foreach($structure as $path) {
			// Don't allow for suspicious structure
			if ($path=='..') return false;

			// Creating directory if not existing
			$fullPath.=$path.'/';
			if (!file_exists(WWW_ROOT.$fullPath) && !mkdir(WWW_ROOT.$fullPath, 0777)) {
				umask($tmpUmask);
				return false;
			}
		}
		umask($tmpUmask);
		return $fullPath;
	}


	/**
	 *	__getUploadError
	 *	Returns a textual representation of an upload error
	 **/
	function __getUploadError($errorCode) {
		$maxFileSize = ini_get('upload_max_filesize');
		switch($errorCode) {
			case UPLOAD_ERR_INI_SIZE :
				return sprintf(__d('caracole_documents', 'The uploaded file is too big. Please choose a file smaller that %1$s.', true), $maxFileSize);
			break;
			case UPLOAD_ERR_FORM_SIZE :
				return sprintf(__d('caracole_documents', 'The uploaded file is too big. Please choose a file smaller that %1$s.', true), $maxFileSize);
			break;
			case UPLOAD_ERR_PARTIAL :
				return __d('caracole_documents', 'The file was only partially uploaded. Please, try again.', true);
			break;
			case UPLOAD_ERR_NO_FILE :
				return __d('caracole_documents', "The file you have selected is empty. Please, choose an other file.", true);
			break;
			case UPLOAD_ERR_NO_TMP_DIR :
				return __d('caracole_documents', 'Temporary directory is not available. Please make sure that it exists on the server.', true);
			break;
			case UPLOAD_ERR_CANT_WRITE :
				return __d('caracole_documents', 'Unable to write file on disk.', true);
			break;
			default:
				return __d('caracole_documents', 'Unable to upload your file. Please report the problem.', true);
			break;
		}
	}

	/**
	 *	__moveUploadedFile
	 *	Will try to move the file. This is basically a wrapper for move_uploaded_file.
	 *	When in testing mode, move_uploaded_file will refuse to move the file because it is not a valid file upload.
	 *	In such cases, we will use a classic rename() call. We don't want to use the rename() call as default because it won't
	 *	offer the same security protection as move_uploaded_file will (for example, one could rename a config.php file out of place)
	 **/
	function __moveUploadedFile($source, $destination) {
		if (!Configure::read('Documents.isTesting')) {
			return move_uploaded_file($source, $destination);
		} else {
			return rename($source, $destination);
		}

	}


	//		METADATA EXTRACTING FROM FILEDATA METHODS

	/**
	 *	extractMetadata
	 *	Will extract in an array form various metadata of the file. At the very least it will contain filename, filesize,
	 *	ext and mimetype
	 *	This method will be overriden in sub classes
	 **/
	function extractMetadata($filedata) {
		return array(
			'filename' => $this->filename($filedata),
			'filesize' => $this->filesize($filedata),
			'ext' => $this->ext($filedata),
			'mimetype' => $this->mimetype($filedata),
		);
	}


	/**
	 * filename
	 * Returns the upload original filename, without extension
	 **/
	function filename($filedata) {
		return substr($filedata['name'], 0, strrpos($filedata['name'], '.'));
	}

	/**
	 *	filesize
	 *	Returns the uploaded file size
	 *	Notice : May cause problems with files larger than 2GB. As we are talking about upload here, this shouldn't worry
	 *	us, tho.
	 **/
	function filesize($filedata) {
		return file_exists($filedata['tmp_name']) ? filesize($filedata['tmp_name']) : false;
	}

	/**
	 *	ext
	 *	Returns the extension of a file upload
	 **/
    function ext($filedata) {
        return strtolower(trim(strrchr($filedata['name'], '.'), '.'));
    }

	/**
	 *	mimetype
	 *	Returns a file mimetype.
	 *	It will try to find the exact mimetype using the file command, or if it can't will guess it
	 *	from the extensions
	 *
	 *	mime_content_type is deprecated and the FileInfo functions returns strange data that we can't really trust
	 *
	 *
	 **/
	function mimetype($filedata) {
		$filepath = $filedata['tmp_name'];
		// Check only existing files
		if (!file_exists($filepath) || !is_readable($filepath)) return false;

		// Trying to run file from the filesystem
		if (function_exists('exec')) {
			$mimeType = exec("/usr/bin/file -i -b $filepath");
			if (!empty($mimeType)) return $mimeType;
		}

		// Trying to get mimetype from images
		$imageData = @getimagesize($filepath);
		if (!empty($imageData['mime'])) {
			return $imageData['mime'];
		}

		// Reverting to guessing the mimetype from a known list
		 // Thanks to MilesJ Uploader plugin : http://milesj.me/resources/logs/uploader-plugin
		static $mimeTypes = array(
			// Images
			'bmp'	=> 'image/bmp',
			'gif'	=> 'image/gif',
			'jpe'	=> 'image/jpeg',
			'jpg'	=> 'image/jpeg',
			'jpeg'	=> 'image/jpeg',
			'pjpeg'	=> 'image/pjpeg',
			'svg'	=> 'image/svg+xml',
			'svgz'	=> 'image/svg+xml',
			'tif'	=> 'image/tiff',
			'tiff'	=> 'image/tiff',
			'ico'	=> 'image/vnd.microsoft.icon',
			'png'	=> 'image/png',
			'xpng'	=> 'image/x-png',
			// Text
			'txt' 	=> 'text/plain',
			'asc' 	=> 'text/plain',
			'css' 	=> 'text/css',
			'csv'	=> 'text/csv',
			'htm' 	=> 'text/html',
			'html' 	=> 'text/html',
			'stm' 	=> 'text/html',
			'rtf' 	=> 'text/rtf',
			'rtx' 	=> 'text/richtext',
			'sgm' 	=> 'text/sgml',
			'sgml' 	=> 'text/sgml',
			'tsv' 	=> 'text/tab-separated-values',
			'tpl' 	=> 'text/template',
			'xml' 	=> 'text/xml',
			'js'	=> 'text/javascript',
			'xhtml'	=> 'application/xhtml+xml',
			'xht'	=> 'application/xhtml+xml',
			'json'	=> 'application/json',
			// Archive
			'gz'	=> 'application/x-gzip',
			'gtar'	=> 'application/x-gtar',
			'z'		=> 'application/x-compress',
			'tgz'	=> 'application/x-compressed',
			'zip'	=> 'application/zip',
			'rar'	=> 'application/x-rar-compressed',
			'rev'	=> 'application/x-rar-compressed',
			'tar'	=> 'application/x-tar',
			// Audio
			'aif' 	=> 'audio/x-aiff',
			'aifc' 	=> 'audio/x-aiff',
			'aiff' 	=> 'audio/x-aiff',
			'au' 	=> 'audio/basic',
			'kar' 	=> 'audio/midi',
			'mid' 	=> 'audio/midi',
			'midi' 	=> 'audio/midi',
			'mp2' 	=> 'audio/mpeg',
			'mp3' 	=> 'audio/mpeg',
			'mpga' 	=> 'audio/mpeg',
			'ra' 	=> 'audio/x-realaudio',
			'ram' 	=> 'audio/x-pn-realaudio',
			'rm' 	=> 'audio/x-pn-realaudio',
			'rpm' 	=> 'audio/x-pn-realaudio-plugin',
			'snd' 	=> 'audio/basic',
			'tsi' 	=> 'audio/TSP-audio',
			'wav' 	=> 'audio/x-wav',
			'wma'	=> 'audio/x-ms-wma',
			// Video
			'flv' 	=> 'video/x-flv',
			'fli' 	=> 'video/x-fli',
			'avi' 	=> 'video/x-msvideo',
			'qt' 	=> 'video/quicktime',
			'mov' 	=> 'video/quicktime',
			'movie' => 'video/x-sgi-movie',
			'mp2' 	=> 'video/mpeg',
			'mpa' 	=> 'video/mpeg',
			'mpv2' 	=> 'video/mpeg',
			'mpe' 	=> 'video/mpeg',
			'mpeg' 	=> 'video/mpeg',
			'mpg' 	=> 'video/mpeg',
			'mp4'	=> 'video/mp4',
			'viv' 	=> 'video/vnd.vivo',
			'vivo' 	=> 'video/vnd.vivo',
			'wmv'	=> 'video/x-ms-wmv',
			// Applications
			'js'	=> 'application/x-javascript',
			'xlc' 	=> 'application/vnd.ms-excel',
			'xll' 	=> 'application/vnd.ms-excel',
			'xlm' 	=> 'application/vnd.ms-excel',
			'xls' 	=> 'application/vnd.ms-excel',
			'xlw' 	=> 'application/vnd.ms-excel',
			'doc'	=> 'application/msword',
			'dot'	=> 'application/msword',
			'pdf' 	=> 'application/pdf',
			'psd' 	=> 'image/vnd.adobe.photoshop',
			'ai' 	=> 'application/postscript',
			'eps' 	=> 'application/postscript',
			'ps' 	=> 'application/postscript'
		);
		$ext = $this->ext($filedata);
		return array_key_exists($ext, $mimeTypes) ? $mimeTypes[$ext] : false;
	}






}
