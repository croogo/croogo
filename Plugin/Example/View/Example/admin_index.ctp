<?php $this->extend('/Common/admin_index'); ?>

<?php $this->start('actions'); ?>
<?php
	echo $this->Croogo->adminAction(
		__('New Tab'),
		array('action' => 'add')
	);
	echo $this->Croogo->adminAction(
		__('Chooser Example'),
		array('action' => 'chooser')
	);
?>
<?php $this->end(); ?>

<p><?php echo __('content here'); ?></p>