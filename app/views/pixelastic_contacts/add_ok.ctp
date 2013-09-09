<?php
	/**
	 *	Form correctly sent
	 **/
?>
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
	<div class="contactsMain span-16">
	<?php

		echo $this->Html->tag('h2', $this->Fastcode->link($pageTitle, $this->here, array('escape' => false)));

		echo $this->Fastcode->message(__("Thank you for your message, I'll strive to answer to every mail I get, so expect a fast answer !", true), 'success');


	?>
	</div>

	<div class="contactSecondary span-7 prepend-1 last">
		<?php
			// Contact sidebar
			echo $this->element('contact_sidebar');
		?>
	</div>
</div>
