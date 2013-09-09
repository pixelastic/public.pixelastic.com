<?php
/**
 *	CaracoleConfigure
 *	This class is an extension to the cake Configure class to add methods helping in setting configuration options for Caracole
 **/
class CaracoleConfigure extends Object {

	/**
	 *	getPluginInfo
	 *	Gets the whole list of installed plugins as well as each config and routes files. Will save them in cache
	 *	for subsequent requests
	 *
	 *	@param	mixed		$type	Filter the type of informations asked. Accepted values are routes, config.
	 *	@return	array				An array of all the filepath of the specified type
	 **/
	function getPluginInfo($type = null) {
		// We first get the cached value
		$pluginInfos = CaracoleCache::read('plugins');

		// If not defined, we find them
		if (empty($pluginInfos)) {
			// We create the var we will return
			$pluginInfos = array(
				'list' 			=> array(),
				'controllers' 	=> array(),
				'models'		=> array()
			);

			// We will get the full list of plugins and search for needed files
			$pluginList = App::objects('plugin');
			sort($pluginList);
			foreach($pluginList as $pluginName) {
				// We get the plugin path
				$pluginPath = App::pluginPath($pluginName);

				// Saving the plugin main path
				$pluginInfos['list'][$pluginName] = $pluginPath;

				// Saving the plugin controllers ( [controllerName] => pluginName)
				$folder = new Folder($pluginPath.'controllers');
				$controllerList = $folder->find('.*_controller\.php');
				foreach($controllerList as $controllerFilename) {
					$pluginInfos['controllers'][str_replace('_controller.php', '', $controllerFilename)] = $pluginName;
				}

				// Saving the plugin models ( [modelName] => pluginName)
				$folder = new Folder($pluginPath.'models');
				$modelList = $folder->find('.*\.php');
				foreach($modelList as $modelFilename) {
					$pluginInfos['models'][str_replace('.php', '',Inflector::camelize($modelFilename))] = $pluginName;
				}

				// We skip the main Caracole plugin
				if ($pluginName=='Caracole') continue;

				// List of all files we need to keep in cache in the config/ folder
				$configFiles = array('config.php', 'bootstrap.php', 'routes.php', 'i18n.php', 'dump.sql');
				foreach($configFiles as &$configFile) {
					$file = new File($pluginPath.'config'.DS.$configFile);
					if (!$file->exists()) continue;
					$pluginInfos[$file->name()][$pluginName] = $file->path;
				}
			}

			// We save this value in cache
			CaracoleCache::write('plugins', $pluginInfos);
		}

		// If no type set, we return the complete set
		if (empty($type)) {
			return $pluginInfos;
		}

		// We return the specified key
		if (array_key_exists($type, $pluginInfos)) {
			return $pluginInfos[$type];
		}

		return false;
	}

	/**
	 *	loadPluginFiles
	 *	Loads a set of plugin files saved in plugins/.../config/type.php
	 *	Accepted value are : config, routes and i18n
	 **/
	function loadPluginFiles($type) {
		// We get the list of the plugin files
		$fileList = CaracoleConfigure::getPluginInfo($type);
		if (empty($fileList)) return false;
		// We load those files
		foreach($fileList as $file) {
			include($file);
		}
	}

	/**
	 *	read
	 *	Wrapper for the main Configure::read() class.
	 *	May be extended in the future, so we add this dispatcher now
	 **/
	function read($key) {
		return Configure::read($key);
	}

	/**
	 *	Write
	 *	This method is a wrapper to the Configure::write method with one addition.
	 *	If the value of a key is an array with both a dev and prod key, then only the values corresponding to the
	 *	actual environment will be set.
	 *
	 *	The check to know if we are in dev or prod is based on the server address, if it's 127.0.0.1, then it's dev.
	 *
	 *	@param	$options	array		An array of options to pass to the Configure class
	 *	@param	$overwrite	boolean		A boolean to set if the value should be overwritten if already defined. Default to
	 *									true
	 **/
	function write($options, $overwrite = true) {
		// The actual environment
		$environment = Configure::read('Caracole.environment');
		// Looping each key
		foreach($options as $configName => $configValue) {
			// We get special environment keys if defined
			if (array_key_exists($environment, $configValue)) {
				$configValue = $configValue[$environment];
			}
			// We get the existing value
			$actualConfigValue = Configure::read($configName);
			// We get the new value depending on the overwrite param. We have to merge the value if it's an array
			if (!empty($overwrite)) {
				$configValue = (is_array($configValue)) ? Set::merge($actualConfigValue, $configValue) : $configValue;
			} else {
				$configValue = (is_array($configValue)) ? Set::merge($configValue, $actualConfigValue) : $actualConfigValue;
			}
			// We set this new value
			Configure::write($configName, $configValue);
		}
	}


}
?>