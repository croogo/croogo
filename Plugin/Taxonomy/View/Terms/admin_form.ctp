<?php
$this->Html->script(array('/taxonomy/js/terms'), false);

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html
		->addCrumb(__('Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index'))
		->addCrumb($vocabulary['Vocabulary']['title'], array('plugin' => 'taxonomy', 'controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id']))
		->addCrumb($this->request->data['Term']['title'], $this->here);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html
		->addCrumb(__('Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index', $vocabulary['Vocabulary']['id'],))
		->addCrumb($vocabulary['Vocabulary']['title'], array('plugin' => 'taxonomy', 'controller' => 'terms', 'action' => 'index'))
		->addCrumb(__('Add'), $this->here);
}

echo $this->Form->create('Term', array(
	'url' => '/' . $this->request->url,
));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<li><a href="#term-basic" data-toggle="tab"><?php echo __('Term'); ?></a></li>
			<?php echo $this->Croogo->adminTabs(); ?>
		</ul>

		<div class="tab-content">
			<div id="term-basic" class="tab-pane">
			<?php
				echo $this->Form->input('Taxonomy.parent_id', array(
					'options' => $parentTree,
					'empty' => true,
					'label' => __('Parent'),
				));
				$this->Form->inputDefaults(array(
					'class' => 'span10',
					'label' => false,
				));
				echo $this->Form->input('title', array(
					'placeholder' => __('Title'),
				));
				echo $this->Form->input('slug', array(
					'placeholder' => __('Slug'),
					'class' => 'slug span10',
				));
				echo $this->Form->input('description', array(
					'placeholder' => __('Description'),
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
				__('Cancel'),
				array('action' => 'index', $vocabularyId),
				array('button' => 'danger')
			) .
			$this->Html->endBox();
		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
