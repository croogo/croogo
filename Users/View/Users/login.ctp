<div class="users form">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));?>
		<fieldset>
		<?php
			echo $this->Form->input('username', array('label' => __d('croogo', 'Username')));
			echo $this->Form->input('password', array('label' => __d('croogo', 'Password')));
		?>
		</fieldset>
	<?php echo $this->Form->end(__d('croogo', 'Log In')); ?>
	<?php
		echo $this->Html->link(__d('croogo', 'Forgot password?'), array(
			'controller' => 'users', 'action' => 'forgot',
		));
	?>
</div>