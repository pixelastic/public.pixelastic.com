<?php
	/**
	 *	Add/edit an abbreviation
	 *	We can define the title
	 **/

	 echo $this->Fastcode->p(
		__d('caracole', 'Please enter the abbreviation definition below.', true)
	);

	// Starting the form
	echo $this->Form->create(null, array('class' => 'niceForm'));



	echo $this->Fastcode->input('title', array(
			'label' => __d('caracole', 'Definition', true),
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