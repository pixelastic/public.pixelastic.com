<?php
	// i18n fields for javascript file. Should be contained in an array named $jsI18n
	$jsI18n = array(
		'domain' => 'formDocument',
		'keys' => array(
			'fileTooBig' 		=> __d('caracole_documents', "The file you've selected is too big to be uploaded. Please select a smaller file.", true),
			'fileEmpty' 		=> __d('caracole_documents', 'The file you selected is empty. Make sure it is not a shortcut or a link.', true),
			'fileInvalid' 		=> __d('caracole_documents', "The file you have selected is not allowed. Please select a file of the following extensions : ", true),
			'fileError' 		=> __d('caracole_documents', 'File error #', true),
			'unableToConnect' 	=> __d('caracole_documents', 'Unable to connect to the upload server. Please make sure that your internet connection is active. Reloading the page may remove the issue.', true),
			'serverResponseUnknown' => __d('caracole_documents', 'The response we got from the server is not JSON. There seems to be a misconfiguration of your server.', true),
			'error'				=> __d('caracole_documents', 'Error', true),
			'imageFiles'		=> __d('caracole_documents', 'Image files', true),
		)
	);
?>
