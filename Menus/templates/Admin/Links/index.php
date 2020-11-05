<?php
/**
 * @var \App\View\AppView $this
 * @var array $linksStatus
 * @var mixed $linksTree
 * @var \Croogo\Menus\Model\Entity\Menu $menu
 */

use Croogo\Core\Status;

$this->Croogo->adminscript('Croogo/Menus.admin');

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Menus'), ['controller' => 'Menus', 'action' => 'index'])
    ->add(h(__d('croogo', $menu->title)), $this->getRequest()->getRequestTarget());

$this->append('action-buttons');
echo $this->Croogo->adminAction(__d('croogo', 'New link'), [
    'action' => 'add',
    '?' => [
        'menu_id' => $menu->id,
    ],
]);
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
echo $tableHeaders;
$this->end();

$this->append('table-body');
$rows = [];
foreach ($linksTree as $linkId => $linkTitle) :
    $actions = [];
    $actions[] = $this->Croogo->adminRowAction('', [
        'action' => 'moveUp',
        $linkId,
    ], [
        'icon' => $this->Theme->getIcon('move-up'),
        'escapeTitle' => false,
        'tooltip' => __d('croogo', 'Move up'),
    ]);
    $actions[] = $this->Croogo->adminRowAction('', [
        'action' => 'moveDown',
        $linkId,
    ], [
        'icon' => $this->Theme->getIcon('move-down'),
        'escapeTitle' => false,
        'tooltip' => __d('croogo', 'Move down'),
    ]);
    $actions[] = $this->Croogo->adminRowActions($linkId);
    $actions[] = $this->Croogo->adminRowAction('', [
        'action' => 'edit',
        $linkId,
    ], [
        'icon' => $this->Theme->getIcon('update'),
        'escapeTitle' => false,
        'tooltip' => __d('croogo', 'Edit this item'),
    ]);
    $actions[] = $this->Croogo->adminRowAction('', '#Link' . $linkId . 'Id', [
        'icon' => $this->Theme->getIcon('copy'),
        'escapeTitle' => false,
        'tooltip' => __d('croogo', 'Create a copy'),
        'rowAction' => 'copy',
    ], __d('croogo', 'Create a copy of this Link?'));
    $actions[] = $this->Croogo->adminRowAction('', '#Link' . $linkId . 'Id', [
        'icon' => $this->Theme->getIcon('delete'),
        'escapeTitle' => false,
        'class' => 'delete',
        'tooltip' => __d('croogo', 'Delete this item'),
        'rowAction' => 'delete',
    ], __d('croogo', 'Are you sure?'));
    $actions = $this->Html->div('item-actions', implode(' ', $actions));
    if ($linksStatus[$linkId] == Status::PREVIEW) {
        $linkTitle .= ' ' . $this->Html->tag('span', __d('croogo', 'preview'), ['class' => 'label label-warning']);
    }
    $rows[] = [
        $this->Form->checkbox('Links.' . $linkId . '.id', ['class' => 'row-select', 'id' => 'Link' . $linkId . 'Id']),
        $linkTitle,
        $this->element('Croogo/Core.admin/toggle', [
            'id' => $linkId,
            'status' => (int)$linksStatus[$linkId],
        ]),
        $actions,
    ];
endforeach;

echo $this->Html->tableCells($rows);

$this->end();

$this->start('bulk-action');

echo $this->Form->control('action', [
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
    'class' => 'btn-outline-primary'
]);
$this->end();
