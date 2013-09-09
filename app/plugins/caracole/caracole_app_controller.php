<?php
/**
 *	CaracoleAppController
 *
 *	This class works as a buffer between the AppController and the cake Controller. It contains
 *	default methods for the most used actions as well as offering admin capabilities
 *
 **/
class CaracoleAppController extends Controller {
	// View
	var $view = 'Caracole.CaracoleApp';

	// Components
	var $defaultComponents = array(
		'Cookie',						//	CORE : Access to cookies
		'RequestHandler',				//	CORE : Tests on request
		'Session',						//	CORE : Session handling
		'Security',						//	CORE : Security functionnality
		'Caracole.Caracole',			//	CARACOLE : Shortcuts and general configuration
		'CaracoleI18n.I18n',			//	CARACOLE : Redirecting to correct language
		'CaracoleUsers.CaracoleAuth',	//	CARACOLE : Authentification made simple
		'CaracoleAntispam.Antispam',	//	CARACOLE : Spam counter measures
		'DebugKit.Toolbar' => array(	//  CARACOLE : Debugkit with custom panels
			'panels' => array(
				'history' => false, 'session' => false, 'request' => false, 'sqlLog' => false, 'log' => false, 'variables' => false, 'timer' => false,
				'Caracole.Javascript', 'Caracole.SwfUpload', 'variables', 'Caracole.Configure', 'request', 'log', 'session', 'timer', 'sqlLog'
			)
		)
		//'CaracoleSettings.Settings',	//	CARACOLE : Load settings stored in database into the Configure class


		//'CaracoleRestorable.Restorable',//	CARACOLE : Adds restore and destroy functionnality to the admin panel

	);

	// Helpers
	var $defaultHelpers = array(
		'Session',						//  CORE : Session handling
		'Cache',						//	CORE : Allow view caching
		'Form',							//	CORE : Form and inputs helper
		'Time',							//  CORE : Time-related displays
		'Caracole.Fastcode',			//	CARACOLE : Maps to common html methods
		'CaracolePacker.Packer',		//	CARACOLE : Compress CSS and Js files
		'CaracoleAntispam.Antispam',	//	CARACOLE : Spam counter measures
	);


	/**
	 *	__construct
	 *	We override the __construct method to add our own default component and helpers and still allow
	 *	subclasses to define their
	 **/
	function __construct() {
		$this->components = Set::merge($this->defaultComponents, $this->components);
		$this->helpers = Set::merge($this->defaultHelpers, $this->helpers);
		parent::__construct();
	}

	/**
	 *	admin_add
	 *	Adding a new item to the database. Will use the same internal logic as admin_edit
	 *	We won't do a setAction because we still need the two actions to have a different $action property (toolbar
	 *	is generated based on this property)
	 **/
	function admin_add() {
		return $this->admin_edit();
	}

	/**
	 *	admin_apply
	 *	Method used to have one form in the admin index page that will dispatch to the various actions
	 **/
	function admin_apply() {
		// Applying action if one is set
		if (!empty($this->data) && !empty($this->data['Options']['action'])) {
			$methodName = 'admin_'.$this->data['Options']['action'];
			if (method_exists($this, $methodName)) {
				return $this->setAction($methodName);
			}
		}
		// Back to default index method if none is set
		return $this->setAction('admin_index');
	}

