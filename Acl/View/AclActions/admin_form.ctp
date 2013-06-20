<?php
$this->extend('/Common/admin_edit');
$this->set('className', 'acl_actions');
$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Users'), array('plugin' => 'users', 'controller' => 'users', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Permissions'), array('plugin' => 'acl', 'controller' => 'acl_permissions'))
	->addCrumb(__d('croogo', 'Actions'), array('plugin' => 'acl', 'controller' => 'acl_actions', 'action' => 'index'));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->data['Aco']['id'] . ': ' . $this->data['Aco']['alias'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

?>
<h2 class="hidden-desktop"><?php echo $title_for_layout; ?></h2>
<?php echo $this->Form->create('Aco', array('url' => array('controller' => 'acl_actions', 'action' => 'add'))); ?>

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
				echo $this->Form->input('id');
				echo $this->Form->input('parent_id', array(
					'options' => $acos,
					'empty' => true,
					'label' => __d('croogo', 'Parent'),
					'rel' => __d('croogo', 'Choose none if the Aco is a controller.'),
				));
				$this->Form->inputDefaults(array(
					'label' => false,
					'class' => 'span10',
				));
				echo $this->Form->input('alias', array(
					'label' => __d('croogo', 'Alias'),
				));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
			$this->Form->button(__d('croogo', 'Submit'), array('name' => 'apply', 'class' => 'btn')) .
			$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('class' => 'cancel btn btn-danger')) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
