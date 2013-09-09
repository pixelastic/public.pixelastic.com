<?php
	/**
	 *	Admin menu
	 *	The content of the admin menu can be edited in app/config/config.php
	 **/
	$adminMenu = Configure::read('Admin.menu');

	// We add the advanced admin menu entries
	$activeUser['User']['is_advanced'] = true;
	if (!empty($activeUser['User']['is_advanced'])) {
		$adminMenu[] = array(
			'label' => __d('caracole', 'Advanced settings', true),
			'icon' => 'Setting',
			'url' => array('admin' => true, 'plugin' => 'caracole_file_systems', 'controller' => 'file_systems', 'action' => 'index'),
			'links' => array(
				array(
					'label' => __d('caracole', 'Users', true),
					'url' => array('plugin' => 'caracole_users', 'controller' => 'users', 'action' => 'index')
				),
				array(
					'label' => __d('caracole', 'Documents', true),
					'url' => array('plugin' => 'caracole_documents', 'controller' => 'documents', 'action' => 'index')
				),
				array(
					'label' => __d('caracole', 'Images', true),
					'url' => array('plugin' => 'caracole_documents', 'controller' => 'images', 'action' => 'index')
				),
				array(
					'label' => __d('caracole', 'Errors', true),
					'url' => array('plugin' => 'caracole_errors', 'controller' => 'caracole_errors', 'action' => 'index')
				),
				array(
					'label' => __d('caracole', 'Icons', true),
					'url' => array('plugin' => 'caracole_icons', 'controller' => 'icons', 'action' => 'index')
				),
				/*
				 array(
					'label' => __d('caracole', 'Tools', true),
					'url' => array('plugin' => 'caracole_tools', 'controller' => 'tools', 'action' => 'index')
				)
				*/
			)
		);

	}



	// No menu set, we have nothing to display
	if (empty($adminMenu)) {
		return;
	}
?>
		<ul>
		<?php
			foreach($adminMenu as &$menu) {
				// Adding a class to parent li
				echo $this->Html->tag(
					'li',
					null,
					array(
						'class' => empty($menu['links']) ? null : 'parent'
					)
				);

				// Getting default url
				$menu['url'] = array_merge(array('admin' => 1, 'action' => 'index', 'plugin' => null ), $menu['url']);

				// Displaying the main link
				echo $this->Fastcode->link($menu['label'], $menu['url'],array('icon' => $menu['icon'], 'class' => 'button'));

				// Displaying the list of sub-links
				if (!empty($menu['links'])) { ?>
					<ul>
					<?php foreach($menu['links'] as &$link) { ?>
						<li><?php echo $this->Fastcode->link($link['label'], $link['url']); ?></li>
					<?php } ?>
					</ul>
				<?php
				}
				echo '</li>';
			}
		?>
</ul>
