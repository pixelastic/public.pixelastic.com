<?php
/**
 *	CaracoleImage
 *	This class is a utility class for everything that has to deal with images.
 *	Its primary goal was to have an external class to be able to calculate correct resize dimensions based on various inputs.
 **/
class CaracoleImage extends Object {

	/**
	  *	create
	  *	Creates a new blank canvas to write image content on it
	  **/
	 function create($width, $height) {
		$result = imagecreatetruecolor($width, $height);
		imagealphablending($result, false); //	Setting png transparency
		return $result;
	 }

	 /**
	 *	directUrl
	 *	Returns a direct url to the file. This will not get processed and will only return a url where to find the file
	 *	Url is of model : (/|http://domain.com)/files/2010/09/09/uuid/filename.ext
	 *	@param	$data	Image data array
	 *	@param	$options	Options :
	 *							- full (default true) : If set to true, the image subdomain will be prepend
	 *							- filename : Default to the Image filename. Name underwhich the file will be saved
	 *							- ext : Default to the Image ext. Extension of the returned file
	 **/
	function directUrl($data, $options = array()) {
		// Default options
		$options = array_merge(
			array(
				'full' => true,
				'filename' => $data['filename'],
				'ext' => $data['ext']
			),
			$options
		);
		// Exploding the path to get the structure
		$structure = explode('/', $data['path']);
		array_pop($structure);
		return CaracoleRequest::shardUrl(sprintf(
			'%1$s/%2$s/%3$s.%4$s',
			implode('/', $structure),
			$data['id'],
			Inflector::slug($options['filename']),
			$options['ext']
		), $data['id']);
	 }

	 /**
	 *	getExistingVersion
	 *	Given an image data array and a set of options, will return a version mathcing the criterias.
	 *	Note that the $data array should contain the whole list of available version because this method do not
	 *	make any calls to the DB
	 **/
	function getExistingVersion($data, $options) {
		// Fast fail if no versions
		if (empty($data['Version'])) return false;
		// We keep only the options that can act as filter
		$options = array_intersect_key($options, array_fill_keys(array('width', 'height', 'resize'), null));

		// Looping through each version...
		foreach($data['Version'] as &$version) {
			// ... checking each filter ...
			foreach($options as $key => $value) {
				//... stopping if not matching
				if ($version[$key]!=$value) {
					$found = false;
					break;
				}
				$found = true;
			}
			// Not found in this version, check next version
			if (empty($found)) continue;
			return $version;
		}

		return false;

	}


	/**
	  *	getImageContent
	  *	Returns the content of an image created with CaracoleImage::source. We will generate the image and grab the content
	  *	using the ob_functions buffer.
	  *
	  *	@param	$image	The image handler to get content from
	  *	@param	$data	The Image data array
	  *	@options	$options	The resize option array
	  *							- quality : The quality of the resize (percent between 0 and 100) (jpg files only)
	  **/
	 function getImageContent($image, $data, $options) {
		// Default options
		$options = array_merge(array('quality' => 100), $options);

		// We save the alpha channel
		imagesavealpha($image, true);

		//	Grabbing image information in buffer
		ob_start();
		switch($data['ext']) {
			case 'gif' : imagegif($image, null, $options['quality']);	break;
			case 'png' : imagepng($image, null, 1);	break;
			case 'jpg' : imagejpeg($image, null, $options['quality']);	break;
			case 'bmp' : imagejpeg($image, null, $options['quality']);	break;
			default :	return false;	break;
		}
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	 }


	 /**
	  *	getResizeContent
	  *	Wrapper for the main resize method. Given an image data array and resize options, will return the content of
	  *	the resized image. It will not write on disk, you'll have to do it yourself
	  *
	  *	@param	$data	The Image data array
	  *	@param	$options	Options to pass to the resize
	  *						- width / height : The width / height we need the image to be resized to. If the value is between 0 and 1 it will be
	  *							used as a ratio of the original file. For example, for an image of width 800, if you set a resize width of 0.4, it will
	  *							be used as 320
	  *						- resize : The type of resize
	  *									- relative (default) : The image will be resized in a way that keep its width/height ratio
	  *										Whatever the resize width/height given, it will make sure that tha general shape of the image is kept.
	  *										In this mode, the image could not be enlarged.
	  *										Think of the resize width/height as boundaries that will enclose the resized image
	  *									- forced : The image will be resized to exactly the specified dimensions, no matter what.
	  *									- square : In this mode, the resulting image will always be a square. We will crop the original image to get the biggest
	  *										available square, then resize it to the specified dimensions.
	  *										If you pass two different dimensions, we will use the biggest. The image could not be enlarged in this mode, but if the
 	  *										result is too small, it will be padded with a plain color (padColor option)
	  *						- padColor : The color to use to pad a too small image in resize squared mode. Can accept either and array(r,g,,b) or an hexadecimal string
	  **/
	 function getResizeContent($data, $options = array()) {
		// Dispatch to inner methods
		switch($options['resize']) {
			// Forced dimensions
			case 'forced':
				return CaracoleImage::getResizeContentForced($data, $options);
			break;
			// Square resize
			case 'square':
				return CaracoleImage::getResizeContentSquare($data, $options);
			break;
			// Default (relative)
			default:
				return CaracoleImage::getResizeContentRelative($data, $options);
			break;
		}
	 }

