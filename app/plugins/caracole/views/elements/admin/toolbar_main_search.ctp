<?php
	/**
	 *	toolbar_main_search.ctp
	 *	Adding a search form on the main header of the admin pages
	 *
	 *	The search part can be either a simple input + button form, or a more complex dropdown with multiple filter options
	 *
	 *	Special options can be passed :
	 *
	 *		- mainField : Default to the model displayField. This is the field upon which the search and autocomplete feature
	 *			will be based
	 *		- autocomplete : Default to true, but can be turned off
	 **/
?>
<li>
	<?php
		$modelName = Inflector::classify($this->params['controller']);

		// Starting the form
		echo $this->Form->create(null, array(
			'url' => array('action' => 'search'),
			'class'		=> 'niceForm searchForm',
			'id' => $modelName.'SearchForm'
		));

		// Form input
		echo $this->Fastcode->input($options['mainField'], array(
			'label' => __d('caracole', 'Search', true),
			'div' => 'input searchField',
			'type' => 'text',
			'class' => empty($options['autocomplete']) ? null : 'autocomplete'
		));

		// We add an hidden field indicating what field is supposed to be the primary one. A broad search (using LIKE) is going to be added on it
		echo $this->Fastcode->input('Options.mainFieldName', array(
			'type' => 'hidden',
			'value' => $modelName.'.'.$options['mainField']
		));

		// Simple submit button if no advanced fields
		if (empty($options['advancedFields'])) {
			echo $this->Fastcode->button(
				sprintf('<em>%1$s</em>', __d('caracole', 'Search', true)),
				array(
					'escape' => false,
					'icon' => 'search',
					'type' => 'submit',
					'class' => 'submit'
				)
			);
		} else {
			// Advanced fields
			echo $this->Html->tag('div', null, array('class' => 'advancedSearch'));

				// Submit button and open handler
				echo '<div class="advancedSearchButtonContainer button">';
					// Submit
					echo $this->Fastcode->button(
						sprintf('<em>%1$s</em>', __d('caracole', 'Search', true)),
						array(
							'escape' => false,
							'class' => 'searchButton',
							'icon' => 'search',
							'type' => 'submit'
						)
					);
					// Open advanced
					echo $this->Fastcode->link(
						$this->Html->tag('em', __d('caracole', "Advanced search", true)),
						'#advancedSearch',
						array('class' => 'toggleAdvancedSearchDropdown toggleAdvancedSearchDropdownTop', 'id' => 'toggleAdvancedSearchDropdown', 'escape' => false)
					);
				echo '</div>';

				// Advanced fieldset
				echo $this->Html->tag('fieldset', null, array('id' => 'advancedSearchDropdown', 'class' => 'advancedSearchDropdown'));
					// Close
					echo $this->Fastcode->link(
						$this->Html->tag('em', __d('caracole', 'Close', true)),
						'#advancedSearch',
						array('icon' => 'search', 'class' => 'toggleAdvancedSearchDropdown toggleAdvancedSearchDropdownBottom')
					);
					// Title
					echo $this->Html->tag('h3', __d('caracole', 'Advanced search', true));

					// Fields
					foreach($options['advancedFields'] as $fieldName => $fieldOptions) {
						echo $this->Fastcode->input($fieldName, $fieldOptions);
					}

					// Submit
					echo $this->Fastcode->button(__d('caracole', 'Search', true),
						array('type' => 'submit','class' => 'button silver small')
					);
				echo '</fieldset>';


			echo '</div>';
		}


		// Ending the form
		echo $this->Form->end();
	?>
</li>
