<?php
$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb($this->Html->icon('home'), '/admin')
	->addCrumb(__d('croogo', 'Settings'), array(
		'admin' => true,
		'plugin' => 'settings',
		'controller' => 'settings',
		'action' => 'index',
	));

if ($this->request->params['action'] == 'admin_edit') {
	$this->Html->addCrumb($this->data['Setting']['key'], '/' . $this->request->url);
}

if ($this->request->params['action'] == 'admin_add') {
	$this->Html->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);
}

echo $this->Form->create('Setting');

?>
<div class="row-fluid">
	<div class="span8">
		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Settings'), '#setting-basic');
			echo $this->Croogo->adminTab(__d('croogo', 'Misc'), '#setting-misc');
		?>
		</ul>

		<div class="tab-content">
			<div id="setting-basic" class="tab-pane">
			<?php
				echo $this->Form->input('id');
				$this->Form->inputDefaults(array(
					'label' => false,
					'class' => 'span10',
				));
				echo $this->Form->input('key', array(
					'rel' => __d('croogo', "e.g., 'Site.title'"),
					'label' => __d('croogo', 'Key'),
				));
				echo $this->Form->input('value', array(
					'label' => __d('croogo', 'Value'),
				));
			?>
			</div>

			<div id="setting-misc" class="tab-pane">
			<?php
				echo $this->Form->input('title', array(
					'label' => __d('croogo', 'Title'),
				));
				echo $this->Form->input('description', array(
					'label' => __d('croogo', 'Description'),
				));
				echo $this->Form->input('input_type', array(
					'label' => __d('croogo', 'Input Type'),
					'rel' => __d('croogo', "e.g., 'text' or 'textarea'"),
				));
				echo $this->Form->input('editable', array(
					'label' => __d('croogo', 'Editable'),
					'class' => false,
				));
				echo $this->Form->input('params', array(
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
			$this->Form->button(__d('croogo', 'Save'), array('button' => 'default')) .
			$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array(
				'button' => 'danger')) .
			$this->Html->endBox();

		echo $this->Croogo->adminBoxes();
	?>
	</div>

</div>
<?php echo $this->Form->end(); ?>