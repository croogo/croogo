<div class="users form">
	<h2><?php echo __d('croogo', 'Login'); ?></h2>
	<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));?>
		<fieldset>
		<?php
			echo $this->Form->input('username', array('label' => __d('croogo', 'Username')));
			echo $this->Form->input('password', array('label' => __d('croogo', 'Password')));
		?>
		</fieldset>
	<?php echo $this->Form->end(__d('croogo', 'Submit'));?>
</div>
