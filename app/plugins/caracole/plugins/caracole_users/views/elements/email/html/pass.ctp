<?php
	echo $this->Fastcode->p(sprintf(__d('caracole_users', 'Hello %1$s', true), $item['first_name']));
?>

<?php
	echo $this->Fastcode->p(sprintf(
		__d('caracole_users', 'You forgot your password and asked for a new one. All you have to do is following %1$s.', true),
		$this->Fastcode->link(
			__d('caracole_users', 'this link', true),
			$url,
			array('title' => 'Follow this link to set a new password')
		)
	));
?>

<?php
	echo $this->Fastcode->p(__d('caracole_users', 'If you never asked for a new password, you can safely ignore this email.', true));
?>

<?php echo $this->Fastcode->p(__d('caracole_users', "Regards,", true)); ?>
<?php echo $this->Fastcode->p(sprintf(__d('caracole_users', 'The %1$s team', true), $siteName)); ?>