	 /**
	  *	getResizeContentForced
	  *	Will resize the image to the specified dimensions, no matter what. It will not handle ratio and will allow for
	  *	bigger images than the original
	  **/
	 function getResizeContentForced($data, $options) {
		// The original image
		$source = CaracoleImage::source($data);
		//	Creating final image
		$result = CaracoleImage::create($options['width'], $options['height']);
		// We resize the result with the given dimensions
		imagecopyresampled($result, $source, 0, 0, 0, 0, $options['width'], $options['height'], $data['width'], $data['height']);
		// We get the image content
		$content = CaracoleImage::getImageContent($result, $data, $options);
		//	Empty memory
		imagedestroy($source);
		imagedestroy($result);
		// Returning the content
		return $content;
	 }

	 /**
	  *	getResizeContentRelative
	  *	Will resize the given image by making sure that the width/height ratio is kept. Think of the given dimensions as the max
	  *	dimensions the resulting image will fit in.
	  **/
	 function getResizeContentRelative($data, $options) {
		// Forcing a relative resize
		$options = array_merge($options, array('resize' => 'relative'));
		// Getting resize dimensions
		$options = array_merge($options, CaracoleImage::getResizeDimensions($options));
		// Resizing to those dimensions
		return CaracoleImage::getResizeContentForced($data, $options);
	 }

	 /**
	  *	getResizeContentSquare
	  *	Will resize the specified image so that the result is a square. This will crop some parts of the image to avoid a distortion effect
	  *	This yields very good results with thumbnails.
	  **/
	 function getResizeContentSquare($data, $options) {
		// Forcing a square resize
		$options = array_merge($options, array('resize' => 'square'));
		// Getting resize dimensions
		$options = array_merge($options, CaracoleImage::getResizeDimensions($options));

		// The original image
		$source = CaracoleImage::source($data);
		// Our final image will have this as final size
		$finalSquareSize = $options['width'];

		// We get the biggest available square in the original file
		if ($data['width']>=$data['height']) {
			// Landscape
			$bigSquareSize = $data['height'];
			$padX = round(($data['width'] - $data['height']) / 2);
			$padY = 0;
		} else {
			// Portrait
			$bigSquareSize = $data['width'];
			$padX = 0;
			$padY = round(($data['height'] - $data['width']) / 2);
		}

		//	Creating big square image from source
		$bigSquare = CaracoleImage::create($bigSquareSize, $bigSquareSize);
		//imagecopyresampled($result, $source, 0, 0, 0, 0, $options['width'], $options['height'], $data['width'], $data['height']);
		imagecopyresampled($bigSquare, $source, 0, 0, $padX, $padY, $bigSquareSize, $bigSquareSize, $bigSquareSize, $bigSquareSize);

		// We now resize the square to the specified dimensions
		$finalSquare = CaracoleImage::create($finalSquareSize, $finalSquareSize);
		imagecopyresampled($finalSquare, $bigSquare, 0, 0, 0, 0, $finalSquareSize, $finalSquareSize, $bigSquareSize, $bigSquareSize);

		// We get the image content
		$content = CaracoleImage::getImageContent($finalSquare, $data, $options);
		//	Empty memory
		imagedestroy($source);
		imagedestroy($bigSquare);
		imagedestroy($finalSquare);
		// Returning the content
		return $content;
	 }


