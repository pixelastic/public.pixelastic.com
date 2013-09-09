<?php
/**
 *	CaracoleHtml
 *	This class extends the cake HtmlHelper but adds more convenient methods to it
 **/
class CaracoleHtmlHelper extends AppHelper {
	// Helpers
	var $helpers = array(
		'Html', 'Text',
		'Caracole.Fastcode'
	);

	/**
	 *	attribute
	 *	Returns a stripped out version of the text to be displayed in an html attribute field
	 *
	 *	@text	string	$text	The text to convert as attribute value
	 **/
	function attribute($text) {
		return $this->text($text);
	}

	/**
	 *	convertLinks
	 *	Converts every http:// into a clickable link and every user@domain.com mail to a non-spammable email
	 **/
	function convertLinks($text) {
		// We start by "protecting" each existing link
		if (!function_exists('__callbackConvertLinksProtect')) {
			function __callbackConvertLinksProtect($args) {
				return '{{'.base64_encode($args[0]).'}}';
			}
		}
		$text = preg_replace_callback('/<a (.+?)>(.+?)<\/a>/','__callbackConvertLinksProtect',$text);

		// We convert each found url and add them protected
		if (!function_exists('__callbackConvertLinksAdd')) {
			function __callbackConvertLinksAdd($args) {
				return __callbackConvertLinksProtect(array(sprintf('<a href="%1$s">%1$s</a>%2$s', $args[1], empty($args[3]) ? null : $args[3])));
			}
		}
        $text = preg_replace_callback('/(http(s?):\/\/.+?)([ \\n\\r<])/','__callbackConvertLinksAdd',$text);
        $text = preg_replace_callback('/^(http(s?):\/\/.+?)/','__callbackConvertLinksAdd',$text);
        $text = preg_replace_callback('/(http(s?):\/\/.+?)$/','__callbackConvertLinksAdd',$text);

		// We unprotect each url
		if (!function_exists('__callbackConvertLinksUnprotect')) {
			function __callbackConvertLinksUnprotect($args) {
				return base64_decode($args[1]);
			}
		}
        $text = preg_replace_callback('/{{([a-zA-Z0-9+\/=]+?)}}/','__callbackConvertLinksUnprotect',$text);

        return $text;
	}


	/**
	 *	convertMails
	 *	Will convert any user@domain.com into a full user at domain dot com string
	 *	Will also wrap any non-text content in <span> so CSS and JS can make it user-readable
	 **/
	function convertMails($text) {
		// Method to replace mails in plain text
		if (!function_exists('__callbackConvertMails')) {
			function __callbackConvertMails($args) {
				$mail = $args[0];
				// Forbidden characters
				$forbidden = array(
					'@' => __d('caracole', 'at', true),
					'-' => __d('caracole', 'hyphen', true),
					'_' => __d('caracole', 'underscore', true),
					'.' => __d('caracole', 'dot', true),
				);
				// Replacing them with spans
				foreach($forbidden as $char => $word) {
					$mail = str_replace($char, sprintf('<span class="mailChar" title="%1$s"> %2$s </span>', $char, $word), $mail);
				}
				return sprintf('<span class="protectedMail">%1$s</span>', $mail);
			}
		}
		// regexp to catch a mail. Taken from cake/libs/validation.php
		$mailRegexp = '/[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9][-a-z0-9]*\.)*(?:[a-z0-9][-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,4}|museum|travel)/i';
		$text = preg_replace_callback($mailRegexp,'__callbackConvertMails',$text);
		return $text;
	}

	/**
	 *	div
	 *	Returns a div
	 **/
	function div($text, $options = array()) {
		return $this->Html->div(null, $text, $options)."\n";
	}

	/**
	 *	html
	 *	Returns the specified text as html text. New lines will be transformed in <br /> and html text will be escaped
	 *	http:// links will be converted to <a> links
	 *
	 **/
	function html($text) {
		// Sanitizing html text
		$text = Sanitize::html($text);
		// Adding <br />
		$text = nl2br($text);
		// Transform http:// into links
		$text = $this->convertLinks($text);

		return $text;
	}

