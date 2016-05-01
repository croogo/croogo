<?php

use Cake\Utility\Inflector;
use Croogo\Core\Status;

$this->Croogo->adminscript('Croogo/Menus.admin');

$this->extend('Croogo/Core./Common/admin_index');

$this->Html->addCrumb(__d('croogo', 'Menus'), ['controller' => 'Menus', 'action' => 'index'])
    ->addCrumb(__d('croogo', $menu->title));

$this->append('actions');
echo $this->Croogo->adminAction(__d('croogo', 'New link'), ['action' => 'add', 'menu_id' => $menu->id], ['button' => 'success']);
$this->end();

$this->append('form-start', $this->Form->create(null, [
    'align' => 'inline',
    'url' => [
        'action' => 'process',
        $menu->id,
    ],
]));

$this->start('table-heading');
$tableHeaders = $this->Html->tableHeaders([
    $this->Form->checkbox('checkAll', ['id' => 'LinksCheckAll']),
    __d('croogo', 'Title'),
    __d('croogo', 'Status'),
    __d('croogo', 'Actions'),
]);
echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');

$rows = [];
foreach ($links as $link):
    $actions = [];
    $actions[] = $this->Croogo->adminRowAction('', [
        'action' => 'moveUp',
        $link->id,
    ], [
        'icon' => $this->Theme->getIcon('move-up'),
        'tooltip' => __d('croogo', 'Move up'),
    ]);
    $actions[] = $this->Croogo->adminRowAction('', [
        'action' => 'moveDown',
        $link->id,
    ], [
        'icon' => $this->Theme->getIcon('move-down'),
        'tooltip' => __d('croogo', 'Move down'),
    ]);
    $actions[] = $this->Croogo->adminRowActions($link->id);
    $actions[] = $this->Croogo->adminRowAction('', [
        'action' => 'edit',
        $link->id,
    ], [
        'icon' => $this->Theme->getIcon('update'),
        'tooltip' => __d('croogo', 'Edit this item'),
    ]);

    $actions[] = $this->Croogo->adminRowAction('', '#Link' . $link->id . 'Id', [
            'icon' => $this->Theme->getIcon('copy'),
            'tooltip' => __d('croogo', 'Create a copy'),
            'rowAction' => 'copy',
        ], __d('croogo', 'Create a copy of this Link?'));

    $actions[] = $this->Croogo->adminRowAction('', '#Link' . $link->id . 'Id', [
            'icon' => $this->Theme->getIcon('delete'),
            'class' => 'delete',
            'tooltip' => __d('croogo', 'Delete this item'),
            'rowAction' => 'delete',
        ], __d('croogo', 'Are you sure?'));
    $actions = $this->Html->div('item-actions', implode(' ', $actions));

    if ($link->status == Status::PREVIEW) {
        $link->title .= ' ' . $this->Html->tag('span', __d('croogo', 'preview'), ['class' => 'label label-warning']);
    }

    $rows[] = [
        $this->Form->checkbox('Links.' . $link->id . '.id', ['class' => 'row-select']),
        $link->title,
        $this->element('Croogo/Core.admin/toggle', [
            'id' => $link->id,
            'status' => (int)$link->status,
        ]),
        $actions,
    ];
endforeach;

echo $this->Html->tableCells($rows);

$this->end();

$this->start('bulk-action');

echo $this->Form->input('Links.action', [
    'class' => 'c-select',
    'label' => __d('croogo', 'Bulk actions'),
    'options' => [
        'publish' => __d('croogo', 'Publish'),
        'unpublish' => __d('croogo', 'Unpublish'),
        'delete' => __d('croogo', 'Delete'),
        [
            'value' => 'copy',
            'text' => __d('croogo', 'Copy'),
            'hidden' => true,
        ],
    ],
    'empty' => __d('croogo', 'Bulk actions'),
]);

echo $this->Form->button(__d('croogo', 'Apply'), [
    'type' => 'submit',
    'value' => 'submit',
    'class' => 'btn-primary-outline'
]);
$this->end();

$this->append('form-end', $this->Form->end());
?>
