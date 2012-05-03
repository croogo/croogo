<div class="install">
	<h2><?php echo __('Step 3: Create Admin User'); ?></h2>
	<?php echo $this->Form->create(null, array('url' => array('controller' => 'install', 'action' => 'adminuser')));?>
	<fieldset>
	<?php
		echo $this->Form->input('User.username');
		echo $this->Form->input('User.password', array('label' => __('New Password'), 'value' => ''));
		echo $this->Form->input('User.verify_password', array('label' => __('Verify Password'), 'type' => 'password', 'value' => ''));
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
</div>