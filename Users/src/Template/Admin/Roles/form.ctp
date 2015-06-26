<?php
$this->extend('Croogo/Core./Common/admin_edit');
$this->CroogoHtml
	->addCrumb($this->CroogoHtml->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Users'), array('plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Roles'), array('plugin' => 'Croogo/Users', 'controller' => 'Roles', 'action' => 'index'));

if ($this->request->param('action') == 'edit') {
	$this->CroogoHtml->addCrumb($role->title, '/' . $this->request->url);
}

if ($this->request->param('action') == 'add') {
	$this->CroogoHtml->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}
?>
<?php echo $this->CroogoForm->create($role);?>

<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
			<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Role'), '#role-main');
			echo $this->Croogo->adminTabs();
			?>
		</ul>

		<div class="tab-content">
			<div id="role-main" class="tab-pane">
				<?php
				echo $this->CroogoForm->input('id');
				$this->CroogoForm->templates(array(
					'class' => 'span10',
				));
				echo $this->CroogoForm->input('title', array(
					'label' => __d('croogo', 'Title'),
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
			$this->CroogoHtml->link(
				__d('croogo', 'Cancel'), array('action' => 'index'),
				array('button' => 'danger')
			) .
			$this->CroogoHtml->endBox();
		echo $this->Croogo->adminBoxes();
		?>
	</div>

</div>
<?php echo $this->CroogoForm->end(); ?>
