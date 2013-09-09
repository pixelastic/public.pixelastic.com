<?php
/**
 *	ImagesController
 *	Extends the DocumentsController but adds specific methods to handle images
 **/
App::import('Controller', 'CaracoleDocuments.Documents');
class ImagesController extends DocumentsController {


	/**
	 *	process
	 *	Main method used to apply any kind of processing to images.
	 *	It was created to allow resizing of images but is (or will be) extended to allow watermarking, metadata writing and
	 *	optimization
	 *
	 *	The only parameter is a base64 encoded serialized array of options created by the ImageHelper::url() method
	 *	Options are
	 *		- id : required. The image id to use as original
	 *		- resize : Kind of resize : relative, forced or square
	 *		- width / height : The required dimensions.
	 *		- quality : The quality (0-100) of the resize
	 *
	 *
	 **/
	function process($processData = null) {
		// Decoding the data
		$decode = base64_decode(str_replace('_', '/', $processData));
		// Error if not correctly formed
		if (!$options = @unserialize($decode)) return $this->cakeError('error404');
		// Error if no id set
		if (empty($options['id'])) return $this->cakeError('error404');

		// We get the matching image and stopping if none found
		$item = $this->model->find('first', array(
			'conditions' => array('Image.id' => $options['id']),
			'contain' => array('Metadata', 'Version' => array('Metadata'))
		));
		if (empty($item)) return $this->cakeError('error404');

		// We will force a resize mode if none is set but a dimension is
		if (empty($options['resize']) && (!empty($options['width']) || !empty($options['height']))) {
			$options['resize'] = 'relative';
		}

		// Resizing the image
		if (!empty($options['resize'])) {
			// Getting the resized item
			$processedItem = $this->__resize($item, $options);
			return $this->redirect(CaracoleImage::directUrl($processedItem, $options), 303);
		}

		// Going that far without rendering ? Error
		return $this->cakeError('error404');
	}

	/**
	 *	__resize
	 *	Inner method to handle all the resize stuff. Given an original item data and resize options
	 *	it will return the resized Image and create it if needed
	 **/
	function __resize($item, $options = array()) {
		// We start by getting only the resize options
		$options = array_intersect_key($options, array_fill_keys(array('width', 'height', 'resize'), null));
		// We get the resized dimensions
		$options = array_merge($options, CaracoleImage::getResizeDimensions($item['Image'], $options));

		// If dimensions are the same as the original, we will return the original
		if ($options['width']==$item['Image']['width'] && $options['height']==$item['Image']['height']) {
			return $item;
		}

		// If there is a version with these dimensions, we will return it
		if ($existingVersion = CaracoleImage::getExistingVersion($item, $options)) {
			return $existingVersion;
		}

		// We get the resized file content
		$resizedFileContent = CaracoleImage::getResizeContent($item['Image'], $options);
		// We generate a UUID
		$uuid = String::uuid();
		// We create the path where the new file will be saved
		$savePath = $this->model->getOrCreateUploadDirectory(date('Y'), date('m'), date('d')).$uuid.'.'.$item['Image']['ext'];
		// We write the file on disk
		file_put_contents($savePath, $resizedFileContent);

		// We create a new item for this version
		$this->model->create(array(
			'id' => $uuid,
			'parent_id' => $item['Image']['id'],
			'path' => $savePath,
			'filename' => $item['Image']['filename'],
			'filesize' => filesize($savePath),
			'ext' => $item['Image']['ext'],
			'mimetype' => $item['Image']['mimetype'],
			'width' => $options['width'],
			'height' => $options['height'],
			'resize' => $options['resize']
		));
		$item = $this->model->save();
		return $item['Image'];
	}










}
