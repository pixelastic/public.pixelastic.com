
<?php $this->set('title_for_layout', __d('caracole_errors','Missing Database Table', true)); ?>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','Database table %1$s for model %2$s was not found.', true), '<em>' . $table . '</em>',  '<em>' . $model . '</em>'); ?>
</p>
