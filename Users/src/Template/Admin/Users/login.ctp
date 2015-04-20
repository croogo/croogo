<?php
use Cake\Core\Configure;

echo $this->Form->create('User', ['url' => ['action' => 'login']]);?>
<div class="box">
	<div class="box-content">
	<?php
	$this->Form->templates([
		'label' => false,
		'textContainer' => '<div class="input-prepend {{type}}"><span class="add-on"><i class="icon-user"></i></span>{{content}}</div>',
		'passwordContainer' => '<div class="input-prepend {{type}}"><span class="add-on"><i class="icon-key"></i></span>{{content}}</div>',
	]);
	echo $this->Form->input('username', [
		'placeholder' => __d('croogo', 'Username'),
		'class' => 'span11'
	]);
	echo $this->Form->input('password', [
		'placeholder' => __d('croogo', 'Password'),
		'class' => 'span11'
	]);
	if (Configure::read('Access Control.autoLoginDuration')):
		echo $this->Form->input('remember', [
			'label' => __d('croogo', 'Remember me?'),
			'type' => 'checkbox',
			'default' => false,
		]);
	endif;
	echo $this->Form->button(__d('croogo', 'Log In'), ['class' => 'btn']);
	echo $this->Html->link(__d('croogo', 'Forgot password?'), [
		'prefix' => null,
		'controller' => 'users',
		'action' => 'forgot',
	], [
		'class' => 'forgot'
	]);
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
