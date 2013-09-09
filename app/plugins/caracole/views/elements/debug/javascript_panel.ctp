<?php
/**
 *	Javascript panel
 *	Adds a placeholder to display javascript debug information
 **/
?>
<?php
	// Clear link
	echo $this->Fastcode->link(
		__d('caracole', 'Clear debug', true),
		'#debugJavascriptPanel',
		array('class' => 'button clearDebug', 'icon' => 'debug_clear')
	);
	// Debug zone
	echo $this->Html->div(null, '', array('id' => 'debugJavascriptPanel'));
?>
