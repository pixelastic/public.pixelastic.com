<?php
	/**
	 *	Edit HTML Source
	 *	Allow admin adding/editing of new items in the database
	 **/

	// Starting the form
	echo $this->Form->create(null, array('class' => 'niceForm'));

	echo $this->Fastcode->input('source', array(
		'type' => 'textarea',
		'label' => false,
		'div' => false,
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

	echo '</div>';

	// Ending the form
	echo $this->Form->end();