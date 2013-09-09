<?php
	/**
	 *	OpenId redirect.
	 *	This is an intermediate page. The goal is that it contains a form containing all the needed openId informations.
	 *	We need to auto-submit it as fast as we can using Javascript. But if the user do not have Javascript, we need to make a nice
	 *	button
	 *
	 *	TODO : Adding a js class to the html element in javascript and targeting css styles using it
	 **/


?>
<html>
	<head>
		<title><?php echo $title_for_layout; ?></title>
		<script>document.documentElement.className+=' js';</script>
	</head>
	<body>
		<style>
			.cake-sql-log,.jsOn,.js .jsOff, .js form { display:none; }
			.jsOff, .js .jsOn { display:block; }
		</style>

		<p class="jsOn"><?php __d('caracole_users', 'Redirecting to OpenId provider, please wait...'); ?></p>
		<p class="jsOff"><?php __d('caracole_users', 'Please click on the Continue button to continue the OpenId authentication process.'); ?></p>
		<?php echo $formHtml; ?>
		<?php
			// Submitting the form
			echo $this->Javascript->codeBlock('document.getElementById("'.$formId.'").submit();');
		?>
	</body>
</html>
<?php die(); ?>
