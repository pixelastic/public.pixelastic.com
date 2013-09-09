<?php echo __d('caracole_contacts', 'You have received a contact request :', true); ?>


<?php echo sprintf('%1$s : %2$s', __d('caracole_contacts', 'Name', true), $item['Contact']['name']); ?>


<?php echo sprintf('%1$s : %2$s', __d('caracole_contacts', 'Email', true), $item['Contact']['email']); ?>


<?php echo sprintf('%1$s :', __d('caracole_contacts', 'Message')); ?>
<?php echo $item['Contact']['text']; ?>