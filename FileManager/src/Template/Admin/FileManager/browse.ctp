<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->assign('title', __d('croogo', 'File Manager'));
$this->Breadcrumbs->add(__d('croogo', 'File Manager'), $this->request->getRequestTarget());

?>

<?php $this->start('action-buttons'); ?>
<div class="btn-group">
    <?php
    echo $this->FileManager->adminAction(__d('croogo', 'Upload here'),
        ['controller' => 'FileManager', 'action' => 'upload'], $path);
    echo $this->FileManager->adminAction(__d('croogo', 'Create directory'),
        ['controller' => 'FileManager', 'action' => 'create_directory'], $path);
    echo $this->FileManager->adminAction(__d('croogo', 'Create file'),
        ['controller' => 'FileManager', 'action' => 'create_file'], $path);
    ?>
</div>
<?php $this->end(); ?>

<?= $this->element('Croogo/FileManager.admin/breadcrumbs') ?>

<div class="directory-content">
    <table class="table table-striped">
        <?php
        $tableHeaders = $this->Html->tableHeaders([
            '',
            __d('croogo', 'Directory content'),
            __d('croogo', 'Actions'),
        ]);
        ?>
        <thead>
            <?php echo $tableHeaders; ?>
        </thead>
        <?php
        // directories
        $rows = [];
        foreach ($content['0'] as $directory):
            $actions = [];
            $fullpath = $path . $directory;
            $actions[] = $this->FileManager->linkDirectory(__d('croogo', 'Open'), $fullpath . DS);
            if ($this->FileManager->inPath($deletablePaths, $fullpath)) {
                $actions[] = $this->FileManager->link(__d('croogo', 'Delete'), [
                    'controller' => 'FileManager',
                    'action' => 'delete_directory',
                ], $fullpath);
            }
            $actions[] = $this->FileManager->link(__d('croogo', 'Rename'), [
                'controller' => 'FileManager',
                'action' => 'rename',
            ], $fullpath);
            $actions = $this->Html->div('item-actions', implode(' ', $actions));
            $rows[] = [
                $this->Html->image('/croogo/core/img/icons/folder.png'),
                $this->FileManager->linkDirectory($directory, $fullpath . DS),
                $actions,
            ];
        endforeach;
        echo $this->Html->tableCells($rows, ['class' => 'directory-listing'], ['class' => 'directory-listing']);

        // files
        $rows = [];
        foreach ($content['1'] as $file):
            $actions = [];
            $fullpath = $path . $file;
            $icon = $this->FileManager->filename2icon($file);
            if ($icon == 'picture.png'):
                $image = '/' . str_replace(WWW_ROOT, '', $fullpath);
                $lightboxOptions = [
                    'data-toggle' => 'lightbox',
                    'escape' => false,
                ];
                $linkFile = $this->Html->link($file, $image, $lightboxOptions);
                $actions[] = $this->Html->link(__d('croogo', 'View'), $image, $lightboxOptions);
            else:
                $linkFile = $this->FileManager->linkFile($file, $fullpath);
                $actions[] = $this->FileManager->link(__d('croogo', 'Edit'), [
                        'plugin' => 'Croogo/FileManager',
                        'controller' => 'FileManager',
                        'action' => 'edit_file',
                    ], $fullpath);
            endif;
            if ($this->FileManager->inPath($deletablePaths, $fullpath)) {
                $actions[] = $this->FileManager->link(__d('croogo', 'Delete'), [
                    'plugin' => 'Croogo/FileManager',
                    'controller' => 'FileManager',
                    'action' => 'delete_file',
                ], $fullpath);
            }
            $actions[] = $this->FileManager->link(__d('croogo', 'Rename'), [
                'plugin' => 'Croogo/FileManager',
                'controller' => 'FileManager',
                'action' => 'rename',
            ], $fullpath);
            $actions = $this->Html->div('item-actions', implode(' ', $actions));
            $rows[] = [
                $this->Html->image('/croogo/core/img/icons/' . $icon),
                $linkFile,
                $actions,
            ];
        endforeach;
        echo $this->Html->tableCells($rows, ['class' => 'file-listing'], ['class' => 'file-listing']);

        ?>
        <thead>
            <?php echo $tableHeaders; ?>
        </thead>
    </table>
</div>
