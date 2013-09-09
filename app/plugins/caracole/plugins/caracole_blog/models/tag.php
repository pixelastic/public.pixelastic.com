<?php
/**
 *	Tag
 *	This model handles blog tags
 **/
class Tag extends AppModel {
	var $actsAs = array('Caracole.Sluggable');
	var $order = array('Tag.name' => 'ASC');
	var $hasAndBelongsToMany = array('CaracoleBlog.Post');

	/**
	 *	__construct
	 *	Creates the model. We need to use this method to define special translateable strings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
		// Admin settings
		$this->adminSettings = array(
			'views' => array('merge'),
			'toolbar' => array(
				'secondary' => array(
					'index' 	=> array(
						'back' => array(
							'label' => __d('caracole_blog', 'Back', true),
							'url' => array('controller' => 'posts'),
							'icon' => 'back'
						),
						'merge' => array(
							'label' => __d('caracole_blog', 'Merge', true),
							'url' => array('action' => 'admin_merge'),
							'icon' => 'Tag_merge'
						)
					),
					'merge' => array(
						'back'
					)
				)
			),
			'fields' => array(
				'name' => array(
					'label' => __d('caracole_blog', 'Name', true),
					'required' => true
				)
			)
		);

		//	Validation
		$this->validate = array(
			'name' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('caracole_blog', 'You must set the name of the tag', true)
				)
			),
			'slug' => array(
				'unique' => array(
					'rule' => 'isUnique',
					'message' => __d('caracole_blog', 'Another tag is using the same slug', true)
				)
			),
		);

		$this->mergeValidate = array(
			'source' => array(
				'notNull' => array(
					'rule' => array('comparison', '>=', 1),
					'message' => __d('caracole_blog', 'You must select a source tag', true)
				),
			),
			'destination' => array(
				'notNull' => array(
					'rule' => array('comparison', '>=', 1),
					'message' => __d('caracole_blog', 'You must select a destination tag', true)
				),
				'differentFromSource' => array(
					'rule' => array('validateDifferentSourceAndDestination'),
					'message' => __d('caracole_blog', 'The source and destination tag are the same', true)
				)
			)
		);

		parent::__construct($id, $table, $ds);
	}

	/**
	 *	validateDifferentSourceAndDestination
	 *	Checks that the destination and source fields are different
	 **/
	function validateDifferentSourceAndDestination() {
		return $this->data['Tag']['source']!=$this->data['Tag']['destination'];
	}

	/**
	 *	__findPopular
	 *	Returns the most popular tags
	 **/
	function __findPopular($options, $order = null, $recursive = null) {
		$options = Set::merge(array(
			'contain' => false,
			'order' => array('Tag.post_count' => 'DESC', 'Tag.name' => 'ASC')
		), $options);
		return $this->find('all', $options, $order, $recursive);
	}

	/**
	 *	url
	 *	Returns a formatted url to access the list of post related to this tag
	 **/
	function url($options) {
		return array(
			'admin' => false,
			'plugin' => 'caracole_blog',
			'controller' => 'tags',
			'action' => 'view',
			'tagSlug' => $options['Tag']['slug']
		);
	}


}
