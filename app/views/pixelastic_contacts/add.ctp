<?php
	/**
	 *	contact form
	 **/
	$pageTitle = __('Contact', true);
	$this->set(array(
		'pageCssId' => 'contact',
		'title_for_layout' => $pageTitle
	));
?>
<div class="contactLayout">
	<div class="contactMain span-15">
	<?php

		echo $this->Html->tag('h2', $this->Fastcode->link($pageTitle, $this->here, array('escape' => false)));

		echo $this->Form->create('PixelasticContact', array(
			'url' => $this->Fastcode->url(array('action' => 'add')),
			'class'		=> 'niceForm contactForm',
			'id' => 'PixelasticContactAddForm'
		));

		// Flash message
		if (!empty($this->validationErrors)) {
			echo $this->Fastcode->message($this->Fastcode->validationErrors($this->validationErrors), 'error');
		} else {
			//echo $this->Fastcode->message(__("Don't hesitate to use the form to ask any question or just to say Hi, it's always welcome.", true));
		}

		// Name
		echo $this->Fastcode->input('name', array(
			'label' => __('Name', true),
			'required' => true
		));

		// Spam bait
		echo $this->Antispam->input();

		// Real email field
		echo $this->Fastcode->input('calirhoe', array(
			'label' => __('Email', true),
			'help' => __('Will not be published, but I need it to contact you back.', true),
			'required' => true
		));

		// Is that a project ?
		echo $this->Fastcode->input('Options.is_project', array(
			'label' => __('This contact is regarding a specific project', true),
			'type' => 'checkbox'
		));

		// Project fieldset
		echo $this->Html->tag('fieldset', null, array('class' => 'fielsetProject', 'id' => 'fieldsetProject'));
			echo $this->Html->tag('legend', __('Project details', true));
			// Timeframe
			echo $this->Fastcode->input('timeframe', array(
				'label' => __('Timeframe', true),
				'type' => 'select',
				'options' => array(
					__('Yesterday', true) => __('This is really urgent. In fact it should already be finished !', true),
					__('This week', true) => __('This is pretty urgent, I need it ASAP.', true),
					__('This month', true) => __('We have a set deadline, and it need to be done by then.', true),
					__('In a couple of months', true) => __("I'm not in a particular hurry, but the sooner the better.", true),
					__('In 6 months', true) => __("There is no immediate need, but planning ahead doesn't hurt.",true),
					__('Someday', true) => __("I have absolutely no pressure, I just want to ask some general questions.", true)
				),
				'value' => __('This month', true),
				'help' => __('When should the project be finished, ideally ?', true)
			));

			// Budget
			echo $this->Fastcode->input('budget', array(
				'label' => __('Budget', true),
				'type' => 'select',
				'options' => array(
					__('No idea', true) 		=> __("I have absolutely no idea, you tell me !", true),
					__('Nothing', true) 		=> __("I think my idea is so great you'll want to do it for free !", true),
					__('~500€', true) 			=> __("I just want you to code some HTML/CSS for me.", true),
					__('~1000€', true) 			=> __("I need a small website, a dozen pages or so, nothing fancy.", true),
					__('~3000€', true) 			=> __("I need a custom website with some special features.", true),
					__('~6000€', true) 			=> __("I need a pretty big website and have some strong requirements.", true),
					__('10000€ and more', true) => __("I'll ask you to do something that have never been done before.", true),
				),
				'value' => __('~1000€', true),
				'help' => __('How much are you willing to invest in the project, approximatively ?', true)
			));

		echo '</fieldset>';

		// Text
		echo $this->Fastcode->input('text', array(
			'label' => __('Message', true),
			'type' => 'textarea'
		));

		// Submit
		echo $this->Fastcode->div(
				$this->Fastcode->button(
				__('Send message', true),
				array(
					'type' => 'submit',
				)
			), array('class' => 'submit')
		);

		// Ending the form
		echo $this->Form->end();
	?>
	</div>

	<div class="contactSecondary span-7 prepend-2 last">
		<?php
			// Contact sidebar
			echo $this->element('contact_sidebar');
		?>
	</div>
</div>
