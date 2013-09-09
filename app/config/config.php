<?php
/**
 *	Default app settings.
 *	This define your app settings. All settings defined here will overwrite default settings (both Cake and Caracole).
 **/
CaracoleConfigure::write(array(
	/**
	 *	Site
	 *	General website informations
	 *
	 *	@name : 		Your site name. Used as the default site title
	 *	@baseline : 	Your site baseline, used as the default meta description
	 *	@id :			A unique string identifying your site. Used to keep various Caracole implementations apart.
	 **/
	'Site' => array(
	   'name' 		=> __('Pixelastic', true),
	   'baseline'	=> __("Freelance web developer", true),
	   'id' 		=> 'pixelastic',
   ),

	/**
	 *	Database
	 *	Database connection credentials. You have to define two sets of credentials for both your development and
	 *	production environment. Corresponding values will be picked accordingly.
	 *
	 *	Please refer to database.php for further details on the keys.
	 **/
   'Database' => array(
	   'prod' => array(
		   'default' => array(
				'driver' 	=> 'mysql',
				'persistent' => false,
				'host' 		=> 'mysql.pixelastic.com',
				'port' 		=> '',
				'login' 		=> 'xxxxxxxx',
				'password' 	=> 'xxxxxxxx',
				'database' 	=> 'xxxxxxxx',
				'schema' 	=> '',
				'prefix' 	=> '',
				'encoding' 	=> 'utf8'
			),
		   'imap' => array(
				'server' => 'imap.gmail.com',
				'port' => '993',
				'type' => 'ssl',
				'login' => 'xxxxxxxx@xxxxxxxx.fr',
				'pass' => 'xxxxxxxx'
		   )
	   ),
	   'dev' => array(
		   'default' => array(
				'driver' 	=> 'mysql',
				'persistent' => false,
				'host' 		=> 'localhost',
				'port' 		=> '',
				'login' 		=> 'root',
				'password' 	=> '',
				'database' 	=> 'yourdatabase',
				'schema' 	=> '',
				'prefix' 	=> '',
				'encoding' 	=> 'utf8'
			),
		   'imap' => array(
				'server' => 'imap.gmail.com',
				'port' => '993',
				'type' => 'ssl',
				'login' => 'xxxxxxxx@xxxxxxxx.fr',
				'pass' => 'xxxxxxxx'
		   )
	   )
   ),

   /**
    *	FTP
    *	FTP connection credentials. The FTP is used by Caracole to set correct chmod rights on folders as well as help
    *	in the Caracole upgrade process.
    *
    *	@server : 	FTP server address
    *	@port :		FTP port
    *	@login : 	FTP Login
    *	@pass :		FTP pass
    *	@root :		The relative pass to access the app/ directory of your app
    **/
	/*
    'FTP' => array(
	   'server' => 	'ftp.yoursite.com',
	   'port'	=>	'21',
	   'login'	=>	'yourlogin',
	   'pass'	=>	'yourpass',
	   'root'	=>	'/yoursite.com/app/'
   ),
	*/


   /**
    *	Email
    *	Caracole can send emails to you, or on your behalf. You have to configure them here. It is also a good place to
    *	configure any other email adress that your app should be using.
    *
    *	You have to define two sets of emails adress, for both your dev and prod environments.
    *
    *	@default :		This email adress will be used when no other will be found
    *	@noreply :		The email adress to use when a mail is not supposed to be answered
    *	@register :		The email adress sending the welcoming mails after registering
    *	@recover_pass :	The email adress sending the mails with the recover pass instructions
	**/
   'Email' => array(
	   'prod' => array(
		   'default' 		=> 'tim@pixelastic.com',
		   'noreply' 		=> 'noreply@pixelastic.com',
		   'register' 		=> 'register@pixelastic.com',
		   'recover_pass' 	=> 'pass@pixelastic.com'
	   ),
	   'dev' => array(
		   //'default' 		=> 'contact@yoursite.com',
		   'default' 		=> 'xxxxxxx@xxxxxxx.com',
		   'noreply' 		=> 'noreply@yoursite.com',
		   'register' 		=> 'register@yoursite.com',
		   'recover_pass' 	=> 'pass@yoursite.com'
	   )
   ),

   /**
    *	SiteUrl
    *	Define here the various url of your website. We suggest that you define at least a base and admin urls.
    *
    *	You have to define two sets of urls, for both your dev and prod environments.
    *
    *	@default : 	The default site url
    *	@admin :	The adress to the admin panel
    *	@css :		A subdomain to fetch CSS content
    *	@js :		A subdomain to fetch JS content
    *	@img :		A subdomain to fetch <img> content
    *	@document :	A subdomain to fetch any other document
    *
    *	Note : Creating subdomains to fetch static content is a good way to improve client side performance because it
    *	tricks the browser into thinking each sudomain is a different hosts and thus allowing for parallel download of
    *	content.
    *	You should create those subdomains as CNAMEs to your main domain.
    **/
   'SiteUrl' => array(
	   'prod' => array(
		   'default' 	=> 'http://www.pixelastic.com/',
		   'admin' 		=> 'http://www.pixelastic.com/admin/',
		   's1' 		=> 'http://s1.pixelastic.com/',
		   's2'			=> 'http://s2.pixelastic.com/',
		   's3' 		=> 'http://s3.pixelastic.com/',
	   ),
	   'dev' => array(
		   'default' 	=> 'http://www.pixelastic.dev/',
		   'admin' 		=> 'http://www.pixelastic.dev/admin/',
		   's1' 		=> 'http://s1.pixelastic.dev/',
		   's2'			=> 'http://s2.pixelastic.dev/',
		   's3' 		=> 'http://s3.pixelastic.dev/',
	   )
	   /*
	    'dev' => array(
		   'default' 	=> 'http://www.yoursite/',
		   'admin' 		=> 'http://yoursite/admin/',
		   'js' 		=> 'http://js.yoursite/',
		   'css'		=> 'http://css.yoursite/',
		   'image' 		=> 'http://img.yoursite/',
		   'document'	=> 'http://dl.yoursite/'
	   )
	   */
   ),

   /**
	*	Admin menu
	*	Define here the list of links displayed in the admin menu. Each key represent a link, or a group of links.
	*
	*	@label	:		The name of the link (or group)
	*	@icon 	:		The icon to display for this group
	*	@url 	:		The target url of the link
	*	@links  :		Array of sublinks. Each entry should have a label and url key
	**/
	'Admin' => array(
		'menu' => array(
			array(
			   'label' => __('Blog', true),
			   'icon' => 'Post',
			   'url' => array('admin' => true, 'plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'index'),
			   'links' => array(
					array(
						'label' => __d('caracole_blog', 'Posts', true),
						'url' => array('admin' => true, 'plugin' => 'caracole_blog', 'controller' => 'posts', 'action' => 'index'),
					),
					array(
						'label' => __d('caracole_blog', 'Comments', true),
						'url' => array('admin' => true, 'plugin' => 'caracole_blog', 'controller' => 'comments', 'action' => 'index'),
					)
			   )
			),
			array(
			   'label' => __('Works', true),
			   'icon' => 'Work',
			   'url' => array('admin' => true, 'controller' => 'works', 'action' => 'index'),
			),
			array(
			   'label' => __('Pages', true),
			   'icon' => 'Page',
			   'url' => array('admin' => true, 'plugin' => 'caracole_pages', 'controller' => 'pages', 'action' => 'index'),
			),
			array(
			   'label' => __('Contacts', true),
			   'icon' => 'Contact',
			   'url' => array('admin' => true, 'controller' => 'pixelastic_contacts', 'action' => 'index'),
			),

		)
	),

   /**
    *	Debug
    *	You can define debug options for both your prod and dev environment. Setting a value of 2 in dev and 0 in prod
    *	is a good practice
    **/
	'debug' => array(
		'prod' => 0,
		'dev' => 2
	),

	/**
	 *	Security
	 *	You should always define those Security settings and never keep the default value.
	 *
	 *	@salt : 		This is the salt value used to hash password.
	 *					Wordpress/bbPress has made publicy available a page to generate thos kind of salt values.
	 *					https://api.wordpress.org/secret-key/1.1/bbpress/
	 *	@cipherSeed :	This is a numeric value used to encrypt/decrypt cookie values. Just a random string of numbers
	 **/
	'Security' => array(
	   //'salt' 			=> '1|/us?Rm|dDuGu,QJ<)JR|9<aeDE?N&y>7*_fe+/E@CJ~Y0vR4`:+FY&c`+S(F0r',
	   'salt' 			=> ',!vEg*E{{jB5fcp|^QmZGkt&U5X_;]H7[y|3h!-.7-4S?CN={xV$1?z{H#pzX2xG',
	   //'cipherSeed'		=> '481516234276859907649076470982582490879'
	   'cipherSeed'		=> '49027354001273578092831469240°82476290936'
	),

	/**
	 *	Translation settings
	 **/
	'I18n' => array(
		'PixelasticContact' => array(
			'human' 		=> __('Contact', true),
			'plural' 		=> __('Contacts', true),
			'add'			=> __('New contact', true),
			'added'			=> __('Contact "%1$s" added.', true),
			'edited'		=> __('Contact "%1$s" edited.', true),
			'deleted'		=> __('Contact "%1$s" deleted.', true),
			'restored'		=> __('Contact "%1$s" restored.', true),
			'destroyed'		=> __('Contact "%1$s" destroyed.', true),
			'reordered'		=> __('Contacts reordered.', true),
		)
	),

	/**
	 *	Blog settings
	 **/
	'Blog' => array(
		'title' => __('Pixelastic - Blog', true),
		'description' => __('Weblog of Timothée Carry-Caignon. Posting on all things web-development, back-end to front-end.', true)
	),

	/**
	 *	Contacts
	 **/
	'Contact' => array(
		'prod' => array(
			'useModel' => 'PixelasticContact',
			'modelAlias' => 'PixelasticContact',
			'mail' => 'tim@pixelastic.com'
	   ),
	   'dev' => array(
			'useModel' => 'PixelasticContact',
			'modelAlias' => 'PixelasticContact',
			'mail' => 'xxxxxxx.xxxxxxx@xxxxxxx.com'
	   )
	)
));
