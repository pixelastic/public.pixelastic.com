<?php
/**
 *	User
 *	Used to login user to the site.
 *	Will allow for 6 default right levels :
 *		- anonymous : The user is not saved in the database
 *		- disabled : the user won't be able to login in, but is saved in the DB
 *		- normal : Any user is considered this level as soon as he is saved in the db and not flagged as disabled
 *		- member : Will be allowed to access the member area (if any). Actions will be prefixed with member_
 *		- admin : Will be able to access the admin panel.
 *		- master : Level given to the first user created, can access the admin panel as well as some more advanced features
 *
 *	The user login are their mail address
 *
 *	If you need to extend this model for the specific needs of your app, just create your own CustomUser model and extends this one
 *
 **/
class User extends AppModel {
	/**
	 *	__construct
	 *	Creates the model. We need to use this method to define special translateable strings
	 **/
	function __construct($id = false, $table = null, $ds = null) {
		// HasMany
		if (empty($this->hasMany)) {
			$this->hasMany = array('CaracoleUsers.UserLoginToken', 'CaracoleUsers.UserPassToken');
		}

		// Order
		if (empty($this->order)) {
			$this->order = array($id['alias'].'.name' => 'ASC');
		}

		//	Validation
		$this->validate = array(
			'name' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('caracole_users', "You must choose a login", true)
				),
				'isUnique' => array(
					'rule' => 'isUnique',
					'message' => __d('caracole_users', 'Sorry, this login is already taken', true),
				),
				'mailValid' => array(
					'rule' => array('email', false),
					'message' => __d('caracole_users', 'The login must be a valid email address. It will be used to send a new password if needed.', true)
				)
			),
			'password' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => __d('caracole_users', 'You have to choose a password', true)
				),
				'minLength' => array(
					'rule' => array('minLength', 8),
					'message' => __d('caracole_users', 'Your password must be at least 8 characters long.', true)
				)
			),
			'password_confirm' => array(
				'match' => array(
					'rule' => array('__validatePasswordMatch', 'password'),
					'message' => __d('caracole_users', 'Your password and its confirmation do not match.', true)
				)
			)
		);



		// Default rights
		$this->defaultRights = array(
			'is_member' => '*:*,!*:admin_*,*:member_*',
			'is_admin'  => '*:*,*:admin_*,*:member_*'
		);
		// Admin settings
		$this->adminSettings = array(
			'views' => array('login', 'pass', 'index'),
			'toolbar' => array(
				'main' => array(
					'login'	=> array(
						'pass' => array(
							'label' => __d('caracole_users', 'I forgot my password', true),
							'url' => array('action' => 'pass')
						),
					)
				)
			),
			'index' => array(
				'headers' => array(
					$id['alias'].'.name' => __d('caracole_users', 'Name', true),
					$id['alias'].'.status' => array('order' => false, 'label' => __d('caracole_users', 'Status', true))
				),
				'paginate' => array(
					$id['alias'] => array(
						'fields' => array(
							$id['alias'].'.is_disabled',
							$id['alias'].'.is_member',
							$id['alias'].'.is_admin',
							$id['alias'].'.is_master',
						)
					),
				)
			),
			'fields' => array(
				'name' => array(
					'label' => __d('caracole_users', 'Login', true),
					'help' => __d('caracole_users', 'This should be a valid email address. It will be used to send a new password if needed', true),
					'required' => true
				),
				// Needed to trick Firefox to autofill that field instead of the real pass field
				'dummypass' => array(
					'label' => __d('caracole_users', 'Keep empty', true),
					'help'	=> __d('caracole_users', "Keep this field empty. It is only here to defeat a bug in Firefox. You shouldn't even be able to see that text with CSS enabled.", true),
					'div' => 'input dummy',
					'type' => 'password'
				),
				'password' => array(
					'label' => __d('caracole_users', 'Password', true),
					'required' => true
				),
				'password_confirm' => array(
					'label' => __d('caracole_users', 'Confirm password', true),
					'type' => 'password',
					'help' => __d('caracole_users', 'Type your password again, to avoid typos.', true)
				),
				'gender' => array(
					'label' => __d('caracole_users', 'Gender', true),
					'type' => 'radio',
					'options' => array(
						'Mr.' => __d('caracole_users', 'Mr.', true),
						'Mrs' => __d('caracole_users', 'Mrs', true),
						'Miss' => __d('caracole_users', 'Miss', true),
					)
				),
				'first_name' => __d('caracole_users', 'First name', true),
				'surname' => __d('caracole_users', 'Surname', true),
				'nickname' => __d('caracole_users', 'Nickname', true),
				'address' => array(
					'label' => __d('caracole_users', 'Address', true),
					'type' => 'textarea'
				),
				'postal_code' => __d('caracole_users', 'Postal code', true),
				'city' => __d('caracole_users', 'City', true),
				'tel' => __d('caracole_users', 'Telephone number', true),
				'mobile' => __d('caracole_users', 'Mobile number', true),



				'openid' => array(
					'label' => __d('caracole_users', 'OpenId', true),
					'help' => __d('caracole_users', 'Type here the associated OpenId url.', true),
					'advanced' => true
				),
				'is_disabled' => array(
					'label' => __d('caracole_users', 'Disable this user', true),
					'help' => __d('caracole_users', "Disabled users can't log in to the site at all", true),
					'advanced' => true
				),
				'is_member' => array(
					'label' => __d('caracole_users', 'This is a member', true),
					'help' => sprintf(
						__d('caracole_users', 'Members can access member_ prefixed methods. Syntax is : %1$s', true),
						$this->defaultRights['is_member']
					),
					'advanced' => true
				),
				'is_admin' => array(
					'label' => __d('caracole_users', 'This is an admin', true),
					'help' => sprintf(
						__d('caracole_users', 'Admin can access the admin panel (where you are right now). Syntax is : %1$s', true),
						$this->defaultRights['is_admin']
					),
					'advanced' => true
				),
				'is_master' => array(
					'label' => __d('caracole_users', 'This is a master user', true),
					'help' => __d('caracole_users', 'Master users can access advanced features of the admin panel', true),
					'advanced' => true
				),
				'acl' => array(
					'label' => __d('caracole_users', 'Rights', true),
					'help' => __d('caracole_users', 'Define additional rights for this user. Have a look at the previous checkboxes for syntax examples.', true),
					'advanced' => true
				),
			)
		);

		parent::__construct($id, $table, $ds);

	}


	/**
	 *	__validateMatch
	 *	Validate that the confirm password match with the initial one
	 **/
	function __validatePasswordMatch($check, $otherField) {
		return $this->data[$this->alias][key($check)]==$this->data[$this->alias][$otherField];
	}

	/**
	 *	__validateEmailExists
	 *	Validate that the given email exists in the database
	 **/
	function __validateEmailExists($check) {
		return $this->find('count', array('conditions' => array($this->alias.'.name' => $this->data[$this->alias][key($check)])));
	}


}
