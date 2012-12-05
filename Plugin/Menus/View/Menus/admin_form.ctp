<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Menus'), array('plugin' => 'menus', 'controller' => 'menus', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Menu']['title'], $this->here);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__('Add'), $this->here);
}

echo $this->Form->create('Menu');

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#menu-basic" data-toggle="tab"><?php echo __('Menu'); ?></a></li>
			<li><a href="#menu-misc" data-toggle="tab"><?php echo __('Misc.'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">

			<div id="menu-basic" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array(
					'class' => 'span10',
				));
				echo $this->Form->input('title', array(
					'label' => false,
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('alias', array(
					'label' => false,
					'placeholder' => __('Alias'),
				));
				echo $this->Form->input('description', array(
					'label' => false,
					'placeholder' => __('Description'),
				));
			?>
			</div>

			<div id="menu-misc" class="tab-pane">
			<?php
				echo $this->Form->input('params', array(
					'label' => false,
					'placeholder' => __('Params'),
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
		<?php
		echo $this->Html->beginBox('Publishing') .
			$this->Form->button(__('Apply'), array('name' => 'apply', 'button' => 'default')) .
			$this->Form->button(__('Save'), array('button' => 'default')) .
			$this->Html->link(__('Cancel'), array('action' => 'index'), array('button' => 'danger')) .
			$this->Html->endBox();

		$this->Croogo->adminBoxes();
		?>
	</div>

</div>
<?php echo $this->Form->end(); ?>
