<?php

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
	->addCrumb(__('Permissions'), array('plugin' => 'acl', 'controller' => 'acl_permissions'))
	->addCrumb(__('Actions'), array('plugin' => 'acl', 'controller' => 'acl_actions', 'action' => 'index'))
	->addCrumb(__('Add'), $this->here)

?>
<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>
<?php echo $this->Form->create('Aco', array('url' => array('controller' => 'acl_actions', 'action' => 'add'))); ?>

<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#action-main" data-toggle="tab"><?php echo __('Action'); ?></a></li>
		</ul>

		<div class="tab-content">
			<div id="action-main" class="tab-pane">
			<?php
				echo $this->Form->input('parent_id', array(
					'options' => $acos,
					'empty' => true,
					'label' => __('Parent'),
					'rel' => __('Choose none if the Aco is a controller.'),
				));
				$this->Form->inputDefaults(array(
					'label' => false,
					'class' => 'span10',
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
			$this->Form->button(__('Submit'), array('name' => 'apply', 'class' => 'btn')) .
			$this->Html->link(__('Cancel'), array('action' => 'index'), array('class' => 'cancel btn btn-danger')) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
