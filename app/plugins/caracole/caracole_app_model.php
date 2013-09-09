<?php
/**
 *	CaracoleAppModel
 *
 *	This class works as a buffer between the AppModel and the cake Model. It contains shortcuts to the most used methods
 *	as well as ne methods to handle data.
 **/
class CaracoleAppModel extends Model {
	// Behaviors
	var $actsAs = array(
		'Caracole.Caracolable',				//	CARACOLE : Set some default configuration
		'CaracoleDocuments.Documentable',	//	CARACOLE : Will automate request on documents to make sure that we grab all data
		'Caracole.Cleanable',				//	CARACOLE : Will clean item input before saving it in the database
		'Containable'						//	CORE : Allow for much more controler over the returned find results
	);
	// Admin settings
	var $adminSettings = array();


	/**
	 *	find
	 *	We hook on the find() method to use __findSomething method if find('something') is called.
	 *	Don't forget to also create a __findSomethingCount to be used when paginating.
	 *
	 *	If you need to use a custom find in a paginate call, just add 'something' as the first value of the paginate
	 *	array : array_unshift($this->paginate, 'something'). The corresponding find method will then be automatically used
	 **/
	function find($type, $options = array(), $order = null, $recursive = null) {
		$methodName = '__find'.ucfirst($type);
		// Using default method if not defined
		if (!method_exists($this, $methodName)) {
			// Setting default options to make sure they won't get erased by the false value the paginate inserts
			//$options = Set::merge(array('fields' => array(), 'order' => $this->order), $options);
			// Adding a default order if none is set
			if (empty($options['order'])) $options['order'] = $this->order;

			return parent::find($type, $options, $order, $recursive);
		}
		// Using custom method
		return $this->{$methodName}($options, $order, $recursive);
	}

	/**
	 *	paginateCount
	 *	We need to define the method for all models to make sure that the paginate count use custom find methods
	 *	We check the extra['type'] to see if a custom find method is defined.
	 **/
	function paginateCount($conditions = array(), $recursive = null, $extra = array()) {
		// If no custom find specified, we return the default count
		if (empty($extra['type'])) {
			$parameters = compact('conditions');
			if ($recursive != $this->recursive) {
				$parameters['recursive'] = $recursive;
			}
			return $this->find('count', array_merge($parameters, $extra));
		}

		// We return the __findSomethingCount
		$methodName = '__paginateCount'.ucfirst($extra['type']);
		return $this->{$methodName}($conditions, $recursive, $extra);
	}

	 /**
	 *	translate
	 *	Translation method used to find a special string defined for this model.
	 *	It is used to get the human-readable name of the model, as well as the text to display when an admin action
	 *	is successfully fired, etc
	 *
	 *	If the specified string is not defined in the Configure key I18n.modelName.key, then a default one will be used.
	 *	You can define those keys on a per plugin basis by editing the plugin/config/i18n.php file or
	 *	the app/config/i18n.php for an application-wide setting
	 **/
	function translate($key) {
		// Finding the value in configure class
		$value = CaracoleConfigure::read('I18n.'.$this->name.'.'.$key);
		if (!empty($value)) {
			return $value;
		}

		// TODO : using sprintf and gettext function on every call of this method can surely be improved
		// Not found, we make a default list
		$human = Inflector::humanize($this->name);
		$plural = Inflector::pluralize($human);
		$default = array(
			'human' 		=> $human,
			'plural' 		=> $plural,
			'add'			=> sprintf(__d('caracole', 'New %1$s', true), strtolower($human)),
			'edit'			=> sprintf(__d('caracole', 'Edit %1$s', true), strtolower($human)),
			'reorder'		=> sprintf(__d('caracole', 'Reorder %1$s', true), strtolower($human)),
			'added'			=> sprintf(__d('caracole', '%1$s "%%1$s" added', true), $human),
			'edited'		=> sprintf(__d('caracole', '%1$s "%%1$s" edited', true), $human),
			'deleted'		=> sprintf(__d('caracole', '%1$s deleted', true), $human),
			'restored'		=> sprintf(__d('caracole', '%1$s restored', true), $human),
			'destroyed'		=> sprintf(__d('caracole', '%1$s destroyed', true), $human),
			'reordered'		=> sprintf(__d('caracole', '%1$s reordered', true), $plural)
		);

		return $default[$key];
	}





}