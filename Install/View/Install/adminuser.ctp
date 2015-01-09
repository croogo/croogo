<?php
echo $this->Form->create(null, array(
	'url' => array('controller' => 'install', 'action' => 'adminuser'),
	'inputDefaults' => array(
		'label' => false,
		'div' => $this->Theme->getCssClass('addonClass').' input text',
	),
));
?>
<div class="install">
	<h2><?php echo __d('croogo', 'Step 3: Create Admin User'); ?></h2>
	<?php
		echo $this->Form->input('User.username', array(
			'placeholder' => __d('croogo', 'Username'),
			'before' => '<span class="input-group-addon">'.$this->Html->icon('user').'</span>',
		));
		echo $this->Form->input('User.password', array(
			'placeholder' => __d('croogo', 'New Password'),
			'value' => '',
			'before' => '<span class="input-group-addon">'.$this->Html->icon('key').'</span>',
		));
		echo $this->Form->input('User.verify_password', array(
			'placeholder' => __d('croogo', 'Verify Password'),
			'type' => 'password',
			'value' => '',
			'before' => '<span class="input-group-addon">'.$this->Html->icon('key').'</span>',
		));
	?>
</div>
<div class="form-actions">
	<?php echo $this->Form->submit(__d('croogo', 'Save'), array('class' => 'btn btn-success', 'div' => false)); ?>
	<?php echo $this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array( 'class' => 'btn btn-danger')); ?>
</div>
<?php
	echo $this->Form->end();
?>