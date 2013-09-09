<?php
	echo $this->Fastcode->p(__d('caracole_contacts', 'You have received a contact request :', true));
?>

<?php
	echo $this->Fastcode->p(sprintf('%1$s : %2$s', __d('caracole_contacts', 'Name', true), $item['Contact']['name']));
?>

<?php
	echo $this->Fastcode->p(sprintf('%1$s : %2$s', __d('caracole_contacts', 'Email', true), $item['Contact']['email']));
?>

<?php echo $this->Fastcode->p(__d('caracole_contacts', 'Message', true)); ?>

<?php echo $this->Fastcode->p($this->Fastcode->html($item['Contact']['text'])); ?>
