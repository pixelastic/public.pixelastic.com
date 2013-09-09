
<?php $this->set('title_for_layout', __d('caracole_errors','Missing Helper File', true)); ?>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','The helper file %s can not be found or does not exist.', true), APP_DIR . DS . 'views' . DS . 'helpers' . DS . $file); ?>
</p>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','Create the class below in file: %s', true), APP_DIR . DS . 'views' . DS . 'helpers' . DS . $file); ?>
</p>
<pre class="message notice">
&lt;?php
class <?php echo $helperClass;?> extends AppHelper {

}
?&gt;
</pre>
