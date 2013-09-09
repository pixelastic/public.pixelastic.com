<?php
	/**
	 *	Pagination links
	 **/

	// Options
	if (!empty($paginatorOptions)) {
		$this->Paginator->options($paginatorOptions);
	}

	echo $this->Html->tag(
		'div',
		// Previous and first
		$this->Paginator->first(
			'<<',
			array(
				'after' => ($this->Paginator->hasPrev()) ? $this->Paginator->prev('<') : null,
				'class' => 'first'
			)
		).
		// Pages
		$this->Paginator->numbers(array(
			'modulus' => 5,
		)).
		// Last and next
		$this->Paginator->last(
			'>>',
			array(
				'before' => ($this->Paginator->hasNext()) ? $this->Paginator->next('>') : null,
				'class' => 'last'
			)
		),
		array('class' => 'paginate')
	);
