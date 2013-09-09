

<?php $this->set('title_for_layout', __d('caracole_errors','Missing View', true)); ?>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','The view for %1$s%2$s was not found.', true), '<em>' . $controller . 'Controller::</em>', '<em>' . $action . '()</em>'); ?>
</p>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','Confirm you have created the file: %s', true), $file); ?>
</p>
