<?php $this->extend('/Common/admin_edit'); ?>
<?php
$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__('Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
	->addCrumb($this->data['User']['name'], array(
		'plugin' => 'users', 'controller' => 'users', 'action' => 'edit',
		$this->data['User']['id']
	));
?>
<?php $this->start('actions'); ?>
	<li><?php echo $this->Html->link(__('Reset password'), array('action' => 'reset_password', $this->params['pass']['0']), array(
	'class' => 'btn')); ?></li>
<?php $this->end(); ?>

<?php echo $this->Form->create('User');?>

<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#user-main" data-toggle="tab"><?php echo __('User'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">

			<div id="user-main" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('role_id', array('label' => __('Role')));
				$this->Form->inputDefaults(array(
					'class' => 'span10',
					'label' => false,
				));
				echo $this->Form->input('username', array(
					'placeholder' => __('Username'),
				));
				echo $this->Form->input('name', array(
					'placeholder' => __('Name'),
				));
				echo $this->Form->input('email', array(
					'placeholder' => __('Email'),
				));
				echo $this->Form->input('website', array(
					'placeholder' => __('Website'),
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->Html->beginBox(__('Publishing')) .
			$this->Form->button(__('Save'), array('button' => 'default')) .
			$this->Html->link(
			__('Cancel'), array('action' => 'index'),
			array('button' => 'danger')) .

			$this->Form->input('status', array(
				'label' => __('Status'),
				'class' => false,
			)) .

			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>

</div>
<?php echo $this->Form->end(); ?>