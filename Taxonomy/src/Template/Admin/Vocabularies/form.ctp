<?php
$this->Croogo->adminScript('Croogo/Taxonomy.vocabularies');

$this->extend('Croogo/Croogo./Common/admin_edit');

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index'));

if ($this->request->params['action'] == 'edit') {
	$this->assign('title', __d('croogo', 'Edit Vocabulary'));

	$this->CroogoHtml
		->addCrumb(__d('croogo', 'Vocabularies'), array('action' => 'index', $vocabulary->id))
		->addCrumb($vocabulary->title, '/' . $this->request->url);
}

if ($this->request->params['action'] == 'add') {
	$this->assign('title', __d('croogo', 'Add Vocabulary'));

	$this->CroogoHtml
		->addCrumb(__d('croogo', 'Vocabularies'), array('action' => 'index'))
		->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

echo $this->CroogoForm->create($vocabulary);

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
				echo $this->CroogoForm->input('id');
				$this->CroogoForm->templates(array(
					'class' => 'span10',
				));
				echo $this->CroogoForm->input('title', array(
					'label' => __d('croogo', 'Title'),
				));
				echo $this->CroogoForm->input('alias', array(
					'class' => 'alias span10',
					'label' => __d('croogo', 'Alias'),
				));
				echo $this->CroogoForm->input('description', array(
					'label' => __d('croogo', 'Description'),
				));
				echo $this->CroogoForm->input('Type.Type', array(
					'label' => __d('croogo', 'Type'),
				));
			?>
			</div>

			<div id="vocabulary-options" class="tab-pane">
			<?php
				echo $this->CroogoForm->input('required', array(
					'label' => __d('croogo', 'Required'),
					'class' => false,
				));
				echo $this->CroogoForm->input('multiple', array(
					'label' => __d('croogo', 'Multiple'),
					'class' => false,
				));
				echo $this->CroogoForm->input('tags', array(
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
		echo $this->CroogoHtml->beginBox(__d('croogo', 'Publishing')) .
			$this->CroogoForm->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
			$this->CroogoForm->button(__d('croogo', 'Save'), array('button' => 'success')) .
			$this->CroogoHtml->link(
				__d('croogo', 'Cancel'),
				array('action' => 'index'),
				array('button' => 'danger')
			) .
			$this->CroogoHtml->endBox();
	?>
	</div>

</div>
<?php echo $this->CroogoForm->end(); ?>
