
<?php $this->set('title_for_layout', sprintf(__d('caracole_errors','Private Method in %s', true))); ?>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','%s%s cannot be accessed directly.', true), '<em>' . $controller . '::</em>', '<em>' . $action . '()</em>'); ?>
</p>
