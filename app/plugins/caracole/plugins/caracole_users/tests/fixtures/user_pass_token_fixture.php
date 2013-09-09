<?php
class UserPassTokenFixture extends CakeTestFixture {
      var $name = 'UserPassToken';
      var $import = 'CaracoleUsers.UserPassToken';

      function __construct() {
		parent::__construct(func_get_args());

		$tomorrow = date('Y-m-d H:i:s', strtotime('+1 day'));
		$yesterday = date('Y-m-d H:i:s', strtotime('-1 day'));

		$this->records = array(
			array('id' => 1, 'user_id' => 1, 'token' => Security::hash('tomorrow', null, true), 	'expires' => $tomorrow, 'is_used' => 0),
			array('id' => 2, 'user_id' => 2, 'token' => Security::hash('used', null, true), 		'expires' => $tomorrow, 'is_used' => 1),
			array('id' => 3, 'user_id' => 3, 'token' => Security::hash('otheruser', null, true), 	'expires' => $tomorrow, 'is_used' => 0),
			array('id' => 4, 'user_id' => 4, 'token' => Security::hash('yesterday', null, true), 	'expires' => $yesterday, 'is_used' => 0),
		);


	  }

 }
 ?>