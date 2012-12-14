<?php

$this->extend('/Common/admin_index');

$this->Html
	->addCrumb('', '/admin', array('icon' => 'home'))
	->addCrumb(__('File Manager'), $this->here);

?>

<?php $this->start('actions'); ?>
<li>
	<?php
		echo $this->FileManager->link(__('Upload here'),
			array('controller' => 'file_manager', 'action' => 'upload'),
			$path
		);
	?>
</li>
<li>
	<?php
		echo $this->FileManager->link(__('Create directory'),
			array('controller' => 'file_manager', 'action' => 'create_directory'),
			$path
		);
	?>
</li>
<li>
	<?php
		echo $this->FileManager->link(__('Create file'),
			array('controller' => 'file_manager', 'action' => 'create_file'),
			$path
		);
	?>
</li>
<?php $this->end(); ?>

<div class="breadcrumb">
	<a href="#"><?php echo __('You are here') . ' '; ?> </a> <span class="divider"> &gt; </span>
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
			__('Directory content'),
			__('Actions'),
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
			$actions[] = $this->FileManager->linkDirectory(__('Open'), $path.$directory.DS);
			if ($this->FileManager->inPath($deletablePaths, $path.$directory)) {
				$actions[] = $this->FileManager->link(__('Delete'), array(
					'controller' => 'file_manager',
					'action' => 'delete_directory',
				), $path . $directory);
			}
			$actions = $this->Html->div('item-actions', implode(' ', $actions));
			$rows[] = array(
				$this->Html->image('/img/icons/folder.png'),
				$this->FileManager->linkDirectory($directory, $path . $directory . DS),
				$actions,
			);
		endforeach;
		echo $this->Html->tableCells($rows, array('class' => 'directory'), array('class' => 'directory'));

		// files
		$rows = array();
		foreach ($content['1'] as $file):
			$actions = array();
			$actions[] = $this->FileManager->link(__('Edit'), array('controller' => 'file_manager', 'action' => 'editfile'), $path.$file);
			if ($this->FileManager->inPath($deletablePaths, $path.$file)) {
				$actions[] = $this->FileManager->link(__('Delete'), array(
					'controller' => 'file_manager',
					'action' => 'delete_file',
				), $path . $file);
			}
			$actions = $this->Html->div('item-actions', implode(' ', $actions));
			$rows[] = array(
				$this->Html->image('/img/icons/' . $this->FileManager->filename2icon($file)),
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