<?php
/**
 *	CaracoleRequest
 *	Will hold missing methods to deal with requests
 **/
class CaracoleRequest extends Object {

	/**
	 *	getAllHeaders
	 *	Return the sent headers in a full list in human readable form
	 *	Will try to emulate the getallheaders() function if not defined
	 *
	 *	@return	string	Each key representing a header type and each value it's corresponding value
	 */
	function getAllHeaders() {
		// Getting headers through getAllHeaders or using custom method
		if (function_exists('getallheaders')) {
			$headers = getallheaders();
		} else {
			$headers = array();
			foreach ($_SERVER as $name => &$value) {
				if (substr($name, 0, 5)!= 'HTTP_') continue;
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}

		// Saving IP
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$headers['IP'] = $_SERVER['REMOTE_ADDR'];
		}

		// Converting as a text
		$return = '';
		foreach($headers as $key => &$value) {
			$return.= sprintf('%1$s : %2$s', $key, $value)."\n";
		}
		return $return;
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
		if (empty($token)) $token = $url;
		return Configure::read('SiteUrl.s'.(ord(substr(md5($token),0,1))%3+1)).$url;
	}




}
?>