	/**
	 *	link
	 *	Wrapper around the HtmlHelper link method.
	 *	Used as a faster way to define link, it will automatically set the title element.
	 *
	 *	@param	string	$name		The label of the link
	 *	@param	string	$url		Href link destination.
	 *	@param	array	$options	Options to pass to the link. Default link options are supported as well as
	 *								- icon : The name of the icon to prepend
	 *								- wrap : The name of the html tag that may wrap the label
	 *
	 *	@return 	string	A <a> tag
	 */
	function link($label, $url = '/', $options = array()) {
		//	If no title specified, we use the label
		if (!isset($options['title'])) {
			$options['title'] = $this->attribute($label);
		}

		// If an icon is specified, we prepend it
		if (!empty($options['icon'])) {
			$label = $this->Fastcode->icon($options['icon']).$label;
			$options['escape'] = false;
			unset($options['icon']);
		}

		// If a wrapper is set, we wrap the label inside it
		if (!empty($options['wrap'])) {
			$label = $this->Html->tag($options['wrap'], $label);
			$options['escape'] = false;
			unset($options['wrap']);
		}

		// We return the link
		return $this->Html->link($label, $url, $options);
	}

	/**
	 *	message
	 *	Displaying a message in a pre-styled message box
	 *
	 *	@param	string	$text	Text to display in the message
	 *	@param	string	$type	Type of message box : information, error, notice, success
	 **/
	function message($text, $type = 'information', $options = array()) {
		// Default options
		$options = array_merge(array('class' => ''), $options);
		// Parsing option class and adding the message class
		$class = array_merge(
			array('message', $type),
			array_values(array_filter(explode(' ', $options['class'])))
		);
		$options['class'] = implode(' ', $class);

		return $this->div($text, $options);
	}

	/**
	 *	p
	 *	Wrapper to HtmlHelper::para with more convenient arguments
	 *
	 *	@param	text	Text to display
	 *	@param	array	Options for the element.
	 *	@return string	<p> element containing the passed string
	 **/
	function p($text = null, $options = array()) {
		if (is_string($options)) $options = array('class' => $options);
		return $this->Html->para(null, $text, $options)."\n";
	}


	/**
	 *	prepareHTML
	 *	Will prepare a pseudo-html text into a real html text. Every http:// and user@domain.com
	 *  will be replaced by a real link
	 **/
	function prepareHTML($text) {
		return $this->convertMails($this->convertLinks($text));
	}


	/**
	 *	shardUrl
	 *	Returns a full url path to a file by dispatching it on one of our static domains.
	 *	Using a static domain allow us to
	 *		- provide cookieless resources
	 *		- improve parallel download
	 *	This method makes sure that we always use the same static domain for a given resource
	 *
	 *	@param	$url	The base url of the document
	 *	@param	$token	A unique token, used to determine the static domain to use. Default to the url
	 **/
	function shardUrl($url, $token = null) {
		return CaracoleRequest::shardUrl($url, $token);
	}

	/**
	 *	rss
	 *	Define the list of rss feeds to add to the current page. We first get the existing list, that we merge with
	 *	the list given in parameter
	 **/
	function rss($feeds = array()) {
		$View = &ClassRegistry::init('View');
		$rssFeed = (array_key_exists('rssFeed', $View->viewVars)) ? $View->viewVars['rssFeed'] : array();
		$rssFeed= $feeds+$rssFeed;
		$View->set('rssFeed', $rssFeed);
	}


	/**
	 *	text
	 *	Returns the specified text as plain text. Will strip out any html tags but won't encode special chars nor escape
	 *	quotes
	 *
	 *	TODO : Maybe using Sanitize::html($text, true) would suffice ?
	 *
	 *	@param	string	$text	The text to convert as plain text
	 **/
	function text($text) {
		// Removing html tags
		$text = strip_tags($text);
		// Removing line breaks and tabs
		$text = preg_replace('/[\n\r\t]+/',' ',$text);
		// Removing extra spaces
		$text = preg_replace('/ {2,}/',' ',$text);
		return $text;
	}

	/**
	 *	time
	 *	Returns a formatted time string in utf8 from a timestamp. Will take care of Windows issues with utf8
	 **/
	function time($format, $date = null) {
		return CaracoleI18n::strftime($format, strtotime($date));
	}

	/**
	 *	truncate
	 *	Return the first $n characters of a given text
	 *
	 *	@param	string	$text	The text to truncate
	 *	@param	int	$n	Number of caracters to display.
	 *					- No words will be cut in the middle, the function will make sure that every word will be complete.
	 *					- Default to 200
	 *	@param	string	$suffix	What to display at the end of the text if it is longer. Default to '...'
	 *	@return string	Truncated text
	 */
	function truncate($text, $n = 200, $ending = '...') {
		return $this->Text->truncate($text, $n, array('ending' => $ending, 'exact' => false));
	}


}
