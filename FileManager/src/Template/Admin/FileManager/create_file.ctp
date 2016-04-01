<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Html
	->addCrumb(__d('croogo', 'File Manager'), array('plugin' => 'Croogo/FileManager', 'controller' => 'FileManager', 'action' => 'browse'))
	->addCrumb(__d('croogo', 'Create File'), '/' . $this->request->url);

$this->append('page-heading');
?>
<div class="breadcrumb">
	<a href="#"><?php echo __d('croogo', 'You are here') . ' '; ?> </a> <span class="divider"> &gt; </span>
	<?php
	$breadcrumb = $this->FileManager->breadcrumb($path);
	foreach ($breadcrumb as $pathname => $p):
		echo $this->FileManager->linkDirectory($pathname, $p);
		echo $this->Html->tag('span', DS, array('class' => 'divider'));
	endforeach;
	?>
</div> &nbsp;
<?php
$this->end();

$this->append('form-start', $this->Form->create('FileManager', array(
	'url' => $this->Url->build(array(
		'controller' => 'FileManager',
		'action' => 'create_file',
	), true) . '?path=' . urlencode($path),
)));

$this->append('tab-heading');
	echo $this->Croogo->adminTab(__d('croogo', 'File'), '#filemanager-createfile');
	echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');
	echo $this->Html->tabStart('filemanager-createfile') .
		$this->Form->input('FileManager.name', array(
			'type' => 'text',
			'label' => __d('croogo', 'Filename'),
		));
	echo $this->Html->tabEnd();

	echo $this->Croogo->adminTabs();
$this->end();

$this->append('panels');
	echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
		$this->Form->button(__d('croogo', 'Save')) .
		$this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger'));
	echo $this->Html->endBox();
$this->end();

$this->append('form-end', $this->Form->end());
