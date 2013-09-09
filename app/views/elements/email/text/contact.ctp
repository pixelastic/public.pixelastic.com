<?php echo __('You have received a contact request :', true); ?>


<?php echo sprintf('%1$s : %2$s', __('Name', true), $item['PixelasticContact']['name']); ?>


<?php echo sprintf('%1$s : %2$s', __('Email', true), $item['PixelasticContact']['email']); ?>


<?php
	if ($item['Options']['is_project']) {
		echo sprintf('%1$s : %2$s', __('Timeframe', true), $item['PixelasticContact']['timeframe'])."\n\n";
		echo sprintf('%1$s : %2$s', __('Budget', true), $item['PixelasticContact']['budget'])."\n\n";
	}
?>
<?php sprintf('%1$s :', __('Message', true)); ?>
<?php echo $item['PixelasticContact']['text']; ?>