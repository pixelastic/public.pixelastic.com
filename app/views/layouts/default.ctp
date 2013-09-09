<?php
// Default CSS files
$this->Packer->css(array(
	//'Caracole.blueprintGrid',	// Blueprint
	'layout',					//  Styling the main layout
	'common',					//	Styling common elements
	'style'						//	Styling special pages
));

// Default Javascript files
$this->Packer->js(array(
	'spaceinvader',		//	Creating space invaders
	'Caracole.vendors/jquery-slimbox2',	// Lightbox clone
	'init',				// Layout
	'init-contact',		// Contact page
	'analytics'			//	Google analytics script
));
//<!DOCTYPE><html><head>
echo $this->element('html_head', array('plugin' => 'caracole'));
//</head></body>

?>

	<div class="global container">
		<div class="masthead">
			<h1><?php echo $this->Fastcode->link(
				Configure::read('Site.name').'<span></span>',
				Configure::read('SiteUrl.lang'),
				array('escape' => false)
			); ?></h1>
			<ul class="menu tablecell">
				<li><?php
					echo $this->Fastcode->link(
						__('Blog', true).'<span>'.__("bits of code", true).'</span>',
						array('plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'index'),
						array('escape' => false, 'title' => false)
					);
					?>
				</li>
				<li><?php
					echo $this->Fastcode->link(
						__('Work', true).'<span>'.__("missions completed", true).'</span>',
						array('plugin' => null, 'controller' => 'works', 'action' => 'index'),
						array('escape' => false, 'title' => false)
					); ?>
				</li>
				<li><?php
					echo $this->Fastcode->link(
						__('About', true).'<span>'.__("more about me", true).'</span>',
						array('plugin' => 'caracole_pages', 'controller' => 'pages', 'action' => 'view', 'pageSlug' => 'about'),
						array('escape' => false, 'title' => false)
					);
					?>
				</li>
				<li><?php
					echo $this->Fastcode->link(
						__('Contact', true).'<span>'.__("come and say Hello", true).'</span>',
						array('plugin' => 'caracole_contacts', 'controller' => 'contacts', 'action' => 'add'),
						array('escape' => false, 'title' => false)
					);
					?>
				</li>
			</ul>
			<?php
				// Adding a space invader
				echo $this->element('spaceinvader', array('width' => '6', 'height' => '7'));
			?>
			<blockquote id="caracoleQuote">
				<cite><?php __("You can clip our wings but we will always remember what it was like to fly."); ?></cite>
			</blockquote>
		</div>
		<div class="mainContent container">
			<?php
				echo $content_for_layout;
			?>
		</div>

	</div>

<?php
	echo $this->Packer->bottom();
?>
</body>
</html>