	/**
	 *	admin_edit
	 *	Edit an existing item from the database, or add a new one if no id is passed
	 **/
	function admin_edit($id = null) {
		// If it's a draft, we disable the behavior for the next calls, allowing to catch any item
		if (array_key_exists('Draftable', $this->model->Behaviors)) {
			$this->model->disableDraftablePermanently();
		}

		// Setting the id
		$this->model->id = $id;

		// Loading belongsTo options into the view
		foreach($this->model->belongsTo as $btName => &$btOptions) {
			$foreignKey = $btOptions['foreignKey'];
			$fieldSettings = $this->model->adminSettings['fields'][$btOptions['foreignKey']];
			// Skipping non-editable fields
			if (empty($fieldSettings)) continue;
			// Skipping if list of options already set
			if (is_array($fieldSettings) && !empty($fieldSettings['options'])) continue;
			// Fetching the list and passing it to the view
			$variableName = Inflector::variable(Inflector::pluralize(preg_replace('/_id$/', '', $foreignKey)));
			$this->set($variableName, $this->model->{$btName}->find('list'));
		}

		// Checking HABTM in the modelName.modelName convention and passing result to the view
		foreach($this->model->hasAndBelongsToMany as &$habtm) {
			$habtmName = $habtm['className'];
			if (empty($this->model->adminSettings['fields'][$habtmName.'.'.$habtmName])) continue;
			$this->set(Inflector::tableize($habtmName), $this->model->{$habtmName}->find('list'));
		}

		// Passing vars to the view
		$this->set(array(
			'title_for_layout' 	=> empty($id) ? $this->model->translate('add') : $this->model->translate('edit'),
			'title' 			=> $this->model->translate('human'),
			'fields'			=> $this->model->adminSettings['fields']
		));

		// Posting data
		if (!empty($this->data)) {
			// Creating the instance
			$this->model->create($this->data);

			// Uploading files if needed
			$Document = &ClassRegistry::init('CaracoleDocuments.Document');
			$Document->loadIDFromUpload($this->model);


			// If it does not validate, we return to the form with data pre-filled
			if (!$this->model->validates()) {
				$this->data = $this->model->data;
				return;
			}

			// We save the model and its associated data
			$this->model->saveAll($this->model->data, array('validate' => false));
			$this->data[$this->model->alias][$this->model->primaryKey] = $this->model->id;
			// We get the name of this new item
			$findName = $this->model->find('first', array(
				'conditions' => array($this->model->alias.'.'.$this->model->primaryKey => $this->model->id),
				'fields' => array($this->model->alias.'.'.$this->model->displayField)
			));
			$itemName = $findName[$this->model->alias][$this->model->displayField];

			// Adding a flash message
			$this->Session->setFlash(sprintf(
				empty($id) ? $this->model->translate('added') : $this->model->translate('edited'),
				$itemName
			), 'success');

			// Redirecting to the index
			if (!$this->RequestHandler->isAjax()) {
				return $this->redirect(array('action' => 'index'));
			}
			return;
		}

		// Reading model
		$this->model->read();
		// Populating view
		$this->data = $this->model->data;

		// Prefilling form with data coming from named param
		foreach($this->params['named'] as $fieldName => $fieldValue) {
			// Casting Model.field
			if (strpos($fieldName, '.')) list($modelName, $fieldName) = explode('.', $fieldName);
			else $modelName = $this->model->alias;

			// Skipping primary key
			if ($modelName == $this->model->alias && $fieldName==$this->model->primaryKey) continue;
			// Skipping fields not in fieldset
			if (empty($this->model->adminSettings['fields'][$fieldName])) continue;
			// Skipping fields marked as hidden
			elseif (!empty($this->model->adminSettings['fields'][$fieldName]['type']) && $this->model->adminSettings['fields'][$fieldName]['type']=='hidden') continue;
			// Prefilling the field
			$this->data[$modelName][$fieldName] = $fieldValue;
		}
	}

	/**
	 *	admin_view
	 *	Display in a non-editable way the informations regarding the item
	 **/
	function admin_view($id = null) {
		// Stopping if no id
		if (empty($id)) return $this->redirect(array('action' => 'index'));

		// Getting information
		$this->model->id = $id;
		$this->model->read();
		// Populating form
		$this->data = $this->model->data;

		// Passing all fields as plain text
		$fields = $this->model->adminSettings['fields'];
		foreach($fields as $fieldName => &$options) {
			// Converting to array if needed
			if (is_string($options)) $options = array('label' => $options);
			// Forcing plain text type
			$options['plain'] = true;
		}

		// Passing vars to the view
		$this->set(array(
			'title' 		=> $this->model->data[$this->model->alias][$this->model->displayField],
			'fields'		=> $fields,
			'hideSubmit' 	=> true
		));

		return $this->render('admin_edit');

	}

	/**
	 *	admin_delete
	 *	Deletes a set of items from the database. The list of ids must be passed as a POST data
	 **/
	function admin_delete() {
		// We delete the selected items
		$deleted = false;
		foreach($this->data[$this->model->alias] as $id => &$item) {
			// Skipping non-checked items
			if (empty($item['checked'])) continue;
			// Deleting checked items
			if ($this->model->delete($id)) {
				$deleted = true;
			}
		}
		// Flash message
		if (!empty($deleted)) {
			$this->Session->setFlash($this->model->translate('deleted'), 'success');
		}
		// Redirecting to index
		if (!$this->RequestHandler->isAjax()) {
			return $this->redirect(array('action' => 'index'));
		}
	}

	/**
	 *	admin_index
	 *	Display the list of all items of the current model in an admin list.
	 *	Each entry links to the edit page of the item.
	 *
	 **/
	function admin_index() {
		// We define the paginate options to grab the items
		$this->paginate = $this->model->adminSettings['index']['paginate'];
		// We get the list
		$itemList = $this->paginate($this->model);

		// Loading belongsTo options for the advanced search
		$searchOptions = $this->model->adminSettings['toolbar']['main']['index']['search'];
		if (!empty($searchOptions['advancedFields'])) {
			foreach($this->model->belongsTo as $btName => &$btOptions) {
				// Skipping non-searchable fields
				if (empty($searchOptions['advancedFields'][$btOptions['foreignKey']])) continue;
				// Fetching the list and passing it to the view
				$variableName = Inflector::variable(Inflector::pluralize(preg_replace('/_id$/', '', $btOptions['foreignKey'])));
				$this->set($variableName, $this->model->{$btName}->find('list'));
			}
		}


		// Passing vars to the view
		$this->set(array(
			'title_for_layout' 	=> $this->model->translate('plural'),
			'headers' 			=> $this->model->adminSettings['index']['headers'],
			'actionOptions'		=> $this->model->adminSettings['index']['actions'],
			'itemList'			=> $itemList
		));
	}

