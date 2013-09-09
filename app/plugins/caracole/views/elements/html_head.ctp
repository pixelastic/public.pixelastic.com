<?php
	// TODO : Maybe start using the HTML doc type <!DOCTYPE html>
	echo $this->Html->doctype('xhtml-trans');
?>

<?php
	// Conditionnal comments blocks download of other download in IE. To circumvent it, we need to add an empty one
	// More details on : http://www.phpied.com/conditional-comments-block-downloads/
	//echo '<!--[if IE]><![endif]-->';
?>

<?php
	//	Starting the HTML tag
	echo $this->Html->tag('html', null, array(
		'xmlns' => "http://www.w3.org/1999/xhtml",
		'lang' => Configure::read('Config.languageIso2'),
		'xml:lang' => Configure::read('Config.languageIso2'),
		'id' => empty($pageCssId) ? null : $pageCssId,
		'class' => 'njs'.(empty($pageCssClass) ? null : ' '.$pageCssClass)
	));
?>

<head>
	<?php
		// Charset
		echo $this->Html->charset();
	?>

	<?php
		// Identifier-URL
		echo $this->Html->meta(array('name' => 'Identifier-URL', 'content' => $this->Fastcode->url('/', true)));
	?>

	<?php
		// Meta description
		if (empty($metaDescription)) {
			$metaDescription = Configure::read('Site.baseline');
		}
		echo $this->Html->meta(array('name' => 'description', 'content' => $metaDescription));
	?>

	<?php
		// Title
		if (empty($title_for_layout)) {
			$title_for_layout = Configure::read('Site.name');
		}
		if (empty($headTitle)) {
			$headTitle = $title_for_layout;
		}
		echo $this->Html->tag('title', $headTitle);
	?>

	<?php
		// Favicon
		echo str_replace(' />', " />\n\t", $this->Html->meta('icon', $this->Fastcode->shardUrl('favicon.ico')));
	?>

	<?php
		// RSS Feeds
		if (!empty($rssFeed)) {
			foreach($rssFeed as $feedTitle => $feedUrl) {
				echo $this->Html->meta('rss', $feedUrl, array('title' => $feedTitle))."\n\t";
			}
		}
	?>

	<?php
		// Writing scripts that should be on top of the page (css)
		$this->Packer->top();
	?>

	<?php
		// We add a js class to the html element to style for Js-enabled browsers
		echo $this->Javascript->codeblock("(function(h){h.className=h.className.replace('njs', 'js')}(document.documentElement));");
	?>
</head>

<!--[if IE 6]><body class="ie ie6 ie-lt8 ie-lt9"><![endif]-->
<!--[if IE 7]><body class="ie ie7 ie-lt8 ie-lt9"><![endif]-->
<!--[if IE 8]><body class="ie ie8 ie-lt9"><![endif]-->
<!--[if IE 9]><body class="ie ie9"><![endif]-->
<!--[if !IE]><!--><body class="nie"><!--<![endif]-->
