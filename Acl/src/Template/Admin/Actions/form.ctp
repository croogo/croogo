<?php
$this->extend('Croogo/Core./Common/admin_edit');

$this->CroogoHtml
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Users'), array('plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Permissions'), array('plugin' => 'Croogo/Acl', 'controller' => 'Permissions'))
	->addCrumb(__d('croogo', 'Actions'), array('plugin' => 'Croogo/Acl', 'controller' => 'Actions', 'action' => 'index'));

if ($this->request->param('action') == 'edit') {
	$this->CroogoHtml->addCrumb($aco->id . ': ' . $aco->alias, '/' . $this->request->url);
}

if ($this->request->param('action') == 'add') {
	$this->CroogoHtml->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}
?>
<?php echo $this->CroogoForm->create($aco); ?>

<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
		echo $this->Croogo->adminTab(__d('croogo', 'Action'), '#action-main');
		echo $this->Croogo->adminTabs();
		?>
		</ul>

		<div class="tab-content">
			<div id="action-main" class="tab-pane">
			<?php
			echo $this->CroogoForm->input('parent_id', array(
				'options' => $acos,
				'empty' => true,
				'label' => __d('croogo', 'Parent'),
				'help' => __d('croogo', 'Choose none if the Aco is a controller.'),
			));
			$this->CroogoForm->templates(array(
				'class' => 'span10',
			));
			echo $this->CroogoForm->input('alias', array(
				'label' => __d('croogo', 'Alias'),
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
			$this->CroogoHtml->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('class' => 'cancel btn btn-danger')) .
			$this->CroogoHtml->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->CroogoForm->end(); ?>
