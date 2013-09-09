<?php
// Css files for the admin panel
$this->Packer->css(array(
	'Caracole.blueprintGrid',		//  Using Blueprint grids to layout content
	'Caracole.admin/common',		//  Common styles used accross the admin panel
	'Caracole.admin/layout',		//	Display of the admin panel
	'Caracole.admin/form',			//	Form styling in the admin panel
	'Caracole.admin/index',			//	Item list styling of the admin panel
));
// Css for Js
$this->Packer->css(array(
	'Caracole.admin/js',
), array('js' => true));

// Js files used only in the admin panel
$this->Packer->js(array(
	'Caracole.admin/init',				// Layout scripts
));
//<!DOCTYPE><html><head>
echo $this->element('html_head', array('plugin' => 'caracole'));
//</head>

?>

<!-- Global -->
<div class="container global">
	<!-- Content -->
	<div class="mainContent span-18 last">
		<!-- Title -->
		<div class="mainTitle">
			<?php echo $this->Html->tag('h2', $title_for_layout); ?>
			<?php
				// Main toolbar
				if (!empty($mainToolbar)) {
					echo $this->element('admin/toolbar_main', array('plugin' => 'caracole', 'mainToolbar' => $mainToolbar));
				}
			?>
		</div>

		<!-- Inner Content -->
		<div class="innerContent">
			<?php
				// Flash message
				echo $this->Session->flash();
				// Page content
				echo $content_for_layout;
			?>
		</div>
		<!-- /Inner Content -->
	</div>
	<!-- /Content -->
</div>
<?php
	// Writing scripts that should be at the end of the page, like Javascript
	$this->Packer->bottom();
?>
</body>
</html>