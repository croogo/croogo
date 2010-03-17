<div class="filemanager folder">
	<h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $filemanager->link(__('Upload here', true), array('controller' => 'filemanager', 'action' => 'upload'), $path); ?></li>
            <li><?php echo $filemanager->link(__('Create directory', true), array('controller' => 'filemanager', 'action' => 'create_directory'), $path); ?></li>
            <li><?php echo $filemanager->link(__('Create file', true), array('controller' => 'filemanager', 'action' => 'create_file'), $path); ?></li>
        </ul>
    </div>

	<div class="breadcrumb">
	<?php
        echo __('You are here:', true) . ' ';
        $breadcrumb = $filemanager->breadcrumb($path);
        foreach ($breadcrumb AS $pathname => $p) {
            echo $filemanager->linkDirectory($pathname, $p);
            echo DS;
        }
	?>
	</div>
	
	<div class="directory-content">
        <table cellpadding="0" cellspacing="0">
        <?php
            $tableHeaders =  $html->tableHeaders(array(
                '',
                __('Directory content', true),
                __('Actions', true),
            ));
            echo $tableHeaders;

            // directories
            $rows = array();
            foreach ($content['0'] AS $directory) {
                $actions = $filemanager->linkDirectory(__('Open', true), $path.$directory.DS);
                if ($filemanager->inPath($deletablePaths, $path.$directory)) {
                    $actions .= ' ' . $filemanager->link(__('Delete', true), array(
                        'controller' => 'filemanager',
                        'action' => 'delete_directory',
                        'token' => $this->params['_Token']['key'],
                    ), $path.$directory);
                }
                $rows[] = array(
                    $html->image('/img/icons/folder.png'),
                    $filemanager->linkDirectory($directory, $path.$directory.DS),
                    $actions,
                );
            }
            echo $html->tableCells($rows, array('class' => 'directory'), array('class' => 'directory'));

            // files
            $rows = array();
            foreach ($content['1'] AS $file) {
                $actions = $filemanager->link(__('Edit', true), array('controller' => 'filemanager', 'action' => 'editfile'), $path.$file);
                if ($filemanager->inPath($deletablePaths, $path.$file)) {
                    $actions .= $filemanager->link(__('Delete', true), array(
                        'controller' => 'filemanager',
                        'action' => 'delete_file',
                        'token' => $this->params['_Token']['key'],
                    ), $path.$file);
                }
                $rows[] = array(
                    $html->image('/img/icons/'.$filemanager->filename2icon($file)),
                    $filemanager->linkFile($file, $path.$file),
                    $actions,
                );
            }
            echo $html->tableCells($rows, array('class' => 'file'), array('class' => 'file'));

            echo $tableHeaders;
        ?>
        </table>
	</div>
</div>