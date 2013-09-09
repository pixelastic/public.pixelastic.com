<?php
App::import('Core', 'Security');
class UserLoginTokenFixture extends CakeTestFixture {
      var $name = 'UserLoginToken';
      var $import = 'CaracoleUsers.UserLoginToken';

      function __construct() {
            parent::__construct();

            $this->records = array(
                  array('id' => 1, 'user_id' => 1, 'token' => Security::hash('testtoken', null, true), 'expires' => date('Y-m-d H:i:s', strtotime('+1 year')))
            );
      }
 }
 ?>