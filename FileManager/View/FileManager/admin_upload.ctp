<?php

$this->extend('/Common/admin_edit');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'File Manager'), array('plugin' => 'file_manager', 'controller' => 'file_manager', 'action' => 'browse'))
	->addCrumb(__d('croogo', 'Upload'), '/' . $this->request->url);

$this->append('page-heading');
?>
<div class="breadcrumb">
	<a href="#"><?php echo __d('croogo', 'You are here') . ' '; ?> </a> <span class="divider"> &gt; </span>
	<?php $breadcrumb = $this->FileManager->breadcrumb($path); ?>
	<?php foreach ($breadcrumb as $pathname => $p) : ?>
		<?php echo $this->FileManager->linkDirectory($pathname, $p); ?>
			<span class="divider"> <?php echo DS; ?> </span>
	<?php endforeach; ?>
</div> &nbsp;
<?php
$this->end();

$this->append('form-start', $this->Form->create('FileManager', array(
	'type' => 'file',
	'url' => $this->Html->url(array(
		'controller' => 'file_manager',
		'action' => 'upload',
	), true) . '?path=' . urlencode($path),
)));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'Upload'), '#filemanager-upload');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');
	echo $this->Html->tabStart('filemanager-upload') .
		$this->Form->input('FileManager.file', array(
			'type' => 'file',
			'label' => '',
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->append('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Save')) .
		$this->Html->link(__d('croogo', 'Cancel'),
			array('action' => 'index'),
			array('button' => 'danger',
		));
	echo $this->Html->endBox();

	echo $this->Croogo->adminBoxes();
$this->end();

$this->append('form-end', $this->Form->end());
