<?php

$this->extend('/Common/admin_edit');
$this->Html->script(array('Nodes.nodes'), false);

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Content'), array('controller' => 'nodes', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_add') {
	$formUrl = array('action' => 'add', $typeAlias);
	$this->Html
		->addCrumb(__('Create'), array('controller' => 'nodes', 'action' => 'create'))
		->addCrumb($type['Type']['title'], $this->here);
}

if ($this->request->params['action'] == 'admin_edit') {
	$formUrl = array('action' => 'edit');
	$this->Html->addCrumb($this->request->data['Node']['title'], $this->here);
}

echo $this->Form->create('Node', array('url' => $formUrl));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#node-main" data-toggle="tab"><?php echo __($type['Type']['title']); ?></a></li>
			<li><a href="#node-access" data-toggle="tab"><?php echo __('Access'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">

			<div id="node-main" class="tab-pane">
			<?php
				echo $this->Form->input('parent_id', array('type' => 'select', 'options' => $nodes, 'empty' => true));
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array(
					'class' => 'span10',
				));
				echo $this->Form->input('title', array(
					'label' => false,
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('slug', array(
					'label' => false,
					'class' => 'span10 slug',
					'placeholder' => __('Slug'),
				));
				echo $this->Form->input('excerpt', array(
					'label' => false,
					'placeholder' => __('Excerpt'),
				));
				echo $this->Form->input('body', array(
					'label' => __('Body'),
				));
			?>
			</div>

			<div id="node-access" class="tab-pane">
			<?php
				echo $this->Form->input('Role.Role', array('class' => false));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>

	</div>
	<div class="span4">
	<?php
		echo $this->Html->beginBox(__('Publishing')) .
			$this->Form->button(__('Apply'), array('name' => 'apply', 'class' => 'btn')) .
			$this->Form->button(__('Save'), array('class' => 'btn btn-primary')) .
			$this->Html->link(__('Cancel'), array('action' => 'index'), array('class' => 'cancel btn btn-danger')) .
			$this->Form->input('status', array(
				'label' => __('Published'),
				'checked' => 'checked',
				'class' => false,
			)) .
			$this->Form->input('promote', array(
				'label' => __('Promoted to front page'),
				'checked' => 'checked',
				'class' => false,
			)) .
			$this->Form->input('user_id', array(
				'label' => __('Publish as '),
			)) .
			$this->Form->input('created', array(
				'type' => 'text',
				'class' => 'span10',
			));

		echo $this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>