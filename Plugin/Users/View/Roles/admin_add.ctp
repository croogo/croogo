<?php
$this->extend('Croogo./Common/admin_edit');
$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__('Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
	->addCrumb(__('Roles'), array('plugin' => 'users', 'controller' => 'roles', 'action' => 'index'))
	->addCrumb(__('Add'), $this->here);
?>
<?php echo $this->Form->create('Role');?>

<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#role-main" data-toggle="tab"><?php echo __('Role'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">
			<div id="role-main" class="tab-pane">
			<?php
				$this->Form->inputDefaults(array(
					'label' => false,
					'class' => 'span10',
				));
				echo $this->Form->input('title', array(
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('alias', array(
					'placeholder' => __('Alias'),
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
				array('button' => 'danger')
			) .
			$this->Html->endBox();
		echo $this->Croogo->adminBoxes();
	?>
	</div>
	
</div>
<?php echo $this->Form->end(); ?>
