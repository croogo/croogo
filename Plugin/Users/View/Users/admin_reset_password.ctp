<div class="users form">
	<h2><?php echo __('Reset password'); ?>: <?php echo $this->data['User']['username']; ?></h2>
	<?php echo $this->Form->create('User', array('url' => array('action' => 'reset_password')));?>
	<fieldset>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('username', array('type' => 'hidden'));
		echo $this->Form->input('password', array('label' => __('New Password'), 'value' => ''));
		echo $this->Form->input('verify_password', array('label' => __('Verify Password'), 'type' => 'password', 'value' => ''));
	?>
	</fieldset>

	<div class="buttons">
	<?php
		echo $this->Form->end(__('Reset'));
		echo $this->Html->link(__('Cancel'), array(
			'action' => 'index',
		), array(
			'class' => 'cancel',
		));
	?>
	</div>
</div>