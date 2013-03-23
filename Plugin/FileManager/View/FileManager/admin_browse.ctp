<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__d('croogo', 'File Manager'), $this->here);

?>

<?php $this->start('actions'); ?>
<?php
	echo $this->FileManager->adminAction(__d('croogo', 'Upload here'),
		array('controller' => 'file_manager', 'action' => 'upload'),
		$path
	);
	echo $this->FileManager->adminAction(__d('croogo', 'Create directory'),
		array('controller' => 'file_manager', 'action' => 'create_directory'),
		$path
	);
	echo $this->FileManager->adminAction(__d('croogo', 'Create file'),
		array('controller' => 'file_manager', 'action' => 'create_file'),
		$path
	);
?>
<?php $this->end(); ?>

<div class="breadcrumb">
	<a href="#"><?php echo __d('croogo', 'You are here') . ' '; ?> </a> <span class="divider"> &gt; </span>
	<?php $breadcrumb = $this->FileManager->breadcrumb($path); ?>
	<?php foreach ($breadcrumb as $pathname => $p) : ?>
		<?php echo $this->FileManager->linkDirectory($pathname, $p); ?>
		<span class="divider"> <?php echo DS; ?> </span>
	<?php endforeach; ?>
</div>

&nbsp;

<div class="directory-content">
	<table class="table table-striped">
	<?php
		$tableHeaders = $this->Html->tableHeaders(array(
			'',
			__d('croogo', 'Directory content'),
			__d('croogo', 'Actions'),
		));
	?>
		<thead>
			<?php echo $tableHeaders; ?>
		</thead>
	<?php
		// directories
		$rows = array();
		foreach ($content['0'] as $directory):
			$actions = array();
			$actions[] = $this->FileManager->linkDirectory(__d('croogo', 'Open'), $path.$directory.DS);
			if ($this->FileManager->inPath($deletablePaths, $path.$directory)) {
				$actions[] = $this->FileManager->link(__d('croogo', 'Delete'), array(
					'controller' => 'file_manager',
					'action' => 'delete_directory',
				), $path . $directory);
			}
			$actions = $this->Html->div('item-actions', implode(' ', $actions));
			$rows[] = array(
				$this->Html->image('/croogo/img/icons/folder.png'),
				$this->FileManager->linkDirectory($directory, $path . $directory . DS),
				$actions,
			);
		endforeach;
		echo $this->Html->tableCells($rows, array('class' => 'directory'), array('class' => 'directory'));

		// files
		$rows = array();
		foreach ($content['1'] as $file):
			$actions = array();
			$actions[] = $this->FileManager->link(__d('croogo', 'Edit'), array('controller' => 'file_manager', 'action' => 'editfile'), $path.$file);
			if ($this->FileManager->inPath($deletablePaths, $path.$file)) {
				$actions[] = $this->FileManager->link(__d('croogo', 'Delete'), array(
					'controller' => 'file_manager',
					'action' => 'delete_file',
				), $path . $file);
			}
			$actions = $this->Html->div('item-actions', implode(' ', $actions));
			$rows[] = array(
				$this->Html->image('/croogo/img/icons/' . $this->FileManager->filename2icon($file)),
				$this->FileManager->linkFile($file, $path . $file),
				$actions,
			);
		endforeach;
		echo $this->Html->tableCells($rows, array('class' => 'file'), array('class' => 'file'));

	?>
		<thead>
			<?php echo $tableHeaders; ?>
		</thead>
	</table>
</div>