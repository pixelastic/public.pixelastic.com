<?php
/**
 *	Admin index
 *	Display the full paginated list of items in a sortable table.
 **/

// We parse the url to find the controller and action
$pageParams = Router::parse($this->here);
$modelName = Inflector::classify($pageParams['controller']);

// Starting the form
echo $this->Form->create(null, array(
	'url' 		=> array('action' => 'admin_apply'),
	'class'		=> 'formIndex',
	'id' 		=> $modelName.'ApplyForm'
));

// Getting the model name
$modelName = $this->Form->model();

// Adding links to select items and select menu to apply action to them
echo $this->Html->div('itemListOptions wrapper', null);
	// Select items
	echo $this->Html->div(
		'select jsOn',
		// Label
		$this->Html->tag('span', __d('caracole', 'Select', true), array('class' => 'label')).
		// Selection links
		$this->Html->tag(
			'ul',
			$this->Html->tag('li', $this->Fastcode->link(__d('caracole', 'All', true), '#', array('id' => 'selectAll'))).
			$this->Html->tag('li', $this->Fastcode->link(__d('caracole', 'None', true), '#', array('id' => 'selectNone'))).
			$this->Html->tag('li', $this->Fastcode->link(__d('caracole', 'Toggle', true), '#', array('id' => 'selectToggle'))),
			array('class' => 'inline')
		)
	);

	// Apply action to selection
	echo $this->Html->div(
		'apply',
		// Select options
		$this->Fastcode->input(
			'Options.action',
			array(
				'label' => false,
				'div'	=> false,
				'type' => 'select',
				'options' => $actionOptions
			)
		).
		$this->Fastcode->button(
			__d('caracole', 'Apply', true),
			array(
				'type' => 'submit',
				'class' => 'action'
			)
		)
	);
echo '</div>';

// Starting the table
echo $this->Html->tag('table', null, array('class' => 'itemList'));
?>
<!-- Thead -->
<thead>
	<tr>
		<th class="checkbox"></th>
		<?php
			/**
			 *	Table headers.
			 *	We display the list of table header. Each one will contain a link to change the order of the display
			 *	Each key is the field to apply the order on, as well as the th class
			 *	The key is the displayed label.
			 *	One can also display virtual fields (by hooking on the view), and in this case should pass an
			 *	array('order' => false, 'label' => 'Label') to disable the reorder
			 **/
			foreach($headers as $fieldName => $options) {
				if (is_string($options)) $options = array('label' => $options);
				$options = array_merge(array('order' => true), $options);

				// Header cell
				echo $this->Html->tag(
					'th',
					empty($options['order']) ? $options['label'] : $this->Paginator->sort($options['label'], $fieldName),
					array(
						'class' => str_replace('.', '_', $fieldName)
					)
				);
			}
		?>
	</tr>
</thead>
<!-- /Thead -->
<!-- Tfoot -->
<tfoot>
	<tr>
		<td colspan="<?php echo count($headers)+1; ?>">
		<?php
			// Paginating results
			echo $this->element('paginate', array('plugin' => 'caracole'));
		?>
		</td>
	</tr>
</tfoot>
<!-- /Tfoot -->
<!-- Tbody -->
<tbody>
	<?php
		// For each item
		foreach($itemList as $index => &$item) {
			// Rows options
			$rowOptions = array('class' => empty($item[$modelName]['is_draft']) ? null : 'isDraft');
			// Starting row
			echo $this->Html->tag('tr', null, $rowOptions);

			// Adding the checkbox on first cell (<tr><input /><label /></tr>)
			echo $this->Html->tag(
				'td',
				$this->Fastcode->input(
					$modelName.'.'.$item[$modelName]['id'].'.checked',
					array(
						'type' => 'checkbox',
						'value' => true,
						'label' => '#'.$item[$modelName]['id'],
						'div' => false
					)
				),
				array('class' => 'checkbox')
			);

			// Flattening item for better access
			$flattenItem = Set::flatten($item);

			// The list of items
			foreach($headers as $fieldName => $headerLabel) {
				// If an explicit label is set, we will use it. Otherwise we use the item value
				$linkOptions = array();
				if (!empty($flattenItem[$fieldName.'.label'])) {
					// Using the label, and passing the options in array form
					$label = $flattenItem[$fieldName.'.label'];
					$linkOptions = Set::extract($fieldName, $item);
					unset($linkOptions['label']);
				} else {
					$label = $flattenItem[$fieldName];
				}

				// Adding a link for editing this item
				echo $this->Html->tag(
					'td',
					$this->Fastcode->link(
						$label,
						array('action' => 'edit', 'id' => $item[$modelName]['id']),
						$linkOptions
					),
					array(
						'class' => str_replace('.', '_', $fieldName)
					)
				);
			}

			// Ending row
			echo '</tr>';
		}
	?>
</tbody>
<!-- /Tbody -->
</table>
<?php

echo $this->Form->end();
