<?php
	/**
	 *	Reorder items
	 *	Allow for reordering items in a list.
	 *	It heavily use Javascript, so the page should not be usable with javascript turned off
	 **/

	// Javascript warning
	echo $this->Fastcode->message(
		__d('caracole', 'Javascript is disabled in your browser. To be able to use the reorder feature, you must enable Javascript first.', true),
		'notice',
		array(
			'class' => 'jsOff'
		)
	);

	// We parse the url to find the controller and action
	$pageParams = Router::parse($this->here);

	// Starting the form
	echo $this->Form->create(null, array(
		'url' 		=> $this->here,
		'id' 		=> Inflector::classify($pageParams['controller']).Inflector::camelize($pageParams['action']).'Form',
		'class' 	=> 'jsOn reorder'
	));

	echo $this->Fastcode->message(
		__d('caracole', 'Drag and drop items to reorder them the way you want. Changes are saved automatically.', true),
		'information'
	);

	$modelName = $this->Fastcode->model();

	// List of items
	echo $this->Html->tag('ul', null, array('class' => 'itemList'));
	$index = 0;
	foreach($itemList as &$item) {
		echo $this->Html->tag('li',
			$this->Fastcode->input(
				'id_'.$item[$modelName]['id'],
				array(
					'type' => 'hidden',
					'value' => ++$index,
					'secureValue' => false
				)
			).$item[$modelName][$displayField]
		);
	}
	echo '</ul>';

	// Closing form
	echo $this->Form->end();
