<?php
use Cake\Core\Configure;

echo $this->Form->create('User', ['url' => ['controller' => 'users', 'action' => 'login']]);?>
<div class="box">
	<div class="box-content">
	<?php
	$this->Form->templates([
		'label' => false,
	]);
	echo $this->Form->input('username', [
		'placeholder' => __d('croogo', 'Username'),
		'before' => '<span class="add-on"><i class="icon-user"></i></span>',
		'div' => 'input-prepend text',
		'class' => 'span11',
	]);
	echo $this->Form->input('password', [
		'placeholder' => __d('croogo', 'Password'),
		'before' => '<span class="add-on"><i class="icon-key"></i></span>',
		'div' => 'input-prepend password',
		'class' => 'span11',
	]);
	if (Configure::read('Access Control.autoLoginDuration')):
		echo $this->Form->input('remember', [
			'label' => __d('croogo', 'Remember me?'),
			'type' => 'checkbox',
			'default' => false,
		]);
	endif;
	echo $this->Form->button(__d('croogo', 'Log In'));
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
