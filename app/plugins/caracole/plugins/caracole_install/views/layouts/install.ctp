<?php
// Css files for the admin panel
$this->Packer->css(array(
	'CaracoleInstall.layout',		//	Install special styles
));

//<!DOCTYPE><html><head>
echo $this->element('html_head', array('plugin' => 'caracole'));
//</head>

// Body element
echo $this->Html->tag('body', null);
?>

<!-- Global -->
<div class="content container">
	<h2><?php echo $title_for_layout; ?></h2>

	<?php
		// Page content
		echo $content_for_layout;
	?>

</div>
<?php
	// Writing scripts that should be at the end of the page, like Javascript
	$this->Packer->bottom();
?>
</body>
</html>