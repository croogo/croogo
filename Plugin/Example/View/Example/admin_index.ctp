<?php $this->extend('/Common/admin_index'); ?>

<?php $this->start('actions'); ?>
<?php
	echo $this->Croogo->adminAction(
		__d('croogo', 'New Tab'),
		array('action' => 'add')
	);
	echo $this->Croogo->adminAction(
		__d('croogo', 'Chooser Example'),
		array('action' => 'chooser')
	);
?>
<?php $this->end(); ?>

<p><?php echo __d('croogo', 'content here'); ?></p>