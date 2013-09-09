<?php $this->set('title_for_layout',__d('caracole_errors','Missing Model', true)); ?>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','<em>%s</em> could not be found.', true), $model); ?>
</p>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','Create the class %s in file: %s', true), '<em>' . $model . '</em>', APP_DIR . DS . 'models' . DS . Inflector::underscore($model) . '.php'); ?>
</p>
<pre class="message notice">
&lt;?php
class <?php echo $model;?> extends AppModel {

	var $name = '<?php echo $model;?>';

}
?&gt;
</pre>
