<?php
/**
 *	RSS Layout
 *	An array named itemList should be passed to this layout. The key should be RSS keys (pubDate, title, etc)
 *	$channelData and $documentData can also be passed
 **/

// Header
echo $this->Rss->header();

// Default vars
if (empty($documentData)) $documentData = array();
if (empty($channelData)) $channelData = array();

// Document data
$documentData = Set::merge(array(
	'xmlns:dc' => 'http://purl.org/dc/elements/1.1/'
), $documentData);

// Channel data
$channelData = Set::merge(array(
	'title' => $title_for_layout,
	'link' => $this->Html->url(null, true),
	'description' => empty($metaDescription) ? Configure::read('Site.baseline') : $metaDescription
	// TODO : Set the current lang in the channel data
	//'language' => 'en-us'
), $channelData);

// Content
$content = '';
foreach ($itemList as &$item) {
    $content.= $this->Rss->item(array(), $item);
}

// Displaying content
echo $this->Rss->document(
	$documentData,
	$this->Rss->channel(
		array(),
		$channelData,
		$content
	)
);

