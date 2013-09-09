  <?php
    Configure::write('Auth.useModel', 'CaracoleUsers.User');
    App::import('Model', 'CaracoleUsers.User');

    class UserTestCase extends CakeTestCase {
        var $fixtures = array('plugin.caracole_users.user', 'plugin.caracole_users.user_login_token', 'plugin.caracole_users.user_pass_token');

        /**
         *  test__validatePasswordMatch
         * */
        function test__validatePasswordMatch() {
            $this->User = & ClassRegistry::init('CaracoleUsers.User');

            // True
            $this->User->data = array('User' => array('password' => 'blablabla', 'password_confirm' => 'blablabla'));
            $check = array('password' => 'blablabla');
            $result = $this->User->__validatePasswordMatch($check, 'password_confirm');
            $this->assertTrue($result);

            // False
            $this->User->data = array('User' => array('password' => 'blablabla', 'password_confirm' => 'bliblibli'));
            $check = array('password' => 'blablabla');
            $result = $this->User->__validatePasswordMatch($check, 'password_confirm');
            $this->assertFalse($result);

        }

        /**
         *  test__validateEmailExists
         *  The email MUST exists in the database
         * */
        function test__validateEmailExists() {
            $this->User = & ClassRegistry::init('CaracoleUsers.User');

            // Pass : is in database
            $this->User->data = array('User' => array('name' => 'tim@pixelastic.com'));
            $check = array('name' => 'tim@pixelastic.com');
            $result = $this->User->__validateEmailExists($check);
            $this->assertTrue($result);

            // Fail : is not in database
            $this->User->data = array('User' => array('name' => 'nonexistent@email.com'));
            $check = array('name' => 'nonexistent@email.com');
            $result = $this->User->__validateEmailExists($check);
            $this->assertFalse($result);

        }

    }
    ?>