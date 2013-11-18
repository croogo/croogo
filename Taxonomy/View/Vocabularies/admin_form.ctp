<?php
$this->Html->script(array('/taxonomy/js/vocabularies'), false);
$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'nodes', 'controller' => 'nodes', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html
		->addCrumb(__d('croogo', 'Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index', $this->request->data['Vocabulary']['id'],))
		->addCrumb($this->request->data['Vocabulary']['title'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html
		->addCrumb(__d('croogo', 'Vocabularies'), array('plugin' => 'taxonomy', 'controller' => 'vocabularies', 'action' => 'index',))
		->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

echo $this->Form->create('Vocabulary');

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Vocabulary'), '#vocabulary-basic');
			echo $this->Croogo->adminTab(__d('croogo', 'Options'), '#vocabulary-options');
			echo $this->Croogo->adminTabs();
		?>
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
					'label' => __d('croogo', 'Title'),
				));
				echo $this->Form->input('alias', array(
					'class' => 'alias span10',
					'label' => __d('croogo', 'Alias'),
				));
				echo $this->Form->input('description', array(
					'label' => __d('croogo', 'Description'),
				));
				echo $this->Form->input('Type.Type', array(
					'label' => __d('croogo', 'Type'),
				));
			?>
			</div>

			<div id="vocabulary-options" class="tab-pane">
			<?php
				echo $this->Form->input('required', array(
					'label' => __d('croogo', 'Required'),
					'class' => false,
				));
				echo $this->Form->input('multiple', array(
					'label' => __d('croogo', 'Multiple'),
					'class' => false,
				));
				echo $this->Form->input('tags', array(
					'label' => __d('croogo', 'Tags'),
					'class' => false,
				));
			?>
			</div>

			<?php echo $this->Croogo->adminBoxes(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
			$this->Form->button(__d('croogo', 'Save'), array('button' => 'default')) .
			$this->Html->link(
				__d('croogo', 'Cancel'),
				array('action' => 'index'),
				array('button' => 'danger')
			) .
			$this->Html->endBox();
	?>
	</div>

</div>
<?php echo $this->Form->end(); ?>
