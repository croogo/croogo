<?php $this->extend('/Common/admin_edit'); ?>
<?php echo $this->Form->create('Message');?>
<fieldset>
<?php
	echo $this->Form->input('id');
	echo $this->Form->input('name');
	echo $this->Form->input('email');
	echo $this->Form->input('title');
	echo $this->Form->input('body');
	echo $this->Form->input('phone');
	echo $this->Form->input('address');
?>
</fieldset>

<div class="buttons">
<?php
	echo $this->Form->end(__('Save'));
	echo $this->Html->link(__('Cancel'), array(
		'action' => 'index',
	), array(
		'class' => 'cancel',
	));
?>
</div>