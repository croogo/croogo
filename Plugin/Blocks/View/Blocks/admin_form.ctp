<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__('Blocks'), array('plugin' => 'blocks', 'controller' => 'blocks', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Block']['title'], $this->here);
}
if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__('Add'), $this->here);
}

echo $this->Form->create('Block');

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#block-basic" data-toggle="tab"><?php echo __('Block'); ?></a></li>
			<li><a href="#block-access" data-toggle="tab"><?php echo __('Access'); ?></a></li>
			<li><a href="#block-visibilities" data-toggle="tab"><?php echo __('Visibilities'); ?></span></a></li>
			<li><a href="#block-params" data-toggle="tab"><?php echo __('Params'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">

			<div id="block-basic" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array(
					'label' => false,
					'class' => 'span10',
				));
				echo $this->Form->input('title', array(
					'placeholder' => __('Title'),
				));

				echo $this->Form->input('alias', array(
					'placeholder' => __('Alias'),
					'rel' => __('unique name for your block'),
				));
				echo $this->Form->input('region_id', array(
					'placeholder' => __('Region'),
					'rel' => __('if you are not sure, choose \'none\''),
				));
				echo $this->Form->input('body', array(
					'placeholder' => __('Body'),
				));
				echo $this->Form->input('class', array(
					'placeholder' => __('Class')
				));
				echo $this->Form->input('element', array(
					'placeholder' => __('Element')
				));
			?>
			</div>

			<div id="block-access" class="tab-pane">
			<?php
				echo $this->Form->input('Role.Role', array(
					'class' => false,
				));
			?>
			</div>

			<div id="block-visibilities" class="tab-pane">
			<?php
				echo $this->Form->input('Block.visibility_paths', array(
					'placeholder' => __('Visibility Paths'),
					'rel' => __('Enter one URL per line. Leave blank if you want this Block to appear in all pages.')
				));
			?>
			</div>

			<div id="block-params" class="tab-pane">
			<?php
				echo $this->Form->input('Block.params', array(
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
			$this->Form->button(__('Apply'), array('name' => 'apply', 'button' => 'default')) .
			$this->Form->button(__('Save'), array('button' => 'default')) .
			$this->Html->link(__('Cancel'), array('action' => 'index'), array('button' => 'danger')) .
			$this->Form->input('status', array(
				'label' => __('Status'),
				'class' => false,
			)) .
			$this->Form->input('show_title', array(
				'label' => __('Show title ?'),
				'class' => false,
			)) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
		?>
	</div>

</div>
<?php echo $this->Form->end(); ?>
