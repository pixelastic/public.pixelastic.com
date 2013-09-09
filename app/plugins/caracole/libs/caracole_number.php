<?php
/**
 *	CaracoleNumber
 *	This class is an extension of the cake Number class.
 *	It adds missing methods.
 **/
class CaracoleNumber extends Object {

	/**
	 *	toMachineSize
	 *	Converts a readable size format (like 25M) to a machine readable size, in bytes.
	 *
	 *	@param	string		$string		Filesize in string.
	 *	@return int		Filesize in bytes
	 **/
	function toMachineSize($string) {
		$val = trim($string);
		$last = strtolower($val{strlen($val)-1});
		switch($last) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
		return $val;
	}

	/**
	 *	toHumanSize
	 *	Converts a number of bytes into a human readable format
	 */
	function toHumanSize($bytes) {
		if (empty($bytes)) return false;
		$s = array('B', 'Kb', 'MB', 'GB', 'TB', 'PB');
		$e = floor(log($bytes)/log(1024));
		return sprintf('%.2f '.$s[$e], ($bytes/pow(1024, floor($e))));
	}





}
?>