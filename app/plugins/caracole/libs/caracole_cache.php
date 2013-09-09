<?php
/**
 *	CaracoleCache
 *	This class is an extension of the cake Cache class. It will always write and read from a special cache config.
  **/
class CaracoleCache extends Object {

	/**
	 *	init
	 *	Define the cache config used by Caracole.
	 **/
	function init() {
		// Very short : 2 minutes
		Cache::config('VeryShort', array('engine' => 'File', 'duration'=> '+2 minutes', 'probability'=> 100, 'path' => CACHE,
			'prefix' => 'cake_', 'lock' => false, 'serialize' => true)
		);
		// Short : 2 hours
		Cache::config('Short', array('engine' => 'File', 'duration'=> '+2 hours', 'probability'=> 100, 'path' => CACHE,
			'prefix' => 'cake_', 'lock' => false, 'serialize' => true)
		);
		// Medium : 1 day
		Cache::config('Medium', array('engine' => 'File', 'duration'=> '+1 day', 'probability'=> 100, 'path' => CACHE,
			'prefix' => 'cake_', 'lock' => false, 'serialize' => true)
		);
		// Long : 1 week
		Cache::config('Long', array('engine' => 'File', 'duration'=> '+1 week', 'probability'=> 100, 'path' => CACHE,
			'prefix' => 'cake_', 'lock' => false, 'serialize' => true)
		);
		// Very long : 1 month
		Cache::config('VeryLong', array( 'engine' => 'File', 'duration'=> '+1 month', 'probability'=> 100, 'path' => CACHE,
			'prefix' => 'cake_', 'lock' => false, 'serialize' => true)
		);
		// Caracole : 15 days
		Cache::config('Caracole', array('engine' => 'File', 'duration'=> '+15 days', 'probability'=> 100, 'path' => CACHE,
			'prefix' => 'cake_', 'lock' => false, 'serialize' => true)
		);
		// Default cache is medium
		Cache::config('default', Cache::settings('Medium'));
	}

	/**
	 *	Clear
	 *	Clears the cache
	 *
	 *	@param 		boolean 	$check 	if true will check expiration, otherwise delete all
	 **/
	function clear($check = false) {
		return Cache::clear($check, 'Caracole');
	}

	/**
	 *	read
	 *	We provide here a little improvement over the cake Cache::read method. This method accepts a dotted-formatted
	 *	nested syntax of keys
	 *
	 *	@param		$key	string		The key to read. Can be in level1.level2.levelX syntax
	 *	@return		mixed				The cached value
	 **/
	function read($key) {
		// We get an array of all the nested keys
		$keys = explode('.', $key);
		// We get the root key...
		$key = array_shift($keys);
		// ...and the root value
		$value = Cache::read($key, 'Caracole');

		// And we will go down the arrays for each defined sub-key
		foreach($keys as &$key) {
			// Key does not exist, we return false
			if (!is_array($value) || !array_key_exists($key, $value)) return false;
			// We go down one more level
			$value = $value[$key];
		}

		// We return the final value
		return $value;
	}

	/**
	 *	write
	 *	We provide here a little improvement over the cake Cache::write method. This method accepts a dotted-formatted
	 *	nested syntax of keys
	 *
	 *	@param		$key	string		The key to write. Can be in level1.level2.levelX syntax
	 *	@param		$value	mixed		The value of the key.
	 *	@return		boolean				True if data is cached, false on failure
	 **/
	function write($key, $value) {
		// We get an array of all the nested keys
		$keys = explode('.', $key);

		// If there is only one key, we just update its value
		if (count($keys)==1) {
			return Cache::write($key, $value, 'Caracole');
		}

		// We get the root key...
		$key = array_shift($keys);
		// ...and the root value
		$rootValue = Cache::read($key, 'Caracole');

		// We create a nested array of the passed dotted key that we'll merge with the root value
		$mergeArray = array();
		$pointer = &$mergedArray;
		// We create the nested levels corresponding to the dotted syntax
		foreach($keys as &$nestedKey) {
			$pointer[$nestedKey] = array();
			$pointer = &$pointer[$nestedKey];
		}
		// We set the final value as the value passed
		$pointer = $value;

		// We merge this array with the existing cache value and resave them
		$rootValue = Set::merge($rootValue, $mergedArray);
		return Cache::write($key, $rootValue, 'Caracole');
	}

	/**
	 *	delete
	 *	Deletes the specified value from cache.
	 *	Accepts dotted notation. Ie domain.key will only delete key in domain
	 **/
	function delete($key) {
		// Classic syntax
		if (!strpos($key, '.')) return Cache::delete($key, 'Caracole');

		// Deleting only key in specified domain
		list($domain, $key)  = explode('.', $key);
		$domainValue = Cache::read($domain);
		// Exists, so we resave its parent minus that key
		if (isset($domainValue[$key])) {
			unset($domainValue[$key]);
			Cache::write($domain, $domainValue, 'Caracole');
		}
	}



}
?>