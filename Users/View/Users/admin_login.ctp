<?php echo $this->Form->create('User', array('url' => array('controller' => 'users', 'action' => 'login')));?>
<div class="panel panel-default">
	<div class="panel-body" id="login-form">
	<?php
		$this->Form->inputDefaults(array(
			'label' => false,
			'class' => 'form-control',
		));
		echo $this->Form->input('username', array(
			'placeholder' => __d('croogo', 'Username'),
				'before' => '<span class="input-group-addon">' . $this->Html->icon('user') . '</span>',
				'div' => 'input-group',
		));
		echo $this->Form->input('password', array(
			'placeholder' => __d('croogo', 'Password'),
				'before' => '<span class="input-group-addon">' . $this->Html->icon('key') . '</span>',
				'div' => 'input-group',
		));
		if (Configure::read('Access Control.autoLoginDuration')):
			echo $this->Form->input('remember', array(
				'label' => __d('croogo', 'Remember me?'),
				'type' => 'checkbox',
				'default' => false,
			));
		endif;
		echo $this->Form->button(__d('croogo', 'Log In'), array('class'=>'btn-primary'));
		echo $this->Html->link(__d('croogo', 'Forgot password?'), array(
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