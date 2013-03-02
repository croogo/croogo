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
		<?php
			echo $this->Croogo->adminTab(__('Block'), '#block-basic');
			echo $this->Croogo->adminTab(__('Access'), '#block-access');
			echo $this->Croogo->adminTab(__('Visibilities'), '#block-visibilities');
			echo $this->Croogo->adminTab(__('Params'), '#block-params');
			echo $this->Croogo->adminTabs();
		?>
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
					'label' => __('Title'),
				));

				echo $this->Form->input('alias', array(
					'label' => __('Alias'),
					'rel' => __('unique name for your block'),
				));
				echo $this->Form->input('region_id', array(
					'label' => __('Region'),
					'rel' => __('if you are not sure, choose \'none\''),
				));
				echo $this->Form->input('body', array(
					'label' => __('Body'),
				));
				echo $this->Form->input('class', array(
					'label' => __('Class')
				));
				echo $this->Form->input('element', array(
					'label' => __('Element')
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
					'label' => false,
					'rel' => __('Enter one URL per line. Leave blank if you want this Block to appear in all pages.')
				));
			?>
			</div>

			<div id="block-params" class="tab-pane">
			<?php
				echo $this->Form->input('Block.params', array(
					'label' => false,
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
				'label' => __('Show title?'),
				'class' => false,
			)) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
		?>
	</div>

</div>
<?php echo $this->Form->end(); ?>
