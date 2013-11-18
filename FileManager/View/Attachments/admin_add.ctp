<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'Attachments'), array('plugin' => 'file_manager', 'controller' => 'attachments', 'action' => 'index'))
	->addCrumb(__d('croogo', 'Upload'), '/' . $this->request->url);

$formUrl = array('controller' => 'attachments', 'action' => 'add');
if (isset($this->params['named']['editor'])) {
	$formUrl['editor'] = 1;
}
echo $this->Form->create('Attachment', array('url' => $formUrl, 'type' => 'file'));

?>
<div class="row-fluid">
	<div class="span8">

		<ul class="nav nav-tabs">
		<?php
			echo $this->Croogo->adminTab(__d('croogo', 'Upload'), '#attachment-upload');
		?>
		</ul>

		<div class="tab-content">

			<div id="attachment-upload" class="tab-pane">
			<?php
			echo $this->Form->input('file', array('label' => __d('croogo', 'Upload'), 'type' => 'file'));
			?>
			</div>

			<?php echo $this->Croogo->adminTabs(); ?>
		</div>
	</div>

	<div class="span4">
	<?php
		$redirect = array('action' => 'index');
		if ($this->Session->check('Wysiwyg.redirect')) {
			$redirect = $this->Session->read('Wysiwyg.redirect');
		}
		echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
			$this->Form->button(__d('croogo', 'Upload'), array('button' => 'default')) .
			$this->Form->end() .
			$this->Html->link(__d('croogo', 'Cancel'), $redirect, array('button' => 'danger')) .
			$this->Html->endBox();
		echo $this->Croogo->adminBoxes();
	?>
	</div>

</div>
<?php echo $this->Form->end(); ?>