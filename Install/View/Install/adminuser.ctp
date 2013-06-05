<?php
echo $this->Form->create(null, array(
	'url' => array('controller' => 'install', 'action' => 'adminuser'),
	));
?>
<div class="install">
	<h2><?php echo __d('croogo', 'Step 3: Create Admin User'); ?></h2>
	<?php
		$this->Form->inputDefaults(array(
			'label' => false,
			'class' => 'span10',
		));
		echo $this->Form->input('User.username', array(
			'placeholder' => __d('croogo', 'Username'),
			'before' => '<span class="add-on"><i class="icon-user"></i></span>',
			'div' => 'input text input-prepend',
		));
		echo $this->Form->input('User.password', array(
			'placeholder' => __d('croogo', 'New Password'),
			'value' => '',
			'before' => '<span class="add-on"><i class="icon-key"></i></span>',
			'div' => 'input password input-prepend',
		));
		echo $this->Form->input('User.verify_password', array(
			'placeholder' => __d('croogo', 'Verify Password'),
			'type' => 'password',
			'value' => '',
			'before' => '<span class="add-on"><i class="icon-key"></i></span>',
			'div' => 'input password input-prepend',
		));
	?>
</div>
<div class="form-actions">
	<?php echo $this->Form->submit(__d('croogo', 'Save'), array('class' => 'btn btn-success', 'div' => false)); ?>
	<?php echo $this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array( 'class' => 'btn cancel')); ?>
</div>
<?php
	echo $this->Form->end();
?>