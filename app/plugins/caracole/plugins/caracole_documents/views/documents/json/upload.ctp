<?php
/**
 *	upload.ctp
 **/
// We set the display html as a preview of the document
if (!empty($data) && empty($data['error'])) {
	$data['html'] = $this->Document->preview($data['data']['Document']);
	$this->set('data', $data);
}