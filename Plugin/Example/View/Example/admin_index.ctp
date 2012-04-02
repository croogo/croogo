<?php $this->extend('/Common/admin_index'); ?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->Html->link(__('New Tab'), array('action' => 'add')); ?></li>
<?php $this->end(); ?>

<p><?php echo __('content here'); ?></p>