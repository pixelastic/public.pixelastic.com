<?php
/**
 *	DocumentHelper
 *	Used to display all document related informations.
 *	Will be extended by ImageHelper to specifically deal with images
 **/
class DocumentHelper extends AppHelper {
	// Helpers
	var $helpers = array('Session', 'Caracole.Fastcode');


	/**
	 *	input
	 *	Displays a input element to fit in a form structure.
	 *	Will provide both a classical file upload functionnality as well as markup for improved asynchronous upload.
	 *	Will also guess correct value for the field and if found will display a preview of it
	 **/
	function input($fieldName, $options) {
		// Prepending the upload field with and upload_
		if(strpos($fieldName, '.')) {
			list($modelName, $fieldName) = explode('.', $fieldName);
			$uploadFieldName = $modelName.'.upload_'.$fieldName;
		} else $uploadFieldName = 'upload_'.$fieldName;

		// Getting the document data
		list(,$modelName) = explode('_', $fieldName);
		$modelName = Inflector::camelize($modelName);
		$documentData = empty($this->data[$modelName]['id']) ? null : $this->data[$modelName];

		// Adding a class of 'finished' to the element if a value is set
		$valueClasses = array('upload');
		if (!empty($documentData)) $valueClasses[] = 'finished';

		// Between label and input
		$between =
			// Wrapping all the upload element in one element
			$this->Fastcode->div(null, array('class' => implode(' ', $valueClasses)))

				// Value element, on top, to display a preview of the document
				.'<div class="value">'
					.'<div class="preview">'.$this->preview($documentData).'</div>'
					.$this->Fastcode->link(__d('caracole_documents', 'Remove', true), '#remove', array('class' => 'remove button action'))
				.'</div>'

				// Wrapping the classic input file in a jsOff div, with the hidden document id
				.'<div class="classicUpload jsOff">'
					.$this->Fastcode->input($fieldName, array('type' => 'hidden', 'secureValue' => false))
		;


		// We will add a massive after element to handle all the dynamic upload part
		$after =
				'</div>'
				.'<div class="dynamicUpload jsOn">'
					// Placeholder for the SWFUpload element. The inner span will be swapped to a flash object.
					// We still need the wrapper to correctly catch hover and click events in Javascript
					// We also need this element to always be visible, even during upload. Otherwise, flash events won't fire
					.'<div class="swfUploadWrapper"><span></span></div>'
					// We add the current sessionId as well as the current userAgent hash.
					// This will be sent to the SWFUpload to prevent cake from thinking we're trying to hijack him
					.'<input type="hidden" name="sessionId_'.$fieldName.'" value="'.$this->Session->id().'">'
					//.'<input type="hidden" name="userAgent_'.$fieldName.'" value="'.$this->Session->read('Config.userAgent').'">'
					// We add the max allowed size of the file
					.'<input type="hidden" name="sizeLimit_'.$fieldName.'" value="'.CaracoleNumber::toMachineSize(ini_get('post_max_size')).' B">'

					// Selecting a file
					.'<div class="initial">'
						.$this->Fastcode->button(__d('caracole_documents', 'Select a file', true), array('icon' => 'Document_upload', 'class' => 'buttonUpload'))
					.'</div>'

					// Progressing
					.'<div class="progress">
						<div class="progressBar">
							<div class="barHolder"><div class="bar"></div></div>
							<div class="percent"></div>
						</div>'
						// Cancel upload
						.$this->Fastcode->link(__d('caracole_documents', 'Cancel', true), '#cancel', array('class' => 'cancel button action'))
					.'</div>'
				.'</div>'
			// Closing the wrapping element
			.'</div>'

		;

		// We overwrite default options
		$options = Set::merge($options, array(
			'type' => 'file',
			'between' => $between,
			'after' => $after
		));



		return $this->Fastcode->input($uploadFieldName, $options);

	}

	/**
	 *	preview
	 *	Display a preview link of the file.
	 *	Default rendering is just a link, opening in a new window and prepended with an extension icon
	 **/
	function preview($data, $options = array()) {
		// Fast fail, need data
		if (empty($data)) return false;

		// Default options
		$options = array_merge(array(
			'target' => '_blank'
		), $options);

		// Define link label
		if (empty($options['label'])) {
			$label = sprintf(
				__d('caracole_documents', '%1$s.%2$s (%3$s)', true),
				trim($data['filename']),
				$data['ext'],
				CaracoleNumber::toHumanSize($data['filesize'])
			);
		} else {
			$label = $options['label'];
			unset($options['label']);
		}

		// Define url
		if (empty($options['url'])) {
			$url = $this->url($data);
		} else {
			$url = $options['url'];
			unset($options['url']);
		}

		// Define icon
		if (!isset($options['icon'])) {
			$options['icon'] = $this->getIcon($data['ext']);
		}


		return $this->Fastcode->link($label, $url, $options);
	}

	/**
	 *	url
	 *	Returns the url to access the file
	 *	A file path files/foo/bar/baz/uuid.jpg will be translated to files/foo/bar/baz/uuid/name.jpg
	 *
	 *	@param	$data	The Document data array
	 *	@param	$options	Options to pass
	 *						- filename and ext : The filename and extension that should be used in the generated link
	 **/
	function url($data, $options = array()) {
		// Default options
		$options = array_merge(
			array(
				'filename' => $data['filename'],
				'ext' => $data['ext']
			)
		);
		$structure = explode('/', $data['path']);
		array_pop($structure);

		return $this->Fastcode->shardUrl(
			sprintf('%1$s/%2$s/%3$s.%4$s', implode('/', $structure), $data['id'], Inflector::slug($options['filename']), $options['ext']),
			$data['id']
		);
	}

	/**
	 *	icon
	 *	Maps a specified extension to a corresponding extension
	 **/
	function getIcon($ext) {
		$mapIcons = array(
			'application'	=> array('exe', 'msi'),
			'archive'		=> array('zip', 'rar', 'tar', 'tgz', 'gz'),
			'audio'			=> array('wav', 'wma', 'mp3', 'ogg', 'mid'),
			'flash'			=> array('swf', 'fla', 'as'),
			'font'			=> array('eot', 'ttf', 'otf', 'woff'),
			'html'			=> array('html', 'htm', 'xml', 'xhtml'),
			'image' 		=> array('bmp', 'gif', 'jpe', 'jpg', 'jpeg', 'ico', 'png', 'psd', 'ai', 'eps', 'ps'),
			'pdf'			=> array('pdf'),
			'php' 			=> array('php', 'php3', 'php4', 'php5', 'php6', 'phtml'),
			'powerpoint'	=> array('ppt', 'pptx', 'pps', 'ppsx'),
			'script'		=> array('css', 'js', 'json'),
			'spreadsheet'	=> array('xls', 'csv'),
			'text' 			=> array('txt', 'doc', 'docx', 'odt', 'rtf', 'abw'),
			'video'			=> array('flv', 'avi', 'divx', 'mpg', 'mpeg', 'mp4', 'ram', 'qt', 'mov', 'wmv'),
		);

		// Finding the correct icon based on the extension
		$icon = 'Document';
		foreach($mapIcons as $category => $exts) {
			if (!in_array($ext, $exts)) continue;
			$icon = $category;
			break;
		}

		return $icon;


	}





}
