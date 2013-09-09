<?php
/**
 *	Error layout. Contains only a basic message display styling
 **/
// Default CSS files
$this->Packer->css(array('CaracoleErrors.error'));
//<!DOCTYPE><html><head>
echo $this->element('html_head', array('plugin' => 'caracole'));
//</head>

	// Title
	if (!empty($title_for_layout)) echo $this->Html->tag('h2', $title_for_layout);

	// Content
	echo $this->Fastcode->div($content_for_layout, array('class' => 'content'));
?>
</body>
</html>