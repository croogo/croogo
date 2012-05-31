<div class="users form">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'reset', $username, $key)));?>
		<fieldset>
		<?php
			echo $this->Form->input('password', array('label' => __('New password')));
			echo $this->Form->input('verify_password', array('type' => 'password', 'label' => __('Verify Password')));
		?>
		</fieldset>
	<?php echo $this->Form->end('Submit');?>
</div>