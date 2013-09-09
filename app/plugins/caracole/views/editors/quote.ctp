<?php
	/**
	 *	Add/edit a quote
	 *	Allow the user to add author and URI reference
	 **/

	echo $this->Fastcode->p(
		__d('caracole', 'You can specify the author and original source of your quote. Click on the remove button to remove the whole quote styling.', true)
	);

	// Starting the form
	echo $this->Form->create(null, array('class' => 'niceForm'));



	echo $this->Fastcode->input('author', array(
			'label' => __d('caracole', 'Author', true),
			'tabindex' => $this->Fastcode->tabindex(1000)
	));
	echo $this->Fastcode->input('source', array(
		'label' => __d('caracole', 'Source', true),
		'tabindex' => $this->Fastcode->tabindex(1000),
		'help' => __d('caracole', 'Enter the URL of the original source.', true)
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