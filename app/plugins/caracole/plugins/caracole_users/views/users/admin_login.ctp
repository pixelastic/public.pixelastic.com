<?php
	$this->set(array(
		'title_for_layout' => __d('caracole_users', 'Log in', true),
		'pageCssClass' => 'logoutLayout userLogin'
	));

	// Starting the form
	echo $this->Form->create(null, array(
		'url' => $this->here,
		'id' => 'loginForm',
		'class'	=> 'loginForm editForm niceForm'
	));

	// Classical login form
	echo $this->Html->tag('fieldset', null, array('id' => 'fieldsetClassic'));
	echo $this->Html->tag('legend', __d('caracole_users', 'Classic login form', true));

		// Login
		echo $this->Fastcode->input('name', array(
			'label' => __d('caracole_users', 'Login', true),
			'required' => true
		));

		// Pass
		echo $this->Fastcode->input('password', array(
			'label' => __d('caracole_users', 'Password', true),
			'required' => true,
			'after' => $this->Fastcode->link(
				__d('caracole_users', 'Log in using openId', true),
				'#fieldsetOpenId',
				array('class' => 'goToOpenId jsOn', 'icon' => 'openid')
			)
		));



	echo '</fieldset>';

	// OpenID login form
	echo $this->Html->tag('fieldset', null, array('id' => 'fieldsetOpenId', 'class' => 'jsOff'));
	echo $this->Html->tag('legend', __d('caracole_users', 'OpenId login form', true));

		// Open Id url
		echo $this->Fastcode->input('openid', __d('caracole_users', 'OpenID', true));

		// We create the list of providers
		$providerList = Configure::read('OpenId.providerList');
		$providerUl = '<ul class="tablecell">';
		foreach($providerList as $providerName => $providerOptions) {
			$providerUl.= $this->Html->tag('li', $this->Fastcode->link($providerName, $providerOptions['url'], array('class' => 'button action', 'icon' => $providerOptions['icon'])));
		}
		$providerUl.= '</ul>';
		$providerUl.= $this->Fastcode->link(
			__d('caracole_users', 'Back to classical login form', true),
			'#fieldsetClassic',
			array('class' => 'goToClassic', 'icon' => 'back')
		);


		// Providers
		echo $this->Fastcode->input('openid_providers', array(
			'label' => __d('caracole_users', 'Or choose a provider', true),
			'plain' => true,
			'value' => $providerUl,
			'div' => array('class' => 'input providers jsOn')
		));

	echo '</fieldset>';

	// Remember me
	echo $this->Fastcode->input('Options.is_remember', __d('caracole_users', 'Remember me', true));



	// Adding a submit button
	echo $this->Fastcode->div(
			$this->Fastcode->button(
			__d('caracole_users', 'Log in', true),
			array(
				'icon' => 'valid',
				'type' => 'submit',
				'class' => 'loginButton'
			)
		),
		array('class' => 'submit')
	);

	// Ending the form
	echo $this->Form->end();
