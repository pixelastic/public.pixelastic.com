
<?php $this->set('title_for_layout', __d('caracole_errors','Missing Database Connection', true)); ?>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','%s requires a database connection', true), $model); ?>
</p>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','Confirm you have created the file : %s.', true), APP_DIR . DS . 'config' . DS . 'database.php'); ?>
</p>
