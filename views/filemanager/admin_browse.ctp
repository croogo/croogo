<div class="filemanager folder">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $this->Filemanager->link(__('Upload here', true), array('controller' => 'filemanager', 'action' => 'upload'), $path); ?></li>
			<li><?php echo $this->Filemanager->link(__('Create directory', true), array('controller' => 'filemanager', 'action' => 'create_directory'), $path); ?></li>
			<li><?php echo $this->Filemanager->link(__('Create file', true), array('controller' => 'filemanager', 'action' => 'create_file'), $path); ?></li>
		</ul>
	</div>

	<div class="breadcrumb">
	<?php
		echo __('You are here:', true) . ' ';
		$breadcrumb = $this->Filemanager->breadcrumb($path);
		foreach ($breadcrumb AS $pathname => $p) {
			echo $this->Filemanager->linkDirectory($pathname, $p);
			echo DS;
		}
	?>
	</div>
	
	<div class="directory-content">
		<table cellpadding="0" cellspacing="0">
		<?php
			$tableHeaders =  $this->Html->tableHeaders(array(
				'',
				__('Directory content', true),
				__('Actions', true),
			));
			echo $tableHeaders;

			// directories
			$rows = array();
			foreach ($content['0'] AS $directory) {
				$actions = $this->Filemanager->linkDirectory(__('Open', true), $path.$directory.DS);
				if ($this->Filemanager->inPath($deletablePaths, $path.$directory)) {
					$actions .= ' ' . $this->Filemanager->link(__('Delete', true), array(
						'controller' => 'filemanager',
						'action' => 'delete_directory',
						'token' => $this->params['_Token']['key'],
					), $path.$directory);
				}
				$rows[] = array(
					$this->Html->image('/img/icons/folder.png'),
					$this->Filemanager->linkDirectory($directory, $path.$directory.DS),
					$actions,
				);
			}
			echo $this->Html->tableCells($rows, array('class' => 'directory'), array('class' => 'directory'));

			// files
			$rows = array();
			foreach ($content['1'] AS $file) {
				$actions = $this->Filemanager->link(__('Edit', true), array('controller' => 'filemanager', 'action' => 'editfile'), $path.$file);
				if ($this->Filemanager->inPath($deletablePaths, $path.$file)) {
					$actions .= $this->Filemanager->link(__('Delete', true), array(
						'controller' => 'filemanager',
						'action' => 'delete_file',
						'token' => $this->params['_Token']['key'],
					), $path.$file);
				}
				$rows[] = array(
					$this->Html->image('/img/icons/'.$this->Filemanager->filename2icon($file)),
					$this->Filemanager->linkFile($file, $path.$file),
					$actions,
				);
			}
			echo $this->Html->tableCells($rows, array('class' => 'file'), array('class' => 'file'));

			echo $tableHeaders;
		?>
		</table>
	</div>
</div>