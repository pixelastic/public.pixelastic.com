<?php
/**
 *	SWFUpload panel
 *	Adds a placeholder to display SWFUpload debug information
 **/

	// Clear link
	echo $this->Fastcode->link(
		__d('caracole', 'Clear debug', true),
		'#swfUploadPanel',
		array('class' => 'button clearDebug', 'icon' => 'debug_clear')
	);
?>
<div id="swfUploadPanel"></div>