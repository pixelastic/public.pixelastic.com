<?php
/**
 *	IconHelper
 *	Used to display an icon using the CSS Sprite method. The icon is in fact a span with a background image set.
 **/
 class IconHelper extends AppHelper {
	// Helpers used in this helper
	var $helpers = array(
		'Html',
	);

	/**
	 *	icon
	 *	Return the html code for displaying an icon stored in the CSS Sprite image
	 *	Will create a <span> with background image centered on the specified icon
	 *
	 *	@param	mixed	$icons	The name of the icon or an array of names.
	 *	@return string	Html code of a formatted span
	 */
	function icon($icons = null, $options = array()) {
		// At least one icon is needed
		if (empty($icons)) return false;
		// If one icon specified, we convert it to an array
		if (is_string($icons)) $icons = array($icons);

		// Default options
		$options = array_merge(array(
			'class' => ''
		), $options);

		// We get an array of classnames
		$class = array_values(array_filter(explode(' ', $options['class'])));
		// We add the icons
		$class[] = 'icon';
		foreach($icons as &$icon) {
			$class[] = 'icon'.ucfirst($icon);
		}
		$options['class'] = implode(' ', $class);

		// Returning a span with icon class added
		return $this->Html->tag('span','', $options);
	}
}
