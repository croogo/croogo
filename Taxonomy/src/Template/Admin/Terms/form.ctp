<?php
$this->extend('Croogo/Croogo./Common/admin_edit');

$this->Croogo->adminScript('Croogo/Taxonomy.terms');

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Content'), array('plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index'));

if ($this->request->params['action'] == 'edit') {
	$this->CroogoHtml
		->addCrumb(__d('croogo', 'Vocabularies'), array('controller' => 'Vocabularies', 'action' => 'index'))
		->addCrumb($vocabulary->title, array('action' => 'index', $vocabulary->id))
		->addCrumb($term->title, '/' . $this->request->url);
}

if ($this->request->params['action'] == 'add') {
	$this->assign('title', __d('croogo', '%s: Add Term', $vocabulary->title));

	$this->CroogoHtml
		->addCrumb(__d('croogo', 'Vocabularies'), array('controller' => 'Vocabularies', 'action' => 'index', $vocabulary->id))
		->addCrumb($vocabulary->title, array('action' => 'index'))
		->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

echo $this->CroogoForm->create($term, array(
	'url' => '/' . $this->request->url,
));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Term'), '#term-basic');
			echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">
			<div id="term-basic" class="tab-pane">
			<?php
				echo $this->CroogoForm->input('taxonomies.0.parent_id', array(
					'options' => $parentTree,
					'empty' => true,
					'label' => __d('croogo', 'Parent'),
				));
				$this->CroogoForm->templates(array(
					'class' => 'span10',
				));
				echo $this->CroogoForm->hidden('taxonomies.0.id');
				echo $this->CroogoForm->hidden('id');
				echo $this->CroogoForm->input('title', array(
					'label' => __d('croogo', 'Title'),
				));
				echo $this->CroogoForm->input('slug', array(
					'label' => __d('croogo', 'Slug'),
					'class' => 'slug span10',
				));
				echo $this->CroogoForm->input('description', array(
					'label' => __d('croogo', 'Description'),
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->CroogoHtml->beginBox(__d('croogo', 'Publishing')) .
			$this->CroogoForm->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
			$this->CroogoForm->button(__d('croogo', 'Save'), array('button' => 'success')) .
			$this->CroogoHtml->link(
				__d('croogo', 'Cancel'),
				array('action' => 'index', $vocabularyId),
				array('button' => 'danger')
			) .
			$this->CroogoHtml->endBox();
		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->CroogoForm->end(); ?>
