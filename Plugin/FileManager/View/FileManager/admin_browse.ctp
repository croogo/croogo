<?php $this->extend('/Common/admin_index'); ?>

<?php $this->start('tabs'); ?>
<li><?php echo $this->FileManager->link(__('Upload here'), array('controller' => 'file_manager', 'action' => 'upload'), $path); ?></li>
<li><?php echo $this->FileManager->link(__('Create directory'), array('controller' => 'file_manager', 'action' => 'create_directory'), $path); ?></li>
<li><?php echo $this->FileManager->link(__('Create file'), array('controller' => 'file_manager', 'action' => 'create_file'), $path); ?></li>
<?php $this->end(); ?>

<div class="breadcrumb">
<?php
	echo __('You are here:') . ' ';
	$breadcrumb = $this->FileManager->breadcrumb($path);
	foreach ($breadcrumb as $pathname => $p) {
		echo $this->FileManager->linkDirectory($pathname, $p);
		echo DS;
	}
?>
</div>

<div class="directory-content">
	<table cellpadding="0" cellspacing="0">
	<?php
		$tableHeaders =  $this->Html->tableHeaders(array(
			'',
			__('Directory content'),
			__('Actions'),
		));
		echo $tableHeaders;

		// directories
		$rows = array();
		foreach ($content['0'] as $directory) {
			$actions = $this->FileManager->linkDirectory(__('Open'), $path.$directory.DS);
			if ($this->FileManager->inPath($deletablePaths, $path.$directory)) {
				$actions .= ' ' . $this->FileManager->link(__('Delete'), array(
					'controller' => 'file_manager',
					'action' => 'delete_directory',
				), $path.$directory);
			}
			$rows[] = array(
				$this->Html->image('/img/icons/folder.png'),
				$this->FileManager->linkDirectory($directory, $path.$directory.DS),
				$actions,
			);
		}
		echo $this->Html->tableCells($rows, array('class' => 'directory'), array('class' => 'directory'));

		// files
		$rows = array();
		foreach ($content['1'] as $file) {
			$actions = $this->FileManager->link(__('Edit'), array('controller' => 'file_manager', 'action' => 'editfile'), $path.$file);
			if ($this->FileManager->inPath($deletablePaths, $path.$file)) {
				$actions .= $this->FileManager->link(__('Delete'), array(
					'controller' => 'file_manager',
					'action' => 'delete_file',
				), $path.$file);
			}
			$rows[] = array(
				$this->Html->image('/img/icons/'.$this->FileManager->filename2icon($file)),
				$this->FileManager->linkFile($file, $path.$file),
				$actions,
			);
		}
		echo $this->Html->tableCells($rows, array('class' => 'file'), array('class' => 'file'));

		echo $tableHeaders;
	?>
	</table>
</div>