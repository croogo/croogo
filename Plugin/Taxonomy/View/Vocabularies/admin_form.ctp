<?php
$this->Html->script(array('/taxonomy/js/vocabularies'), false);
$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html
		->addCrumb(__('Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index', $this->request->data['Vocabulary']['id'],))
		->addCrumb($this->request->data['Vocabulary']['title'], $this->here);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html
		->addCrumb(__('Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index',))
		->addCrumb(__('Add'), $this->here);
}

echo $this->Form->create('Vocabulary');

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#vocabulary-basic" data-toggle="tab"><?php echo __('Vocabulary'); ?></a></li>
			<li><a href="#vocabulary-options" data-toggle="tab"><?php echo __('Options'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">

			<div id="vocabulary-basic" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array(
					'class' => 'span10',
					'label' => false,
				));
				echo $this->Form->input('title', array(
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('alias', array(
					'class' => 'alias span10',
					'placeholder' => __('Alias'),
				));
				echo $this->Form->input('description', array(
					'label' => __('Description'),
				));
				echo $this->Form->input('Type.Type', array(
					'label' => __('Type'),
				));
			?>
			</div>

			<div id="vocabulary-options" class="tab-pane">
			<?php
				echo $this->Form->input('required', array(
					'label' => __('Required'),
					'class' => false,
				));
				echo $this->Form->input('multiple', array(
					'label' => __('Multiple'),
					'class' => false,
				));
				echo $this->Form->input('tags', array(
					'label' => __('Tags'),
					'class' => false,
				));
			?>
			</div>

			<?php echo $this->Croogo->adminBoxes(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->Html->beginBox(__('Publishing')) .
			$this->Form->button(__('Save'), array('button' => 'default')) .
			$this->Html->link(
				__('Cancel'),
				array('action' => 'index'),
				array('button' => 'danger')
			) .
			$this->Html->endBox();
	?>
	</div>

</div>
<?php echo $this->Form->end(); ?>
