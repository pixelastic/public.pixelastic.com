<?php
// Css files for the admin panel
$this->Packer->css(array(
	'Caracole.blueprintGrid',		//  Using Blueprint grids to layout content
	'Caracole.admin/common',		//  Common styles used accross the admin panel
	'Caracole.admin/layout',		//	Display of the admin panel
	'Caracole.admin/form',			//	Form styling in the admin panel
	'Caracole.admin/index',			//	Item list styling of the admin panel

	'Caracole.admin/js',			// 	Javascript-specific rules
	'Caracole.admin/tinymce',		//  tinyMCE main styling


	'admin',						//	Project special admin styles
));
// Css for IE
$this->Packer->css(array('Caracole.admin/ie'), array('ie' => true));



// Js files used only in the admin panel
$this->Packer->js(array(
	'Caracole.vendors/tinymce_gz',		// TinyMCE gzipped
	'Caracole.vendors/jquery-tinymce',	//jQuery tinyMCE plugin
	'Caracole.admin/tinymce',			// TinyMCE configuration

	'Caracole.admin/dialog',			// Extends jqueryUI dialog methods
	'Caracole.vendors/jquery-slimbox2',	// Lightbox clone

	'Caracole.admin/init',				// Main scripts
	'Caracole.admin/init-index',		// Index scripts
	'Caracole.admin/init-edit',			// Add/edit scripts
	'Caracole.admin/jquery-formHabtm',	// Scripts for the habtm selection
	'Caracole.admin/init-reorder',		// Reorder scripts

	'admin',							// App admin special configuration
));
//<!DOCTYPE><html><head>
echo $this->element('html_head', array(
	'plugin' => 'caracole',
	'pageCssId' => empty($pageCssId) ? null : $pageCssId
));
//</head>
?>

<!-- Header -->
<div class="masthead">
	<div class="container">
		<div class="span-6">
			<?php
				echo $this->Html->tag('h1',
					$this->Fastcode->link(
						'<span></span>'.Configure::read('Site.name'),
						Configure::read('SiteUrl.admin'),
						array('escape' => false)
					)
				);
			?>
		</div>
		<div class="span-6">
			<?php
				echo $this->Fastcode->link(
					__d('caracole', 'View website', true),
					Configure::read('SiteUrl.default'),
					array(
						'target' => '_blank',
						'class' => 'viewWebsite'
					)
				);
			?>
		</div>
		<div class="span-6 prepend-6 last">
			<div class="loginInfos">
				<?php
					if (!empty($activeUser)) {
						// Displaying the welcoming message
						echo sprintf(
							__d('caracole', 'Welcome %1$s. %2$s', true),
							'<strong>'.$activeUser['User']['nickname'].'</strong>',
							$this->Fastcode->link(__d('caracole', 'Log out', true), $urlLogout)
						);
					}
			?>
			</div>
			<div class="changeLang">
			<?php
				// Switching lang
				//echo $this->Fastcode->linkSwitchLang('fre');
				//echo $this->Fastcode->linkSwitchLang('eng');
			?>
			</div>
		</div>
	</div>
</div>
<!-- /Header -->


<!-- Global -->
<div class="container global">
	<!-- Sidebar -->
	<div id="sidebar" class="sidebar span-6">
<?php
			echo $this->element('admin/menu', array('plugin' => 'caracole'));
?>
	</div>
	<!-- /Sidebar -->


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

		<?php
			// Secondary toolbar
			if (!empty($secondaryToolbar)) {
				echo $this->element('admin/toolbar_secondary', array('plugin' => 'caracole', 'secondaryToolbar' => $secondaryToolbar));
			}
		?>

		<!-- Inner Content -->
		<div class="innerContent">
			<?php
				// Flash message
				if ($this->Session->check('Message.flash')) {
					$flashMessage = $this->Session->read('Message.flash');
					echo $this->element($flashMessage['element'], array('message' => $this->Fastcode->html($flashMessage['message'])));
					$this->Session->delete('Message.flash');
				}
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