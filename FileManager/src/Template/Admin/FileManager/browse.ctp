<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->Html
	->addCrumb(__d('croogo', 'File Manager'), '/' . $this->request->url);

?>

<?php $this->start('actions'); ?>
<div class="btn-group">
<?php
	echo $this->FileManager->adminAction(__d('croogo', 'Upload here'),
		array('controller' => 'FileManager', 'action' => 'upload'),
		$path
	);
	echo $this->FileManager->adminAction(__d('croogo', 'Create directory'),
		array('controller' => 'FileManager', 'action' => 'create_directory'),
		$path
	);
	echo $this->FileManager->adminAction(__d('croogo', 'Create file'),
		array('controller' => 'FileManager', 'action' => 'create_file'),
		$path
	);
?>
</div>
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
			$fullpath = $path . $directory;
			$actions[] = $this->FileManager->linkDirectory(__d('croogo', 'Open'), $fullpath . DS);
			if ($this->FileManager->inPath($deletablePaths, $fullpath)) {
				$actions[] = $this->FileManager->link(__d('croogo', 'Delete'), array(
					'controller' => 'FileManager',
					'action' => 'delete_directory',
				), $fullpath);
			}
			$actions[] = $this->FileManager->link(__d('croogo', 'Rename'), array(
				'controller' => 'FileManager',
				'action' => 'rename',
			), $fullpath);
			$actions = $this->Html->div('item-actions', implode(' ', $actions));
			$rows[] = array(
				$this->Html->image('/croogo/core/img/icons/folder.png'),
				$this->FileManager->linkDirectory($directory, $fullpath . DS),
				$actions,
			);
		endforeach;
		echo $this->Html->tableCells($rows, array('class' => 'directory'), array('class' => 'directory'));

		// files
		$rows = array();
		foreach ($content['1'] as $file):
			$actions = array();
			$fullpath = $path . $file;
			$icon = $this->FileManager->filename2icon($file);
			if ($icon == 'picture.png'):
				$image = '/' . str_replace(WWW_ROOT, '', $fullpath);
				$thickboxOptions = array(
					'class' => 'thickbox', 'escape' => false,
				);
				$linkFile = $this->Html->link($file, $image, $thickboxOptions);
				$actions[] = $this->Html->link(__d('croogo', 'View'),
					$image,
					$thickboxOptions
				);
			else:
				$linkFile = $this->FileManager->linkFile($file, $fullpath);
				$actions[] = $this->FileManager->link(__d('croogo', 'Edit'),
					array(
						'plugin' => 'Croogo/FileManager',
						'controller' => 'FileManager', 'action' => 'edit_file'
					),
					$fullpath
				);
			endif;
			if ($this->FileManager->inPath($deletablePaths, $fullpath)) {
				$actions[] = $this->FileManager->link(__d('croogo', 'Delete'), array(
					'plugin' => 'Croogo/FileManager',
					'controller' => 'FileManager',
					'action' => 'delete_file',
				), $fullpath);
			}
			$actions[] = $this->FileManager->link(__d('croogo', 'Rename'), array(
				'plugin' => 'Croogo/FileManager',
				'controller' => 'FileManager',
				'action' => 'rename',
			), $fullpath);
			$actions = $this->Html->div('item-actions', implode(' ', $actions));
			$rows[] = array(
				$this->Html->image('/croogo/core/img/icons/' . $icon),
				$linkFile,
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
