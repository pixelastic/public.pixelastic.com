
<?php $this->set('title_for_layout', __d('caracole_errors','Missing Behavior File', true)); ?>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','The Behavior file %s can not be found or does not exist.', true), APP_DIR . DS . 'models' . DS . 'behaviors' . DS . $file); ?>
</p>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','Create the class below in file: %s', true), APP_DIR . DS . 'models' . DS . 'behaviors' . DS . $file); ?>
</p>
<pre class="message notice">
&lt;?php
class <?php echo $behaviorClass;?> extends ModelBehavior {

}
?&gt;
</pre>
