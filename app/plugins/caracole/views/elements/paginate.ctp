<?php
	/**
	 *	Pagination links
	 **/

	// Default options
	if (empty($paginatorOptions)) $paginatorOptions = array();
	$paginatorOptions = Set::merge(array(
		'numbers' => array(
			'modulus' => 3,
			'separator' => '',
			'first' => 2,
			'last' => 2
		)
	), $paginatorOptions);
	// Getting numbers options
	$paginatorOptionsNumbers = $paginatorOptions['numbers'];
	unset($paginatorOptions['numbers']);
	//Applying options
	$this->Paginator->options($paginatorOptions);

	$params = $this->Paginator->params();
	// Stopping if no pagination needed
	if ($params['pageCount']<=1) return;

	echo $this->Html->tag(
		'div',
		// Previous
		(($this->Paginator->hasPrev()) ?
			$this->Paginator->prev(__d('caracole', 'Previous', true)) :
			$this->Html->tag('span', __d('caracole', 'Previous', true), array('class' => 'prevDisabled'))
		).

		// Pages
		$this->Paginator->numbers($paginatorOptionsNumbers).

		// Next
		(($this->Paginator->hasNext()) ?
			$this->Paginator->next(__d('caracole', 'Next', true)) :
			$this->Html->tag('span', __d('caracole', 'Next', true), array('class' => 'nextDisabled'))
		),
		array('class' => 'paginate')
	);
