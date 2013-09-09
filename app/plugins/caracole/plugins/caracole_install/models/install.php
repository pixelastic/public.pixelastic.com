<?php
/**
 *	Install
 *	Dummy model to be used when installing Caracole
 **/
class Install extends AppModel {
	// No table to be used
	var $useTable = false;


	/**
	 *	validateBaseConfig
	 *	Test that the basic configuration options are correctly set. Includes sensitive informations changed from default
	 *	-eg. Security salt, mail adress, database connection-.
	 *	Also ensure that directories are writabled and database is running
	 **/
	function validateBaseConfig() {
		$baseConfig = array(
			'Site.id' => array(
				'rule' => array('__validateNotDefault', 'yoursite'),
				'message' => __d('caracole_install', "You have to change the Site.id key to make sure that multiple Caracole installation will play nice with each other.", true)
			),
			'Email.default' => array(
				'rule' => array('__validateNotDefault', 'contact@yoursite.com'),
				'message' => __d('caracole_install', 'You have to change the value of Email.default to receive any mail that Caracole could send.', true)
			),
			'Security.salt' => array(
				'rule' => array('__validateNotDefault', '.r2tZT?E.IQiYG)r1y-$b0A-m]k5D`sk&t.Cvn8rJ.p-H0N,IlFd6v!Q|[F@:~%3'),
				'message' => __d('caracole_install', 'You have to change the Security.salt value to avoid rainbow table attacks.', true)
			),
			'Security.cipherSeed' => array(
				'rule' => array('__validateNotDefault', '481516234276859309657453542496749683645'),
				'message' => __d('caracole_install', 'You have to change the Security.cipherSeed value to allow for better cookie encryption.', true)
			),
			'Database' => array(
				'rule' => array('__validateDatabase'),
				'message' => __d('caracole_install', 'Caracole is unable to connect to your database.', true)
			),
			'tmp' => array(
				'rule' => array('__validateWritableDirectory', TMP),
				'message' => __d('caracole_install', 'Your tmp directory is not writable. It is used to store all kind of cached data.', true)
			),
			'config' => array(
				'rule' => array('__validateWritableDirectory', APP.'config'),
				'message' => __d('caracole_install', 'Your app/config directory is not writable. It is used to create a dummy file, used to check if Caracole is installed.', true)
			),
			'files' => array(
				'rule' => array('__validateWritableDirectory', WWW_ROOT.'files'),
				'message' => __d('caracole_install', 'Your webroot/files directory is not writable. It is used to save uploaded documents.', true)
			),
			'img' => array(
				'rule' => array('__validateWritableDirectory', WWW_ROOT.'img'.DS.'caracole'),
				'message' => __d('caracole_install', 'Your webroot/img/caracole directory is not writable. It is used to save the icon sprite.', true)
			),
			'css' => array(
				'rule' => array('__validateWritableDirectory', WWW_ROOT.'css'),
				'message' => __d('caracole_install', 'Your webroot/css/ directory is not writable. It is used to save the icon sprite CSS rules.', true)
			),
			'iconCss' => array(
				'rule' => array('__validateWritableFile', WWW_ROOT.'css'.DS.'icons.css'),
				'message' => __d('caracole_install', 'Your webroot/css/icons.css file is not writable. It is used to store the CSS Sprite icon rules.', true)
			),
			'cssPacked' => array(
				'rule' => array('__validateWritableDirectory', WWW_ROOT.'css'.DS.'packed'),
				'message' => __d('caracole_install', 'Your webroot/css/packed directory is not writable. It is used to save compressed CSS files.', true)
			),
			'jsPacked' => array(
				'rule' => array('__validateWritableDirectory', WWW_ROOT.'js'.DS.'packed'),
				'message' => __d('caracole_install', 'Your webroot/js/packed directory is not writable. It is used to save compressed Javascript files.', true)
			),
		);

		// We set config values to the model to try and validate it
		$values = array_fill_keys(array_keys($baseConfig), '0');
		$this->create($values);
		$this->validate = $baseConfig;
		return $this->validates();
	}


	/**
	 *	__validateNotDefault
	 *	Validate the field if the value is different from the default one
	 **/
	function __validateNotDefault($check, $default) {
		return Configure::read(key($check))!=$default;
	}

	/**
	 *	__validateDatabase
	 *	Validate the database connection
	 **/
	function __validateDatabase($check) {
		if (!class_exists('ConnectionManager')) {
			require LIBS . 'model' . DS . 'connection_manager.php';
		}
		$db = ConnectionManager::getInstance();
		@$connected = $db->getDataSource('default');
		return $connected->isConnected();
	}

	/**
	 *	__validateWritableDirectory
	 *	Validate that the directory is writable
	 **/
	function __validateWritableDirectory($check, $directory) {
		return is_writable($directory);
	}

	/**
	 *	__validateWritableFile
	 *	Validate that the file is writable
	 **/
	function __validateWritableFile($check, $file) {
		$file = &new File($file);
		return $file->writable();
	}

}
