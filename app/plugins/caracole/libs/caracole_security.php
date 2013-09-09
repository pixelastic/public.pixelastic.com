<?php
/**
 *	CaracoleSecurity
 *	This class is an extension of the cakePHP Security class.
  **/
class CaracoleSecurity extends Object {

	/**
	 *	randomToken
	 *	Generate a random token. It will try to maximize entropy to create a random token.
	 *	It is used whenever we need to generate a random string for security purpose. This method takes some time to process,
	 *	but that's fine, it will prevent brute force attempts.
	 *	Based on recommendation found at :
	 *		http://www.php-security.org/2010/05/09/mops-submission-04-generating-unpredictable-session-ids-and-hashes/index.html
	 **/
	function randomToken($maxLength = 32) {
		$entropy = '';

		// Let's try openssl random generation function
		if (function_exists('openssl_random_pseudo_bytes')) {
			$entropy = openssl_random_pseudo_bytes(64, $strong);
			// Skipping if not strong enough
			if ($strong !== true) {
				$entropy = '';
			}
		}

		// Let's try the unix RNG
		if (is_readable('/dev/urandom')) {
			$h = fopen('/dev/urandom', 'rb');
			$entropy .= fread($h, 64);
			fclose($h);
		}

		// Let's try the window random generation function
		if (class_exists('COM')) {
			try {
				$com = new COM('CAPICOM.Utilities.1');
				$entropy.= base64_decode($com->GetRandom(64, 0));
			} catch (Exception $ex) {
			}
		}

		// Ading default uniqid/mt_rand
		$entropy.= uniqid(mt_rand(), true);

		// Finally hashing it
		$hash = hash('whirlpool', $entropy);

		return substr($hash, 0, $maxLength);
	}


}
?>