	 /**
	 *	getResizeDimensions
	 *	Given an input file and some options, it will calculate the resize dimensions
	 *	@param	$data		The original data array of the image. Should at least contain width and height keys
	 *	@param	$options	Options to pass to the resize
	 *						- width / height : The width / height we need the image to be resized to. If the value is between 0 and 1 it will be
	 *							used as a ratio of the original file. For example, for an image of width 800, if you set a resize width of 0.4, it will
	 *							be used as 320
	 *						- resize : The type of resize
	 *									- relative (default) : The image will be resized in a way that keep its width/height ratio
	 *										Whatever the resize width/height given, it will make sure that tha general shape of the image is kept.
	 *										In this mode, the image could not be enlarged.
	 *										Think of the resize width/height as boundaries that will enclose the resized image
	 *									- forced : The image will be resized to exactly the specified dimensions, no matter what.
	 *									- square : In this mode, the resulting image will always be a square. We will crop the original image to get the biggest
	 *										available square, then resize it to the specified dimensions.
	 *										If you pass two different dimensions, we will use the biggest. The image could not be enlarged in this mode, but if the
	 *										result is too small, it will be padded with a plain color (padColor option)
	 **/
	 function getResizeDimensions($data, $options = array()) {
		// If we resize as a square, we make sure that both height and width are set
		if (!empty($options['resize']) && $options['resize']=='square') {
			if (empty($options['height']) && !empty($options['width'])) $options['height'] = $options['width'];
			if (empty($options['width']) && !empty($options['height'])) $options['width'] = $options['height'];
		}

		// Default options
		$options = array_merge(
			array(
				'resize' => 'relative',
				'height' => $data['height'],
				'width' => $data['width']
			),
			$options
		);

		// If the resize is forced, we do return the specified dimensions
		if ($options['resize'] == 'forced') {
			return array('width' => $options['width'], 'height' => $options['height']);
		}

		// We calculate ratios if resize options are between 0 and 1
		if ($options['width']<=1) {
			$options['width'] = round($data['width'] * $options['width']);
		}
		if ($options['height']<=1) {
			$options['height'] = round($data['height'] * $options['height']);
		}

		// We prevent enlargment
		if ($options['width']>$data['width']) {
			$options['width'] = $data['width'];
		}
		if ($options['height']>$data['height']) {
			$options['height'] = $data['height'];
		}
		// We first calculate the square resize dimensions as they are easier to get
		if ($options['resize']=='square') {
			$maxDimensions = max($options['width'], $options['height']);
			return array('width' => $maxDimensions, 'height' => $maxDimensions);
		}

		// We return the dimensions right now if they are the same as original
		if ($data['width']==$options['width'] && $data['height']==$options['height']) {
			return array('width' => $options['width'], 'height' => $options['height']);
		}

		// We now get the resize dimensions in landscape mode
		if ($data['width']>=$data['height']) {
			// Setting new width and applying same ratio to the height
			$resizedWidth = $options['width'];
			$ratio = round($data['width']/$options['width'],2);
			$resizedHeight = round($data['height'] / $ratio);
			// Resized height is still too big, we have to reduce it and reduce the width accordingly
			if ($resizedHeight>$options['height']) {
				// Recalculating ratio and applying it back to the width
				$ratio = round($resizedHeight / $options['height'],2);
				$resizedHeight = $options['height'];
				$resizedWidth = round($resizedWidth / $ratio);
			}
		} else {
			// In portrait mode, setting new height and applying same ratio to width
			$resizedHeight = $options['height'];
			$ratio = round($data['height']/$options['height'],2);
			$resizedWidth = round($data['width'] / $ratio);
			// Resized width is still too big, we have to reduce it and reduce the height accordingly
			if ($resizedWidth>$options['width']) {
				// Recalculating ratio and applying it back to the height
				$ratio = round($resizedWidth / $options['width'],2);
				$resizedWidth = $options['width'];
				$resizedHeight = round($resizedHeight / $ratio);
			}
		}

		return array('width' => $resizedWidth, 'height' => $resizedHeight);
	 }



	 /**
	  *	source
	  *	Create an image handler from an Image data array by reading the file in path
	  **/
	 function source($data) {
		switch($data['ext']) {
			// GIF
			case 'gif' : return imagecreatefromgif($data['path']); break;
			// PNG
			case 'png' : return imagecreatefrompng($data['path']); break;
			// JPEG
			case 'jpg' : case 'jpeg' : return imagecreatefromjpeg($data['path']); break;
			// BMP
			case 'bmp' : return imagecreatefromwbmp($data['path']); break;
			// None
			default : return false; break;
		}
	 }


	 /**
	  *	url
	  *	Given an image data array and a set of options, will return the corresponding url
	  *	@param	$data	Image data array
	 *	@param	$options	Options :
	 *							- resize : Type of resize : relative (default), forced or square
	 *							- width : Width of the resize
	 *							- height : Height of the resize
	 *							- full (default true) : If set to true, the image subdomain will be prepend
	 *							- filename : Default to the Image filename. Name underwhich the file will be saved
	 *							- ext : Default to the Image ext. Extension of the returned file
	  **/
	 function url($data, $options = array()) {
		// We set default options
		$options = array_merge(
			array(
				'resize' => 'relative',
				'filename' => $data['filename'],
				'ext' => $data['ext']
			),
			$options
		);

		// We set the correct resize dimensions
		$options = array_merge($options, CaracoleImage::getResizeDimensions($data, $options));

		// Removing resize dimensions equal to original
		if ($options['width']==$data['width']) unset($options['width']);
		if ($options['height']==$data['height']) unset($options['height']);
		// Removing the resize if no dimensions set
		if (empty($options['width']) && empty($options['height'])) unset($options['resize']);

		// If no processing needed, we will return a simple link
		$processingOptions = array_intersect_key($options, array_fill_keys(array('width', 'height', 'resize'), null));
		if (empty($processingOptions)) {
			return CaracoleImage::directUrl($data, $options);
		}

		// We search the existing version to find one that match
		if ($existingVersion = CaracoleImage::getExistingVersion($data, $options)) {
			return CaracoleImage::directUrl($existingVersion, $options);
		}

		// The image do not already exists, so we will return a processing link
		$options['id'] = $data['id'];
		$processData = base64_encode(serialize($options));
		// As an additionnal measure, we replace each / character with a _ to not mess with the routes
		$processData = str_replace('/', '_', $processData);

		return array(
			'admin' => false,
			'plugin' => 'caracole_documents',
			'controller' => 'images',
			'action' => 'process',
			'processData' => $processData,
			'filename' => Inflector::slug($options['filename']).'.'.$options['ext']
		);
	 }













}
?>