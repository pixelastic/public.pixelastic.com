<?php
	/**
	 *	Secondary toolbar
	 *	Used to display less-prominent buttons for actions. Mostly used in sub-pages to get back to the index list
	 **/
	// Default, pre-saved, actions
	$defaultActions = array(
		'back' => array(
			'label' 	=> __d('caracole', 'Back', true),
			'url' 		=> array('action' => 'index'),
			'icon'		=> 'back'
		),
		'reorder' => array(
			'label' 	=> __d('caracole', 'Reorder', true),
			'url' 		=> array('action' => 'reorder'),
			'icon'		=> 'reorder'
		)
	);
?>

<ul class="secondaryToolbar">
	<?php
		foreach($secondaryToolbar as $name => &$options) {
			// If the key is numeric, we use the value as the key
			if (is_numeric($name)) {
				$name = $options;
				$options = array();
			}
			// If options is a string, we use it as a label
			if (is_string($options)) {
				$options = array('label' => $options);
			}
			// We add default values
			if (array_key_exists($name, $defaultActions)) {
				$options = Set::merge($defaultActions[$name], $options);
			}

			// Taking the label and the url from the options
			$label = $options['label'];
			unset($options['label']);
			$url = $options['url'];
			unset($options['url']);

			// We convert the url by replacing any {Model.field} by its value
			foreach($url as $key => &$value) {
				// Skipping if do not match
				if (!preg_match('/{([a-zA-Z]+)\.([a-zA-Z0-9_]+)}/', $value, $matches)) continue;
				// Replacing by value in $this->data
				$value = $this->data[$matches[1]][$matches[2]];
			}

			// Displaying the link
			echo $this->Html->tag('li',	$this->Fastcode->link($label, $url, $options));

		}
	?>
</ul>
