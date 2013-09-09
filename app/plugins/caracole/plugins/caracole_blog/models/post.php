<?php
/**
 *	Post
 *	This is the classical blog post model
 **/
class Post extends AppModel {
	var $actsAs = array(
		'Caracole.Draftable',
		'Caracole.Sluggable',
		'Caracole.HabtmCounterCache' => array('counterScope' => array('Post.is_draft' => 0))
	);
	var $order = array('Post.publish_start' => 'DESC', 'Post.id' => 'DESC');
	var $hasAndBelongsToMany = array('CaracoleBlog.Tag');
	var $hasMany = array(
		'Comment' => array(
			'className' => 'CaracoleBlog.Comment',
			'conditions' => array('Comment.is_spam' => 0),
			'order' => array('Comment.created' => 'ASC', 'Comment.id' => 'ASC')
		)
	);

	/**
	 *	__construct
	 *	Creates the model. We need to use this method to define special translateable strings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
		// Admin settings
		$this->adminSettings = array(
			'views' => array('index'),
			'toolbar' => array(
				'main' => array(
				),
				'secondary' => array(
					'index' 	=> array(
						'tags' => array(
							'label' => __d('caracole_blog', 'Tags', true),
							'url' => array('controller' => 'tags'),
							'icon' => 'Tag'
						)
					),
					'edit' => array(
						'comments' => array(
							'label' => __d('caracole_blog', 'Comments', true),
							'icon' => 'Comment',
							'url' => array('controller' => 'comments', 'action' => 'search', 'Comment.post_id' => '{Post.id}')
						)
					)
				)
			),
			'index' => array(
				'headers' => array(
					'Post.publish_start' => __d('caracole_blog', 'Date published', true)
				),
				'paginate' => array(
					'Post' => array(
						'fields' => array(
							'Post.publish_start'
						),
						'contain' => false
					)
				)
			),
			'fields' => array(
				'name' => array(
					'label' => __d('caracole_blog', 'Title', true),
					'required' => true
				),
				'text' => array(
					'label' => __d('caracole_blog', 'Text', true)
				),
				'Tag.Tag' => array(
					'label' => 'Tags'
				),
				'publish_start' => array(
					'label' => __d('caracole_blog', 'Publish from', true),
					'help' => __d('caracole_blog', 'The post will not be publicly visible until this date.', true),
					'advanced' => true
				),
				'publish_end' => array(
					'label' => __d('caracole_blog', 'Publish until', true),
					'help' => __d('caracole_blog', 'The post will disapear from the public site when this date is due. Leave empty to never hide the post.', true),
					'advanced' => true
				)
			)
		);

		//	Validation
		$this->validate = array(
			'name' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('caracole_blog', 'You must set a title for the post', true)
				)
			),
			'slug' => array(
				'unique' => array(
					'rule' => 'isUnique',
					'message' => __d('caracole_blog', 'Another post is using the same slug', true)
				)
			)
		);

		parent::__construct($id, $table, $ds);
	}


	/**
	 *	__getFindPublishedOptions
	 *	Helper method to get the find options to get the published posts.
	 *	Used by both __findPublished and __paginateCountPublished
	 **/
	function __getFindPublishedOptions($options) {
		$now = date('Y-m-d H:i:s');
		return Set::merge(array(
			'conditions' => array(
				$this->alias.'.publish_start <=' => $now,
				"OR" => array(
					$this->alias.'.publish_end' => '0000-00-00 00:00:00',
					$this->alias.'.publish_end >=' => $now
				)
			)
		), $options);
	}

	/**
	 *	__findPublished
	 *	Returns only published posts
	 **/
	function __findPublished($options, $order = null, $recursive = null) {
		return $this->find('all', $this->__getFindPublishedOptions($options), $order, $recursive);
	}

	/**
	 *	__paginateCountPublished
	 *	Returns the total count of published posts. Used for pagination
	 **/
	function __paginateCountPublished($conditions = array(), $recursive = null, $extra = array()) {
		$options = $this->__getFindPublishedOptions(array(
			'conditions' => $conditions,
			'recursive' => $recursive
		));
		return $this->find('count', $options);
	}

	/**
	 *	__findFirstPublished
	 *	Returns the first matching post only if is published
	 **/
	function __findFirstPublished($options, $order = null, $recursive = null) {
		return $this->find('first', $this->__getFindPublishedOptions($options), $order, $recursive);
	}

	/**
	 *	__findCalendar
	 *	Returns a list of posts, one for each month where one is published
	 **/
	function __findCalendar($options, $order = null, $recursive = null) {
		$options = Set::merge(array(
			'group' => array('SUBSTRING(Post.publish_start,1,8)'),
			'order' => array(
				'SUBSTRING(Post.publish_start, 1, 4)' => 'DESC',
				'SUBSTRING(Post.publish_start, 6, 7)' => 'ASC',
			)
		), $options);
		return $this->find('all', $this->__getFindPublishedOptions($options), $order, $recursive);
	}

	/**
	 *	__findRecent
	 *	Returns the most recent posts
	 **/
	function __findRecent($options, $order = null, $recursive = null) {
		$options = Set::merge(array(
			'fields' => array('Post.name', 'Post.slug', 'Post.id'),
			'order' => array('Post.publish_start' => 'DESC', 'Post.publish_start' => 'DESC'),
			'contain' => false,
			'limit' => 5
		), $options);
		return $this->find('all', $this->__getFindPublishedOptions($options), $order, $recursive);
	}





	/**
	 *	url
	 *	Returns a formatted url to access the post
	 **/
	function url($options) {
		return array(
			'admin' => false,
			'plugin' => 'caracole_blog',
			'controller' => 'posts',
			'action' => 'view',
			'id' => $options['Post']['id'],
			'postSlug' => $options['Post']['slug']
		);
	}


}
