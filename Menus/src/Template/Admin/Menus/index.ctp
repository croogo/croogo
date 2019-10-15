<?php

use Croogo\Core\Status;

$this->Croogo->adminScript(['Croogo/Menus.admin']);

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Menus'), $this->getRequest()->getUri()->getPath());

$this->start('table-heading');
$tableHeaders = $this->Html->tableHeaders([
    $this->Paginator->sort('title', __d('croogo', 'Title')),
    $this->Paginator->sort('alias', __d('croogo', 'Alias')),
    $this->Paginator->sort('link_count', __d('croogo', 'Link Count')),
    $this->Paginator->sort('status', __d('croogo', 'Status')),
    __d('croogo', 'Actions'),
]);
echo $tableHeaders;
$this->end();

$this->start('table-body');
$rows = [];
foreach ($menus as $menu) :
    $actions = [];
    $actions[] = $this->Croogo->adminRowAction(
        '',
        ['controller' => 'Links', 'action' => 'index', '?' => ['menu_id' => $menu->id]],
        ['icon' => $this->Theme->getIcon('inspect'), 'escapeTitle' => false, 'tooltip' => __d('croogo', 'View links')]
    );
    $actions[] = $this->Croogo->adminRowActions($menu->id);
    $actions[] = $this->Croogo->adminRowAction(
        '',
        ['controller' => 'Menus', 'action' => 'edit', $menu->id],
        ['icon' => $this->Theme->getIcon('update'), 'escapeTitle' => false, 'tooltip' => __d('croogo', 'Edit this item')]
    );
    $actions[] = $this->Croogo->adminRowAction(
        '',
        ['controller' => 'Menus', 'action' => 'delete', $menu->id],
        ['icon' => $this->Theme->getIcon('delete'), 'escapeTitle' => false, 'tooltip' => __d('croogo', 'Remove this item')],
        __d('croogo', 'Are you sure?')
    );
    $actions = $this->Html->div('item-actions', implode(' ', $actions));

    $title = $this->Html->link($menu->title, [
        'controller' => 'Links',
        '?' => [
            'menu_id' => $menu->id,
        ],
    ]);

    if ($menu->status === Status::PREVIEW) {
        $title .= ' ' . $this->Html->tag('span', __d('croogo', 'preview'), ['class' => 'label label-warning']);
    }

    $status = $this->element('Croogo/Core.admin/toggle', [
        'id' => $menu->id,
        'status' => (int)$menu->status,
    ]);

    $rows[] = [
        $title,
        $menu->alias,
        $menu->link_count,
        $status,
        $this->Html->div('item-actions', $actions),
    ];
endforeach;

echo $this->Html->tableCells($rows);

$this->end();
