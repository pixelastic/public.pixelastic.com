/**
 *	analytics.js
 *	This is an improved version of the Google Analytics code.
 *	Optimisations are taken from : http://mathiasbynens.be/notes/async-analytics-snippet
 **/
var _gaq = [['_setAccount', 'UA-6863870-1'], ['_setDomainName', 'www.pixelastic.com'], ['_trackPageview']];
(function(d, t) {
	var g 	= d.createElement(t),
		s 	= d.getElementsByTagName(t)[0];
	g.async = 1;
	g.src = '//www.google-analytics.com/ga.js';
	s.parentNode.insertBefore(g, s);
}(document, 'script'));