<?php
/**
 *	InstallsController
 *	Controller to install Caracole, telling the user about configuration issues and creating all the needed tables
 **/
class InstallsController extends AppController {

	/**
	 *	index
	 *	Default installation screen. We will test if all the config options are correctly set
	 **/
	function index() {
		//We block access to this plugin if Caracole is already installed
		if (Configure::read('Caracole.installed')) {
			$this->cakeError('error404');
			return false;
		}

		// We will use a special layout for installing
		App::build(array('views' => CARACOLE.'plugins'.DS.'caracole_install'.DS.'views'));
		$this->layout = 'install';

		// We check if the default config options are set (security, database connection, write access, etc)
		if (!$this->Install->validateBaseConfig()) {
			// We display the errors, and stop
			$this->set(array(
				'validationErrors' => $this->Install->validationErrors,
				'installStep' => 1
			));
			return $this->render();
		}

		// We clear the cache to make sure we have the latest values
		CaracoleCache::clear();

		// We generate the icon CSS Sprite
		$this->__generateIconSprite();

		// We create the needed database tables
		$this->__createDatabaseTables();


		// Loading the user model
		$this->modelNames[] = 'User';
		$this->User =& ClassRegistry::init(array(
			'class' => Configure::read('Auth.useModel'),
			'alias' => 'User'
		));

		// We find the admin user
		$masterUser = $this->User->find('first', array('conditions' => array('User.is_master' => 1)));

		// Creating the master user
		if (empty($masterUser)) {
			// Displaying the user form
			if (empty($this->data)) {
				$this->set('installStep', 2);
				return $this->render();
			}

			//Validating the new user
			$this->User->create($this->data);
			if (!$this->User->validates()) {
				$this->set('installStep', 2);
				return $this->render();
			}

			// We save the master user
			if (empty($this->User->data['User']['nickname'])) $this->User->data['User']['nickname'] = __d('caracole_install', 'Admin', true);
			$this->User->data['User']['is_admin'] = 1;
			$this->User->data['User']['is_master'] = 1;
			$this->User->data['User']['password'] = Security::hash($this->data['User']['password'], null, true);
			$this->User->save($this->User->data, false);

		}

		// We mark the installation as finished
		$file = &new File(APP.'config'.DS.'installed', true);
		$file->write(__d('caracole_install', 'This file mark your Caracole installation as successful. Delete it if you want to restart the install procedure.', true));

		$this->set('installStep', 3);
		return $this->render();
	}


	/**
	 *	__generateIconSprite
	 *	Generate the icon sprite and css file
	 **/
	function __generateIconSprite() {
		$Icon = ClassRegistry::init('CaracoleIcons.Icon');
		$Icon->generate($Icon->findAll());
	}

	/**
	 *	__createDatabaseTables
	 *	We create all the tables needed by the installed plugins
	 **/
	function __createDatabaseTables() {
		$sqlFiles = CaracoleConfigure::getPluginInfo('dump');
		foreach($sqlFiles as $file) {
			$commands = explode(';', file_get_contents($file));
			foreach($commands as $command) {
				if (trim($command)=='') continue;
				$this->model->query($command);
			}
		}
	}

}
