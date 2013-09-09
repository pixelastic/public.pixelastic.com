
<?php $this->set('title_for_layout', __d('caracole_errors','Missing Component Class', true)); ?>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','Component class %1$s in %2$s was not found.', true), '<em>' . $component . 'Component</em>', '<em>' . $controller . 'Controller</em>'); ?>
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
