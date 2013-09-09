<?php
	/**
	 *	Adding a new comment
	 **/

		echo $this->Form->create('Comment', array(
			'url' => array('action' => 'add'),
			'class'		=> 'niceForm commentForm',
			'id' => 'CommentAddForm'
		));

		// Flash message
		if (!empty($this->validationErrors)) {
			echo $this->Fastcode->message($this->Fastcode->validationErrors($this->validationErrors), 'error');
		}

		// Post id
		echo $this->Fastcode->input('post_id', array(
			'type' => 'hidden',
			'value' => $post_id
		));

		// Author
		echo $this->Fastcode->input('author', array(
			'label' => __d('caracole_blog', 'Name', true),
			'required' => true
		));

		// Spam bait
		echo $this->Antispam->input();

		// Real email field
		echo $this->Fastcode->input('calirhoe', array(
			'label' => __d('caracole_blog', 'Email', true),
			'help' => __d('caracole_blog', 'Will not be published', true),
			'required' => true
		));

		// Website
		echo $this->Fastcode->input('website', array(
			'label' => __d('caracole_blog', 'Website', true)
		));

		// Comment
		echo $this->Fastcode->input('text', array(
			'label' => __d('caracole_blog', 'Comment', true),
			'type' => 'textarea'
		));

		// Remember me
		echo $this->Fastcode->input('Options.is_remember', array(
			'label' => __d('caracole_blog', 'Remember me', true),
			'type' => 'checkbox'
		));

		echo '<div class="submit">';
			// Preview
			echo $this->Fastcode->button(
				__d('caracole_blog', 'Preview', true),
				array(
					'type' => 'button',
					'class' => 'jsOn preview'
				)
			);

			// Submit
			echo $this->Fastcode->button(
				__d('caracole_blog', 'Add comment', true),
				array(
					'type' => 'submit',

				)
			);

		echo '</div>';



		// Ending the form
		echo $this->Form->end();