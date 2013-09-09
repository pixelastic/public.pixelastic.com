<?php
class UserFixture extends CakeTestFixture {
      var $name = 'User';
      var $import = 'CaracoleUsers.User';

      var $records = array(
        array('id' => 1, 'name' => 'tim@pixelastic.com', 'openid' => 'http://tim.openid.com',       'is_disabled' => 0, 'is_member' => 0, 'is_admin' => 1, 'is_master' => 1),
        array('id' => 2, 'name' => 'member@pixelastic.com', 'openid' => 'http://member.openid.com', 'is_disabled' => 0, 'is_member' => 1, 'is_admin' => 0, 'is_master' => 0),
        array('id' => 3, 'name' => 'admin@pixelastic.com', 'openid' => 'http://admin.openid.com',   'is_disabled' => 0, 'is_member' => 0, 'is_admin' => 1, 'is_master' => 0),
        array('id' => 4, 'name' => 'nothing@pixelastic.com', 'openid' => null,                      'is_disabled' => 0, 'is_member' => 0, 'is_admin' => 0, 'is_master' => 0),
        array('id' => 5, 'name' => 'disabled@pixelastic.com', 'openid' => null,                     'is_disabled' => 1, 'is_member' => 0, 'is_admin' => 0, 'is_master' => 0),
      );
 }
 ?>