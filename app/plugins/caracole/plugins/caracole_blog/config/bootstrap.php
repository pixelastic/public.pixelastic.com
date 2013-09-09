<?php
/**
 *	Blog bootstrap
 **/
// Unless the user set this option to false (when using custom views), we load the default CSS styling
if (Configure::read('Blog.useDefaultCSS')) {
	CaracoleConfigure::write(array(
		'Packer' => array(
			'cssDefault' => array(
				'CaracoleBlog.style'
			)
		)
	));
}