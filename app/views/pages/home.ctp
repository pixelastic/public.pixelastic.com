<?php
/**
 *	Displaying the home page
 **/

//	Page title
$this->pageTitle = Configure::read('Site.name');
$this->set('pageCssId', 'home');
/*
<h2>Welcome to Pixelastic</h2>
<p>
	Pixelastic is my online identity. My real name is Timothée Carry-Caignon, but people usually just call me Tim.
</p>
<p>
	I'm a freelance web developer, which means that I'm the one who wrote the code that make your website working.
	I know my way around the various parts of web development, from back-end to front-end and my weapons of choice are
	<?php e($this->Fastcode->link(__('cakePHP', true), 'http://www.cakephp.org/', array('target' => '_blank'))); ?>
	and
	<?php e($this->Fastcode->link(__('jQuery', true), 'http://www.jquery.com/', array('target' => '_blank'))); ?>.
</p>
<p>
	I take great pride into	giving to my clients really easy-to-use websites that suits their needs, without worrying them about all
	the technical stuff. But I also speak geek as a second language, so if you want to discuss what's under the hood or have very specific needs, I'm here too.
</p>
<p>
	Unfortunatly, I am not a web designer, but if you need one, I've got plenty of friends in that area.
</p>
	<p class="presentation">
		<img src="/img/photo.jpg">
		I'm Tim, a freelance web developer, which means that I'm the one who wrote the code that make your website working.
	</p>
*/?>

<div class="homeLayout">
	<div class="homeMain span-13">
		<?php
			echo $this->Html->tag('h2', $this->Fastcode->link(__('Welcome to Pixelastic', true), $this->here, array('escape' => false)));

			echo $this->Fastcode->message(
				$this->Fastcode->p("My name is Timothée Carry-Caignon, and I'm a freelance web developer.")
				.$this->Fastcode->p("This website is the place for all my web-related stuff as it is both a dayjob and a passion.")
			);
		?>
	</div>
	<div class="homeSecondary prepend-2 span-9 last">
		<?php
			echo $this->element('spaceinvader', array('width' => '6', 'height' => '7'));
		?>
	</div>

</div>