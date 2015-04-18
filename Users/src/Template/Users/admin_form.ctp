<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->data['User']['name'], array(
		'plugin' => 'users', 'controller' => 'users', 'action' => 'edit',
		$this->data['User']['id']
	));
	$this->set('title_for_layout', __d('croogo', 'Edit user %s', $this->data['User']['username']));
} else {
	$this->set('title_for_layout', __d('croogo', 'New user'));
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'add'));
}
?>
<?php $this->start('actions'); ?>
<?php if ($this->request->params['action'] == 'admin_edit'): ?>
<?php
	echo $this->Croogo->adminAction(__d('croogo', 'Reset password'), array('action' => 'reset_password', $this->request->params['pass']['0']));
?>
<?php endif; ?>
<?php $this->end(); ?>

<?php
echo $this->Form->create('User', array(
	'fieldAccess' => array(
		'User.role_id' => 1,
	),
	'class' => 'protected-form',
));
?>

<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'User'), '#user-main');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">

			<div id="user-main" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('role_id', array('label' => __d('croogo', 'Role')));
				$this->Form->inputDefaults(array(
					'class' => 'span10',
					'label' => false,
				));
				echo $this->Form->input('username', array(
					'label' => __d('croogo', 'Username'),
				));
				echo $this->Form->input('name', array(
					'label' => __d('croogo', 'Name'),
				));
				echo $this->Form->input('email', array(
					'label' => __d('croogo', 'Email'),
				));
				echo $this->Form->input('website', array(
					'label' => __d('croogo', 'Website'),
				));
				echo $this->Form->input('timezone', array(
					'type' => 'select',
					'empty' => true,
					'options' => $this->Time->listTimezones(),
					'label' => __d('croogo', 'Timezone'),
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
			$this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
			$this->Form->button(__d('croogo', 'Save'), array('button' => 'success')) .
			$this->Html->link(
			__d('croogo', 'Cancel'), array('action' => 'index'),
			array('button' => 'danger'));

		if ($this->request->params['action'] == 'admin_add'):
			echo $this->Form->input('notification', array(
				'label' => __d('croogo', 'Send Activation Email'),
				'type' => 'checkbox',
				'class' => false,
			));
		endif;

		echo $this->Form->input('status', array(
			'label' => __d('croogo', 'Status'),
			'class' => false,
		));

		$showPassword = !empty($this->data['User']['status']);
		if ($this->request->params['action'] == 'admin_add'):
			$out = $this->Form->input('password', array(
				'label' => __d('croogo', 'Password'),
				'class' => false,
				'disabled' => !$showPassword,
			));
			$out .= $this->Form->input('verify_password', array(
				'label' => __d('croogo', 'Verify Password'),
				'class' => false,
				'disabled' => !$showPassword,
				'type' => 'password'
			));

			$this->Form->unlockField('User.password');
			$this->Form->unlockField('User.verify_password');

			echo $this->Html->div(null, $out, array(
				'id' => 'passwords',
				'style' => $showPassword ? '' : 'display: none',
			));
		endif;

		echo $this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>

</div>
<?php echo $this->Form->end(); ?>
<?php

$script = <<<EOF
	$('#UserStatus').on('change', function(e) {
		var passwords = $('#passwords');
		var elements = $('input', passwords);
		elements.prop('disabled', !this.checked);
		if (this.checked) {
			passwords.show('fast');
		} else {
			passwords.hide('fast');
		}
	});
	$('#UserNotification').on('change', function(e) {
		var status = $('#UserStatus');
		status.attr('checked', false);
		status.trigger('change').parent().toggle('fast');
	});
EOF;

if ($this->request->params['action'] == 'admin_add'):
	$this->Js->buffer($script);
endif;
