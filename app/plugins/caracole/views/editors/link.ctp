<?php
	/**
	 *	Add/edit a link
	 *	Allow the author to set the href, title and target="_blank"
	 **/

	echo $this->Fastcode->p(
		__d('caracole', 'You can add a link to a specified page. Do not forget to set a descriptive title if the text of your link is not explicit enough.', true)
	);

	// Starting the form
	echo $this->Form->create(null, array('class' => 'niceForm'));

	echo $this->Fastcode->input('href', array(
		'label' => __d('caracole', 'Url', true),
		'tabindex' => $this->Fastcode->tabindex(1000)
	));
	echo $this->Fastcode->input('title', array(
		'label' => __d('caracole', 'Title', true),
		'tabindex' => $this->Fastcode->tabindex(1000),
		'help' => __d('caracole', 'You can keep it empty if the text of your link is descriptive enough.', true)
	));
	echo $this->Fastcode->input('is_blank', array(
		'label' => __d('caracole', 'Open in new window', true),
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