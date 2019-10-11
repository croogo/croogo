<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs
    ->add(__d('croogo', 'Settings'), [
        'prefix' => 'admin',
        'plugin' => 'Croogo/Settings',
        'controller' => 'Settings',
        'action' => 'index',
    ]);

$key = $this->getRequest()->getQuery('key');
if ($key) {
    $this->Breadcrumbs->add($key);
}

$this->start('table-heading');
    $tableHeaders = $this->Html->tableHeaders([
        $this->Paginator->sort('id', __d('croogo', 'Id')),
        $this->Paginator->sort('key', __d('croogo', 'Key')),
        $this->Paginator->sort('value', __d('croogo', 'Value')),
        $this->Paginator->sort('editable', __d('croogo', 'Editable')),
        __d('croogo', 'Actions'),
    ]);
    echo $tableHeaders;
    $this->end();

    $this->append('table-body');
    $rows = [];
    foreach ($settings as $setting) :
        $actions = [];
        $actions[] = $this->Croogo->adminRowAction(
            '',
            ['controller' => 'Settings', 'action' => 'moveup', $setting->id],
            [
            'icon' => $this->Theme->getIcon('move-up'),
            'escapeTitle' => false,
            'tooltip' => __d('croogo', 'Move up'),
            ]
        );
        $actions[] = $this->Croogo->adminRowAction(
            '',
            ['controller' => 'Settings', 'action' => 'movedown', $setting->id],
            [
            'icon' => $this->Theme->getIcon('move-down'),
            'escapeTitle' => false,
            'tooltip' => __d('croogo', 'Move down')
            ]
        );
        $actions[] = $this->Croogo->adminRowAction(
            '',
            ['controller' => 'Settings', 'action' => 'edit', $setting->id],
            [
            'icon' => $this->Theme->getIcon('update'),
            'escapeTitle' => false,
            'tooltip' => __d('croogo', 'Edit this item')
            ]
        );
        $actions[] = $this->Croogo->adminRowActions($setting->id);
        $actions[] = $this->Croogo->adminRowAction(
            '',
            ['controller' => 'Settings', 'action' => 'delete', $setting->id],
            [
            'icon' => $this->Theme->getIcon('delete'),
            'escapeTitle' => false,
            'tooltip' => __d('croogo', 'Remove this item')
            ],
            __d('croogo', 'Are you sure?')
        );

        $key = $setting->key;
        $keyE = explode('.', $key);
        $keyPrefix = $keyE['0'];
        if (isset($keyE['1'])) {
            $keyTitle = '.' . $keyE['1'];
        } else {
            $keyTitle = '';
        }
        $actions = $this->Html->div('item-actions', implode(' ', $actions));
        $rows[] = [
            $setting->id,
            $this->Html->link($keyPrefix, ['controller' => 'Settings', 'action' => 'index', '?' => ['key' => $keyPrefix]]) . $keyTitle,
            $this->Text->truncate(h($setting->value), 20),
            $this->Html->status($setting->editable),
            $actions,
        ];
    endforeach;

    echo $this->Html->tableCells($rows);
    $this->end();
