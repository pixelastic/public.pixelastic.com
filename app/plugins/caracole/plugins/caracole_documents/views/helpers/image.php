<?php
/**
 *	ImageHelper
 *	Extends the main DocumentHelper but adds all Image specific methods
 **/
class ImageHelper extends DocumentHelper {


	/**
	 *	image
	 *	Returns the <img> tag of the specified image
	 *	@param	$data	Array of the Image data
	 *	@param	$options	Array of options to pass
	 *						- width : Width to resize to
	 *						- height : Height to resize to
	 *						- resize : Type of resize to perform : forced / relative or square. Default is relative
	 *						- alt : Alt attribute value. An empty alt will be set if none is specified
	 *						- class, id, style just as usual
	 **/
	function image($data, $options = array()) {
		// Setting some default options
		$options = array_merge(
			array(
				'alt' => '',
			),
			$options
		);

		// We get the correct resize dimensions
		$options = array_merge($options, CaracoleImage::getResizeDimensions($data, $options));

		// We will get from the options the one for the url and the ones for the img
		$urlOptions = array_intersect_key($options, array_fill_keys(array('filename', 'width', 'height', 'resize'), null));
		$imgOptions = array_intersect_key($options, array_fill_keys(array('width', 'height', 'alt', 'class', 'id', 'style'), null));

		// We finally construct the final tag
		$url = $this->url($data, $urlOptions);
		$tag = sprintf('<img src="%1$s" %2$s/>', $url, $this->_parseAttributes($imgOptions, null, '', ' '));

		return $tag;
	}


	/**
	 *	url
	 *	Returns the url of the given image.
	 *	Options can be passed to set a width and height to resize the image. If the Image hasn't yet be processed the url
	 *	will be pointing to the process script and return the correct file. Subsequent calls will directly return the processed
	 *	url.
	 *	@param	$data	Array of the Image data
	 *	@param	$options	Array of options to pass
	 *						- width : Width to resize to
	 *						- height : Height to resize to
	 *						- resize : Type of resize to perform : forced / relative or square. Default is relative
	 *						- filename and ext : Filename and extension that should be used in the final link
	 *
	 *	If the resize dimensions are equal to the source dimensions, no resize will occur and the original file will be returned.
	 *	This does not apply for square resizes which will still be applied.
	 **/
	function url($data, $options = array()) {
		return $this->Fastcode->url(CaracoleImage::url($data, $options));


	}


	/**
	 * preview
	 * This extends the DocumentHelper::preview method for images
	 * Will display a sample of the image. This is mostly used as a debug feature or in the admin panel.
	 * You shouldn't use it as-is in your own application but instead make calls to image() and url() methods to fine-tune
	 * your display
	 *
	 * @param	$data	Image data
	 * @param	$options	Options
	 **/
	function preview($data, $options = array()) {
		// Fast fail if no data available
		if (empty($data)) return '';

		// Thumbnail
		$thumbnail = $this->image($data, array('resize' => 'square', 'width' => 96, 'height' => 96));
		// Thumbnail link
		$link = $this->Fastcode->link(
			$thumbnail,
			$this->url($data, array('width' => 950)),
			array('class' => 'lightbox', 'target' => '_blank', 'escape' => false, 'title' => $data['width'].'x'.$data['height'])
		);
		// Link to full version
		$fullLink = $this->Fastcode->link(
			sprintf('%1$s.%2$s', $data['filename'], $data['ext']),
			$this->url($data),
			array('target' => '_blank')
		);
		$preview = sprintf('<div class="imagePreview">%1$s %2$s</div>', $link, parent::preview($data));

		return $preview;

	}








}
