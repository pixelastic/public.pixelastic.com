<?php
/**
 *	Image
 *	Extends the Document model but adds methods to specificly handle image uploads
 *
 **/
App::import('Model', 'CaracoleDocuments.Document');
class Image extends Document {
	var $useTable = 'documents';
	// hasMany
	var $hasMany = array(
		'Metadata' => array(
			'className' => 'CaracoleDocuments.Metadata',
			'foreignKey' => 'document_id',
			'dependent' => true
		),
		'Version' => array(
			'className' => 'CaracoleDocuments.Image',
			'foreignKey' => 'parent_id',
			'dependent' => true
		)
	);

	/**
	 *	__construct
	 *	Extends the Document construct to add special admin settings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		// We replace the admin headers with a preview and a full filename
		$this->adminSettings['index']['headers'] = array(
			'Image.preview' => array(
				'label' => __d('caracole_documents', 'Preview', true),
				'order' => false
			),
			'Image.filename' => array(
				'label' => __d('caracole_documents', 'Name', true),
				'order' => false
			)
		);

		// Setting custom pagination to only grab images
		$this->adminSettings['index']['paginate'] = array(
			'Image' => array(
				'order' => array('Image.created' => 'DESC'),
				'conditions' => array(
					'Image.parent_id' => 0,
					// Trick to find only images. We can't add conditions on Metadata from here
					'OR' => array(
						array('Image.path LIKE' => '%.jpg'),
						array('Image.path LIKE' => '%.jpeg'),
						array('Image.path LIKE' => '%.gif'),
						array('Image.path LIKE' => '%.png')
					)

				),
				'contain' => array('Metadata', 'Version' => 'Metadata')
			)
		);

		// Replacing the document_document field with and image_image one when editing
		$tmpArray = array(
			'image_image' => array(
				'label' => __d('caracole_documents', 'Image', true),
				'type' => 'image',
				'plain' => true
			)
		);
		unset($this->adminSettings['fields']['document_document']);
		$this->adminSettings['fields'] = Set::merge($tmpArray, $this->adminSettings['fields']);

		// Setting image special metadata
		$this->adminSettings['fields'] = Set::merge(
			$this->adminSettings['fields'],
			array(
				'width' => array(
					'label' => __d('caracole_documents', 'Width', true),
					'plain' => true
				),
				'height' => array(
					'label' => __d('caracole_documents', 'Height', true),
					'plain' => true
				)
			)
		);

		// We also add some validation rules to only accept image files
		$this->validate = Set::merge(
			$this->validate,
			array(
				'upload' => array(
					'onlyImages' => array(
						'rule' => array('isOfType', array('jpg', 'jpeg', 'png', 'bmp', 'gif', 'wbmp', 'tiff')),
						'message' => __d('caracole_documents', 'You can only upload images files in this field.', true)
					)
				)
			)
		);

	}

	//		METADATA EXTRACTING FROM FILEDATA METHODS

	/**
	 *	extractMetadata
	 *	Extends the base metadata extractor method but will also add image specific metadatas such as :
	 *	- width and height
	 **/
	function extractMetadata($filedata) {
		return array_merge(
			parent::extractMetadata($filedata),
			array(
				'width' => $this->width($filedata),
				'height' => $this->height($filedata),
			)
		);
	}

	/**
	 *	width
	 *	Gets image width
	 **/
	function width($filedata) {
		$dim = getimagesize($filedata['tmp_name']);
		return $dim[0];
	}

	/**
	 *	height
	 *	Gets image height
	 **/
	function height($filedata) {
		$dim = getimagesize($filedata['tmp_name']);
		return $dim[1];
	}



}
