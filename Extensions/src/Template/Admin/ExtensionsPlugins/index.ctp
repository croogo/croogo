<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->name = 'extensions-plugins';

$this->Html->addCrumb(__d('croogo', 'Extensions'),
        ['plugin' => 'Croogo/Extensions', 'controller' => 'ExtensionsPlugins', 'action' => 'index'])
    ->addCrumb(__d('croogo', 'Plugins'));

$this->start('actions');
echo $this->Croogo->adminAction(__d('croogo', 'Upload'), ['action' => 'add'], ['class' => 'btn btn-success']);
$this->end(); ?>

<table class="table table-striped">
    <?php
    $tableHeaders = $this->Html->tableHeaders([
        '',
        __d('croogo', 'Alias'),
        __d('croogo', 'Name'),
        __d('croogo', 'Description'),
        __d('croogo', 'Active'),
        __d('croogo', 'Actions'),
    ]);
    ?>
    <thead>
        <?php echo $tableHeaders; ?>
    </thead>

    <?php
    $rows = [];
    foreach ($plugins as $pluginAlias => $pluginData):
        $toggleText = $pluginData['active'] ? __d('croogo', 'Deactivate') : __d('croogo', 'Activate');
        $statusIcon = $this->Html->status($pluginData['active']);

        $actions = [];
        $queryString = ['name' => $pluginAlias];
        if (!in_array($pluginAlias, $bundledPlugins) && !in_array($pluginAlias, $corePlugins)):
            $icon = $pluginData['active'] ? $this->Theme->getIcon('power-off') : $this->Theme->getIcon('power-on');
            $actions[] = $this->Croogo->adminRowAction('', ['action' => 'toggle', '?' => $queryString],
                ['icon' => $icon, 'tooltip' => $toggleText, 'method' => 'post']);
            $actions[] = $this->Croogo->adminRowAction('', ['action' => 'delete', '?' => $queryString],
                ['icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Delete')],
                __d('croogo', 'Are you sure?'));
        endif;

        if ($pluginData['active'] &&
            !in_array($pluginAlias, $bundledPlugins) &&
            !in_array($pluginAlias, $corePlugins)
        ) {
            $actions[] = $this->Croogo->adminRowAction('', ['action' => 'moveup', '?' => $queryString],
                ['icon' => $this->Theme->getIcon('move-up'), 'tooltip' => __d('croogo', 'Move up'), 'method' => 'post'],
                __d('croogo', 'Are you sure?'));

            $actions[] = $this->Croogo->adminRowAction('', ['action' => 'movedown', '?' => $queryString], [
                    'icon' => $this->Theme->getIcon('move-down'),
                    'tooltip' => __d('croogo', 'Move down'),
                    'method' => 'post',
                ], __d('croogo', 'Are you sure?'));
        }

        if ($pluginData['needMigration']) {
            $actions[] = $this->Croogo->adminRowAction(__d('croogo', 'Migrate'), [
                'action' => 'migrate',
                '?' => $queryString,
            ], [], __d('croogo', 'Are you sure?'));
        }

        $actions = $this->Html->div('item-actions', implode(' ', $actions));

        $rows[] = [
            '',
            $pluginAlias,
            $pluginData['name'],
            !empty($pluginData['description']) ? $pluginData['description'] : '',
            $statusIcon,
            $actions,
        ];
    endforeach;

    echo $this->Html->tableCells($rows);
    ?>
</table>
