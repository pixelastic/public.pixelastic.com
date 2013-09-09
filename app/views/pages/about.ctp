<?php
/**
 *	Displaying the home page
 **/

$this->set(array(
	'pageCssId' =>  'about',
	'title_for_layout' => $item['Page']['name']
));
?>

<div class="aboutLayout">
	<div class="aboutMain span-14">
		<?php
			echo $this->Html->tag('h2', $this->Fastcode->link($item['Page']['name'], $this->here, array('escape' => false)));

			echo $this->Fastcode->div($item['Page']['text'], 'text');


		?>
	</div>
	<div class="aboutSecondary prepend-2 span-8 last">
		<?php
			echo $this->Html->tag('h3', __("What I can do...", true));

			echo $this->Fastcode->message(
				$this->Html->tag('ul',
					$this->Html->tag('li',__('10 years experience of website building with PHP', true))
					.$this->Html->tag('li', __('Clean and simple HTML/CSS', true))
					.$this->Html->tag('li',__('Unobstrusive Javascript. Vanilla, jQuery or Backbone', true))
				),
				'notice'
			);

			echo $this->Html->tag('h3', __("What I'd like to do...", true));

			echo $this->Fastcode->message(
				$this->Html->tag('ul',
					$this->Html->tag('li', __('Teach and learn as part of a team', true))
					.$this->Html->tag('li',__('Learn more of Ruby and/or node', true))
					.$this->Html->tag('li',__('Working on one big project, fixing and improving it', true))
					.$this->Html->tag('li',__('TDD workflow and git as a team tool', true))
				),
				'notice'
			);

			echo $this->Html->tag('h3', __("I'd love to work for you if...", true));

			echo $this->Fastcode->message(
				$this->Html->tag('ul',
					$this->Html->tag('li',__('Your project is challenging', true))
					.$this->Html->tag('li',__('Your project as a social dimension', true))
					.$this->Html->tag('li',__('Your project as a gaming dimension', true))
				),
				'notice'
			);

			echo $this->Html->tag('h3', __("I'd hate to work for you if...", true));

			echo $this->Fastcode->message(
				$this->Html->tag('ul',
					$this->Html->tag('li',__('You need pixel-perfect compatibility', true))
					.$this->Html->tag('li',__('You need to interface with Facebook API', true))
					.$this->Html->tag('li',__('You do not use a versioning system', true))
				),
				'notice'
			);
		?>
	</div>
</div>
