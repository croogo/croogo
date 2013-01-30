<?php
$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__('Settings'), array(
		'admin' => true,
		'plugin' => 'settings',
		'controller' => 'settings',
		'action' => 'index',
	));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->data['Setting']['key'], $this->here);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__('Add'), $this->here);
}

echo $this->Form->create('Setting');

?>
<div class="row-fluid">
	<div class="span8">
		<ul class="nav nav-tabs">
			<li><a href="#setting-basic" data-toggle="tab"><?php echo __('Settings'); ?></a></li>
			<li><a href="#setting-misc" data-toggle="tab"><?php echo __('Misc.'); ?></a></li>
		</ul>

		<div class="tab-content">
			<div id="setting-basic" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array(
					'label' => false,
					'class' => 'span10',
				));
				echo $this->Form->input('key', array(
					'rel' => __("e.g., 'Site.title'"),
					'placeholder' => __('Key'),
				));
				echo $this->Form->input('value', array(
					'placeholder' => __('Value'),
				));
			?>
			</div>

			<div id="setting-misc" class="tab-pane">
			<?php
				echo $this->Form->input('title', array(
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('description', array(
					'placeholder' => __('Description'),
				));
				echo $this->Form->input('input_type', array(
					'placeholder' => __('Input Type'),
					'rel' => __("e.g., 'text' or 'textarea'"),
				));
				echo $this->Form->input('editable', array(
					'label' => __('Editable'),
					'class' => false,
				));
				echo $this->Form->input('params', array(
					'placeholder' => __('Params'),
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
			$this->Html->link(__('Cancel'), array('action' => 'index'), array(
				'button' => 'danger')) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>

</div>
<?php echo $this->Form->end(); ?>