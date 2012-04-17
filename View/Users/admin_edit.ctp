<?php $this->extend('/Common/admin_edit'); ?>
<?php $this->start('actions'); ?>
	<li><?php echo $this->Html->link(__('Reset password'), array('action' => 'reset_password', $this->params['pass']['0'])); ?></li>
<?php $this->end(); ?>