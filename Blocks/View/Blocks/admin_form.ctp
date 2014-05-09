<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Blocks'), array('plugin' => 'blocks', 'controller' => 'blocks', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->request->data['Block']['title'], '/' . $this->request->url);
}
if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

echo $this->Form->create('Block', array(
	'class' => 'protected-form',
));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Block'), '#block-basic');
			echo $this->Croogo->adminTab(__d('croogo', 'Access'), '#block-access');
			echo $this->Croogo->adminTab(__d('croogo', 'Visibilities'), '#block-visibilities');
			echo $this->Croogo->adminTab(__d('croogo', 'Params'), '#block-params');
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
					'label' => __d('croogo', 'Title'),
				));

				echo $this->Form->input('alias', array(
					'label' => __d('croogo', 'Alias'),
					'help' => __d('croogo', 'unique name for your block'),
				));
				echo $this->Form->input('region_id', array(
					'label' => __d('croogo', 'Region'),
					'help' => __d('croogo', 'if you are not sure, choose \'none\''),
				));
				echo $this->Form->input('body', array(
					'label' => __d('croogo', 'Body'),
				));
				echo $this->Form->input('class', array(
					'label' => __d('croogo', 'Class')
				));
				echo $this->Form->input('element', array(
					'label' => __d('croogo', 'Element')
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
					'label' => __d('croogo', 'Visibility Paths'),
					'help' => __d('croogo', 'Enter one URL per line. Leave blank if you want this Block to appear in all pages.')
				));
			?>
			</div>

			<div id="block-params" class="tab-pane">
			<?php
				echo $this->Form->input('Block.params', array(
					'label' => __d('croogo', 'Params'),
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
			$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger')) .
			$this->Form->input('status', array(
				'legend' => false,
				'type' => 'radio',
				'class' => false,
				'label' => true,
				'default' => CroogoStatus::UNPUBLISHED,
				'options' => $this->Croogo->statuses(),
			)) .
			$this->Form->input('show_title', array(
				'label' => __d('croogo', 'Show title ?'),
				'class' => false,
			)) .
			$this->Html->div('input-daterange',
				$this->Form->input('publish_start', array(
					'label' => __d('croogo', 'Publish Start'),
					'type' => 'text',
				)) .
				$this->Form->input('publish_end', array(
					'label' => __d('croogo', 'Publish End'),
					'type' => 'text',
				))
			) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
		?>
	</div>

</div>
<?php echo $this->Form->end(); ?>
