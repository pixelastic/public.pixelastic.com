<?php
	/**
	 *	index.ctp
	 *	Display the list of my latest works
	 **/

	$pageTitle = __('Work', true);
	$this->set(array(
		'pageCssId' => 'work',
		'title_for_layout' => $pageTitle
	));
?>
<div class="workLayout">
	<?php
		echo $this->Html->tag('h2', $this->Fastcode->link($pageTitle, $this->here, array('escape' => false)));

		echo $this->Fastcode->p(__("Please, enjoy a selection of the latests projects I've been involved with and that I'm proud to show to the whole world.", true));
		echo $this->Fastcode->p(__("Each project is different and each client has its own needs, so it will be a fairly reasonable representation of what I'm able to do.", true));

		/*
		 echo $this->Html->tag('h3', __("You can't have done this all by yourself !", true));
			echo $this->Fastcode->message(
				$this->Fastcode->p(__("No, you're right. There are still some areas where I'm not confortable enough to handle all the work. When it happens, I call one of my talented colleages to the rescue.", true))
				.$this->Fastcode->p(__('Due credits are indicated on each description.', true)),
				'notice'
			);

			echo $this->Html->tag('h3', __("So what did you do exactly ?", true));
			echo $this->Fastcode->message(

				.$this->Fastcode->p(__("But as I have quite a few talents in web development I get involved either on a very specific need or helps from start to finish.", true)),
				'notice'
			);

			echo $this->Html->tag('h3', __("But why do you do that ?!", true));
			echo $this->Fastcode->message(
				$this->Fastcode->p(__("Because I like it !", true)),
				'notice'
			);
		*/



		// Displaying all the works
		foreach($itemList as &$item) {
			?>
			<div class="workDisplay">
				<?php
					// Title
					echo $this->Html->tag('h3', $this->Fastcode->link($item['Work']['name'], $item['Work']['url'], array('target' => '_blank')));
					// Link
					//echo $this->Fastcode->link($item['Work']['url'], $item['Work']['url'], array('class' => 'website', 'target' => '_blank'));
				?>
				<div class="text span-15">
					<?php echo $this->Fastcode->message($item['Work']['text'], 'notice'); ?>
				</div>
				<div class="screen span-8 prepend-1 last">
					<?php
						echo $this->Fastcode->link(
							$this->Image->image($item['Screen'], array('width' => 310, 'resize' => 'square', 'filename' => $item['Work']['name'])),
							$this->Image->url($item['Screen'], array('width' => 950, 'filename' => $item['Work']['name'])),
							array('escape' => false, 'target' => '_blank', 'class' => 'lightbox', 'title' => $item['Work']['name'].' - '.$item['Work']['url'])
						);
					?>
				</div>
			</div>
			<?php
		}
	?>
</div>
