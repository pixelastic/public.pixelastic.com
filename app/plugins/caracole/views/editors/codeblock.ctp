<?php
	/**
	 *	Add/edit a code block
	 *	Allow the user for specifying the language
	 **/

	echo $this->Fastcode->p(
		__d('caracole', 'Please select the corresponding language in the list below.', true)
	);

	// Starting the form
	echo $this->Form->create(null, array('class' => 'niceForm'));



	echo $this->Fastcode->input('language', array(
			'label' => __d('caracole', 'Language', true),
			'type' => 'select',
			'options' => array(
				'php' => __d('caracole', 'PHP', true),
				'js' => __d('caracole', 'Javascript', true),
				'css' => __d('caracole', 'CSS', true),
				'html' => __d('caracole', 'HTML', true),
				'apache' => __d('caracole', 'Apache', true),
				'sql' => __d('caracole', 'SQL', true),
				'sh' => __d('caracole', 'Bash', true),
				'xml' => __d('caracole', 'XML', true),
				'ini' => __d('caracole', '.ini', true),
				'ahk' => __d('caracole', 'AutoHotKey', true),

				'cf' => __d('caracole', 'Coldfusion', true),
			),
			'tabindex' => $this->Fastcode->tabindex(1000)
	));

	// Adding Update and cancel buttons
	echo '<div class="submit">';
		echo $this->Fastcode->button(
			__d('caracole', 'Cancel', true),
			array(
				'icon' => 'cancel',
				'class' => 'cancel action',
				'tabindex' => $this->Fastcode->tabindex(1000)
			)
		);

		echo $this->Fastcode->button(
			__d('caracole', 'Update', true),
			array(
				'icon' => 'valid',
				'type' => 'submit',
				'class' => 'update',
				'tabindex' => $this->Fastcode->tabindex(1000)
			)
		);

		echo $this->Fastcode->button(
			__d('caracole', 'Remove', true),
			array(
				'class' => 'remove important',
				'tabindex' => $this->Fastcode->tabindex(1000)
			)
		);

	echo '</div>';

	// Ending the form
	echo $this->Form->end();