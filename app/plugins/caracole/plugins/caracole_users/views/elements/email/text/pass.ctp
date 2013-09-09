<?php
	echo sprintf(__d('caracole_users', 'Hello %1$s', true), $item['first_name']);
?>


<?php
	echo __d('caracole_users', 'You forgot your password and asked for a new one. All you have to do is click on the link below :', true);
?>

<?php
	echo $url
?>


<?php
	echo __d('caracole_users', 'If you never asked for a new password, you can safely ignore this email.', true);
?>


<?php echo __d('caracole_users', "Regards,", true); ?>

<?php echo sprintf(__d('caracole_users', 'The %1$s team', true), $siteName); ?>
