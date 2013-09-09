<?php
/**
 *	upload.ctp
 **/
// We set the display html as a preview of the document
if (!empty($data) && empty($data['error'])) {
	$data['html'] = $this->Image->preview($data['data']['Image']);
	$this->set('data', $data);
}