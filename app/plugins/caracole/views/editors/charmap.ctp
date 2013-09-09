<?php
	/**
	 *	Character map
	 *	Displays a list of special characters the user may be intereted in adding
	 **/

	echo $this->Html->tag('div', null, array('id' => 'charmap'));

	echo $this->Fastcode->p(__d('caracole', 'Click on the character you want to add. The first cell contains a non-breakable space.', true));

	$charList = array(
		'&nbsp;',
		'&cent;', '&euro;', '&pound;', '&yen;',
		'&copy;', '&reg;', '&trade;', '&permil;', '&micro;', '&hellip;', '&szlig;',
		'&lsaquo;', '&rsaquo;', '&laquo;', '&raquo;', '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;', '&le;', '&ge;',
		'&iexcl;','&iquest;', '&plusmn;', '&divide;', '&frasl;',  '&times;',  '&sup1;',  '&sup2;', '&sup3;', '&frac14;',
		'&frac12;','&frac34;',
		'&radic;', '&ne;',
		'&Agrave;','&Aacute;','&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&AElig;', '&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;',
		'&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&Oslash;','&OElig;','&Scaron;','&Ugrave;','&Uacute;','&Ucirc;',
		'&Uuml;','&Yacute;','&Yuml;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;',
		'&igrave;','&iacute;','&icirc;','&iuml;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&oslash;','&oelig;','&scaron;','&ugrave;','&uacute;',
		'&ucirc;','&uuml;','&yacute;','&yuml;','&Epsilon;','&Omega;','&alpha;','&beta;','&gamma;','&theta;','&lambda;','&mu;','&pi;','&phi;','&psi;','&omega;'
	);

	echo $this->Html->tag('ul', null);
		foreach($charList as $char) {
			echo $this->Html->tag('li', $char);
		}
	echo '</ul>';

	echo $this->Html->tag('div', null, array('class' => 'preview'));

	echo '</div>';
