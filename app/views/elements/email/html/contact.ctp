<?php
	echo $this->Fastcode->p(__('You have received a contact request :', true));
?>

<?php
	echo $this->Fastcode->p(sprintf('%1$s : %2$s', __('Name', true), $item['PixelasticContact']['name']));
?>

<?php
	echo $this->Fastcode->p(
		sprintf(
			'%1$s : %2$s',
			__('Email', true),
			$this->Fastcode->link($item['PixelasticContact']['email'], 'mailto:'.$item['PixelasticContact']['email'])
		)
	);
?>

<?php
	if ($item['Options']['is_project']) {
		echo $this->Fastcode->p(sprintf('%1$s : %2$s', __('Timeframe', true), $item['PixelasticContact']['timeframe']))."\n";
		echo $this->Fastcode->p(sprintf('%1$s : %2$s', __('Budget', true), $item['PixelasticContact']['budget']));
	}
?>

<?php echo $this->Fastcode->p(sprintf('%1$s :<br />%2$s', __('Message', true), $this->Fastcode->html($item['PixelasticContact']['text']))); ?>
