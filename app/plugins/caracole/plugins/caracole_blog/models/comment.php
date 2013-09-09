<?php
/**
 *	Comment
 *	This model represent a comment posted on a given post
 **/
class Comment extends AppModel {
	var $order = array(
		'Comment.created' => 'DESC',
		'Comment.id' => 'DESC'
	);

	/**
	 *	__construct
	 *	Creates the model. We need to use this method to define special translateable strings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
		// belongsTo
		$this->belongsTo = array(
			'Post' => array(
				'className' => 'CaracoleBlog.Post',
				'counterCache' => true
			),
			Configure::read('Auth.useModel')
		);

		// Admin settings
		$this->adminSettings = array(
			'views' => array('spam', 'index', 'edit'),
			'toolbar' => array(
				'main' => array(
					// We can't add new comments, but we can search through them
					'index' => array(
						'add' => false,
						'search' => array(
							'mainField' => 'text', 		// We make a search on the text of each comment
							'autocomplete' => false		// We disable the autocomplete because comments do not display well
						)
					)
				),
				'secondary' => array(
					'edit' => array(
						// Linking to the parent post
						'post' => array(
							'label' => __d('caracole_blog', 'Post', true),
							'icon' => 'Post',
							'url' => array('controller' => 'posts', 'action' => 'admin_edit', 'id' => '{Comment.post_id}')
						)
					)
				)
			),
			'index' => array(
				'headers' => array(
					'Comment.text' => __d('caracole_blog', 'Text', true),
					'Comment.author' => __d('caracole_blog', 'Author', true)
				),
				'paginate' => array(
					'Comment' => array(
						'fields' => array(
							'Comment.text', 'Comment.author', 'Comment.is_spam'
						),
						'contain' => false
					)
				)
			),
			'fields' => array(
				'post_id' => array(
					'label' => __d('caracole_blog', 'Post', true),
					'required' => true
				),
				'author' => array(
					'label' => __d('caracole_blog', 'Author', true),
					'required' => true
				),
				'user_id' => array(
					'label' => __d('caracole_blog', 'User', true),
				),
				'email' => array(
					'label' => __d('caracole_blog', 'Email', true),
					'required' => true
				),
				'website' => array(
					'label' => __d('caracole_blog', 'Website', true),
				),
				'text' => array(
					'label' => __d('caracole_blog', 'Comment', true),
					'type' => 'textarea'
				),
				'is_spam' => array(
					'label' => __d('caracole_blog', 'This comment is a spam', true),
					'advanced' => true
				),
				'spam_js' => array(
					'label' => __d('caracole_blog', 'Javascript', true),
					'advanced' => true,
					'plain' => true,
				),
				'spam_delay' => array(
					'label' => __d('caracole_blog', 'Delay before posting', true),
					'advanced' => true,
					'plain' => true,
				),
				'spam_headers' => array(
					'label' => __d('caracole_blog', 'Headers', true),
					'advanced' => true,
					'plain' => true
				)
			)
		);

		//	Validation
		$this->validate = array(
			'author' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('caracole_blog', 'You have to type your name', true)
				),
			),
			'email' => array(
				'mailValid' => array(
					'rule' => array('email', false),
					'message' => __d('caracole_blog', "You have to type a valid email address. Don't worry, it won't be published", true)
				),
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('caracole_blog', "You have to type your mail address. Don't worry, it won't be published", true)
				)
			),
			'text' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('caracole_blog', "You can't add an empty comment.", true)
				),
			),
		);

		parent::__construct($id, $table, $ds);
	}

	/**
	 *	__findNospam
	 *	Returns only comments that are not spam
	 **/
	function __findNospam($options, $order = null, $recursive = null) {
		$options = Set::merge(array(
			'conditions' => array(
				'Comment.is_spam' => 0
			)
		), $options);
		return $this->find('all', $options, $order, $recursive);
	}

	/**
	 *	__findRecent
	 *	Returns the most recent comments
	 **/
	function __findRecent($options, $order = null, $recursive = null) {
		$options = Set::merge(array(
			'conditions' => array(
				'Comment.is_spam' => 0,
				'Post.is_draft' => 0 // Manually excluding drafted posts
			),
			'fields' => array(
				'Comment.text', 'Comment.id', 'Comment.email', 'Comment.author',
				'Post.slug', 'Post.id'
			),
			'order' => array('Comment.created' => 'DESC'),
			'limit' => 5
		), $options);
		return $this->find('all', $options, $order, $recursive);
	}


}