	/**
	 *	admin_search
	 *	Method used to have search result in REST mode (search params are in the url), but use the admin_index method
	 *	internally
	 *	The first call (with post data) will redirect to the same page using get data
	 *
	 *	Prefixing a value with a tilde ~ will do a broad (LIKE) search instead of a perfect match. This is enabled by default
	 *	when using the search field in the toolbar
	 **/
	function admin_search() {
		// We redirect to get it in GET mode
		if (!empty($this->data)) {
			$params = array();

			// We will convert the submitted data in a valid array of named params
			foreach($this->data as $modelName => &$modelFields) {
				// Skipping Security tokens
				if ($modelName=='_Token') continue;
				// Skipping options
				if ($modelName=='Options') continue;

				foreach($modelFields as $fieldName => $fieldValue) {
					if (empty($fieldValue)) continue;
					// Saving to params list
					$params[$modelName.'.'.$fieldName] = $fieldValue;
				}
			}
			// Doing a broad search on the field in the toolbar
			if (!empty($params[$this->data['Options']['mainFieldName']])) {
				$params[$this->data['Options']['mainFieldName']] = '~'.$params[$this->data['Options']['mainFieldName']];
			}

			return $this->redirect($params);
		}

		// Get request, we add the named params to the paginate options
		foreach($this->params['named'] as $fieldName => $fieldValue) {
			// We skip pagination options like page, sort and direction, we only keep conditions on the fields
			if (in_array($fieldName, array('page', 'sort', 'direction'))) continue;

			// Casting Model.field
			if (strpos($fieldName, '.')) list($modelName, $fieldName) = explode('.', $fieldName);
			else $modelName = $this->model->alias;

			// Flagging broad search and removing tilde
			$broadSearch = substr($fieldValue,0,1)=='~';
			if ($broadSearch) $fieldValue = substr($fieldValue,1);

			// Prefilling the search form
			$this->data[$modelName][$fieldName] = $fieldValue;

			// Adding a condition to the paginate
			if ($broadSearch) {
				$fieldName.= ' LIKE';
				$fieldValue = '%'.$fieldValue.'%';
			}
			$this->model->adminSettings['index']['paginate'][$this->model->alias]['conditions'][$modelName.'.'.$fieldName] = $fieldValue;
		}

		// We use the classic admin_index method
		$this->setAction('admin_index');
	}



	/**
	 *	admin_reorder
	 *	Change the order in which the items are fetched. This method can only be working when JS is enabled.
	 *	It uses drag'n'drop to send an array of id
	 *
	 *	Will not display drafted element by default
	 **/
	function admin_reorder() {
		// Ajax submitted content, we save the new orders
		if ($this->RequestHandler->isAjax() && !empty($this->data)) {
			// Updating orders
			foreach($this->data[$this->model->alias] as $idField => $order) {
				$id = str_replace('id_', '', $idField);
				$this->model->id = $id;
				$this->model->saveField('order', $order);
			}
			// Passing to the view
			$this->Session->setFlash($this->model->translate('reordered'), 'success');
			return;
		}


		// We get the full list of items
		$itemList = $this->model->find('all', array('fields' => array($this->model->primaryKey, $this->model->displayField)));

		$this->set(array(
			'itemList' =>  $itemList,
			'displayField' => $this->model->displayField,
			'title_for_layout' 	=> $this->model->translate('reorder')
		));
	}



	/**
	 *	redirect
	 *	Overriding the redirect method to keep the correct language even during a redirect.
	 *
	 *	We also block redirecting when in a unit test
	 **/
	function redirect($url, $status = null, $exit = true) {
		// Using default method if default language
		if (Configure::read('Config.language')==Configure::read('I18n.default')) return parent::redirect($url, $status, $exit);

		// Prepending the language if a specific language is specified
		$url = str_replace(FULL_BASE_URL, FULL_BASE_URL.'/'.Configure::read('Config.language'), Router::url($url, true));
		return parent::redirect($url, $status, $exit);

	}




	/**
	 *	index
	 *	This is the default index method that all models will inherit
	 *	It just gets all the items and passed them as an itemList
	 **/
	function index() {
		$this->set('itemList', $this->model->find('all'));
	}


	/**
	 *	view
	 *	This is the defaul view method that all models will inherit
	 *	It just gets information about one item (passed as id) and pass its information
	 **/
	function view($id = null) {
		// No id
		if (empty($id)) return $this->cakeError('error404');
		$item = $this->model->find('first', array('conditions' => array($this->model->name.'.id' => $id)));
		// No item
		if (empty($item)) return $this->cakeError('error404');
		$this->set('item', $item);
	}






}