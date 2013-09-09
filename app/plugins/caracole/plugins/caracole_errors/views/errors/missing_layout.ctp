
<?php $this->set('title_for_layout', __d('caracole_errors','Missing Layout', true)); ?>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','The layout file %s can not be found or does not exist.', true), '<em>' . $file . '</em>'); ?>
</p>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','Confirm you have created the file: %s', true), '<em>' . $file . '</em>'); ?>
</p>
