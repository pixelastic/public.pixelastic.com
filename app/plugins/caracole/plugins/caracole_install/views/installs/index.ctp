<?php
	/**
	 *	Install page
	 *	Will guide the user into installing Caracole.
	 **/
	$this->set('title_for_layout', __d('caracole_install', 'Caracole installation', true));

	/**
	 *	Install : Step 1
	 *	We check that the basic config values, writing permissions and database connection are set
	 **/
	if ($installStep==1) {
		// Information message
		echo $this->Fastcode->message(
			__d('caracole_install', 'Welcome to the Caracole installation process. Please review all errors shown below and reload the page.', true),
			'information'
		);

		// List of errors
		foreach($validationErrors as &$error) {
			echo $this->Fastcode->message($error, 'error');
		}

		echo $this->Fastcode->message(
			__d('caracole_install', 'Please make the appropriate changes in your app/config/config.php file and change your directories access rights.', true),
			'notice'
		);

		// Stop
		return;
	}

	/**
	 *	Install : Step 2
	 *	We ask the user for a login and password
	 **/
	if ($installStep==2) {

		echo $this->Fastcode->message(__d('caracole_install', 'You have almost finished the installation process. You now just have to enter the login and password you would like to use for the admin panel.', true), 'information');

		// Starting the form
		echo $this->Form->create(null, array(
			'url' => $this->here,
			'class'		=> 'niceForm'
		));

		// Wrapping
		echo '<div class="fieldsets">';

			// Title
			echo $this->Html->tag('h3', __d('caracole_install', 'Setting up the master admin', true));

			// Login
			echo $this->Fastcode->input('User.name', array(
				'label' => __d('caracole_install', 'Login', true),
				'help' => __d('caracole_install', 'Type the login that will be used to login as the main admin. This must be a valid mail address because we will use it to help you recover your password.', true),
				'required' => true
			));

			// Pass
			echo $this->Fastcode->input('User.password', array(
				'label' => __d('caracole_install', 'Password', true),
				'help' => __d('caracole_install', 'Try to make it difficult to guess. Use number and letters, upper and lowercase', true),
				'required' => true
			));

			// Confirm pass
			echo $this->Fastcode->input('User.password_confirm', array(
				'label' => __d('caracole_install', 'Confirm password', true),
				'type' => 'password',
				'help' => __d('caracole_install', 'Just to make sure that there is no typo', true),
				'required' => true
			));

			// Nickname
			echo $this->Fastcode->input('User.nickname', array(
				'label' => __d('caracole_install', 'Nickname', true)
			));

		// Closing the wrapping div
		echo '</div>';

		// Adding a submit button
		echo $this->Fastcode->div(
				$this->Fastcode->button(
					__d('caracole_install', 'Finish install', true),
					array(
						'icon' => 'valid',
						'type' => 'submit',
					)
				)
				,array('class' => 'submit')
		);


	// Ending the form
	echo $this->Form->end();

	}

	/**
	 *	Install : Step 3
	 *	Installation is finished
	 **/
	if ($installStep==3) {
		echo $this->Fastcode->message(
			__d('caracole_install', 'Congratulations, you have succesfully installed Caracole.', true),
			'success'
		);

		echo $this->Fastcode->message(
			__d('caracole_install', 'You can now access the admin panel with the login and pass you typed on the previous page.', true),
			'information'
		);

		echo $this->Fastcode->link(
			__d('caracole_install', 'Go to the admin panel', true),
			Configure::read('SiteUrl.admin'),
			array(
				'class' => 'button goToAdminPanel',
				'icon' => 'valid'
			)
		);
	}
