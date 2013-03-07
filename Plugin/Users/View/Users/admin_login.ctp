<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));?>
<div class="box">
	<div class="box-content">
	<?php
		$this->Form->inputDefaults(array(
			'label' => false,
		));
		echo $this->Form->input('username', array(
			'placeholder' => __('Username'),
			'before' => '<span class="add-on"><i class="icon-user"></i></span>',
			'div' => 'input-prepend text',
			'class' => 'span11',
		));
		echo $this->Form->input('password', array(
			'placeholder' => 'Password',
			'before' => '<span class="add-on"><i class="icon-key"></i></span>',
			'div' => 'input-prepend password',
			'class' => 'span11',
		));
		echo $this->Form->button(__('Log In'));
		echo $this->Html->link(__('Forgot password?'), array(
			'admin' => false,
			'controller' => 'users',
			'action' => 'forgot',
			), array(
			'class' => 'forgot'
		));
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>