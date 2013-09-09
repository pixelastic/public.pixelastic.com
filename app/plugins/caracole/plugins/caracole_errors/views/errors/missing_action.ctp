
<?php $this->set('title_for_layout', sprintf(__d('caracole_errors','Missing Method in %s', true), $controller)); ?></h2>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','The action %1$s is not defined in controller %2$s', true), '<em>' . $action . '</em>', '<em>' . $controller . '</em>'); ?>
</p>
<p class="message error">
	<strong><?php __d('caracole_errors','Error'); ?>: </strong>
	<?php printf(__d('caracole_errors','Create %1$s%2$s in file: %3$s.', true), '<em>' . $controller . '::</em>', '<em>' . $action . '()</em>', APP_DIR . DS . 'controllers' . DS . Inflector::underscore($controller) . '.php'); ?>
</p>
<pre class="message notice">
&lt;?php
class <?php echo $controller;?> extends AppController {

	var $name = '<?php echo $controllerName;?>';

<strong>
	function <?php echo $action;?>() {

	}
</strong>
}
?&gt;
</pre>
