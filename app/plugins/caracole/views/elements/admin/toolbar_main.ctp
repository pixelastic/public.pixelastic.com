<?php
	/**
	 *	Main toolbar
	 *	Displayed on top of the admin pages, near the title.
	 **/
	// Default, pre-saved, actions
	$defaultActions = array('add', 'search');


?>
<ul class="mainToolbar tablecell">
	<?php
	$mainToolbar = array_reverse($mainToolbar);
		foreach($mainToolbar as $name => &$options) {
			// If the key is numeric, we use the value as the key
			if (is_numeric($name)) {
				$name = $options;
				$options = array();
			}
			// If options is a string, we use it as a label
			if (is_string($options)) {
				$options = array('label' => $options);
			}
			// Skipping empty keys
			if ($options===false) continue;
			// Using element for default actions
			if (in_array($name, $defaultActions)) {
				echo $this->element('admin/toolbar_main_'.$name, array('plugin' => 'caracole', 'options' => $options));
				continue;
			}

			// Otherwise, we just display a link
			$label = $options['label'];
			unset($options['label']);
			$url = $options['url'];
			unset($options['url']);

			echo $this->Html->tag('li',	$this->Fastcode->link($label, $url, $options));

		}
	?>
</ul>
