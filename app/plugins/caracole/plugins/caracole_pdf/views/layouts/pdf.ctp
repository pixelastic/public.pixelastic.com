<?php
	/**
	*	Pdf layout
	*	Sends the correct headers for downloading a PDF file.
	**/
	header('Content-Type: application/x-download');
	header('Content-Length: '.strlen($content_for_layout));
	header('Content-Disposition: attachment; filename="'.$pageTitle.'.pdf"');
	header('Cache-Control: private, max-age=0, must-revalidate');
	header('Pragma: public');
	ini_set('zlib.output_compression','0');

	echo $content_for_layout;
?>