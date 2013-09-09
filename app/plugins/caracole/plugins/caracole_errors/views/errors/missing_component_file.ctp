
<?php $this->set('title_for_layout', __d('caracole_errors','Missing Component File', true)); ?>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php __d('caracole_errors','The component file was not found.'); ?>
</p>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','Create the class %s in file: %s', true), '<em>' . $component . 'Component</em>', APP_DIR . DS . 'controllers' . DS . 'components' . DS . $file); ?>
</p>
<pre class="message notice">
&lt;?php
class <?php echo $component;?>Component extends Object {<br />

}
?&gt;
</pre>
