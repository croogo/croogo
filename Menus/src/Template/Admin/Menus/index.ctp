<?php

use Croogo\Core\Status;

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Menus'), $this->request->getUri()->getPath());

$this->start('table-heading');
$tableHeaders = $this->Html->tableHeaders([
    $this->Paginator->sort('title', __d('croogo', 'Title')),
    $this->Paginator->sort('alias', __d('croogo', 'Alias')),
    $this->Paginator->sort('link_count', __d('croogo', 'Link Count')),
    $this->Paginator->sort('status', __d('croogo', 'Status')),
    __d('croogo', 'Actions'),
]);
echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->start('table-body');
$rows = [];
foreach ($menus as $menu):
    $actions = [];
    $actions[] = $this->Croogo->adminRowAction('',
        ['controller' => 'Links', 'action' => 'index', '?' => ['menu_id' => $menu->id]],
        ['icon' => $this->Theme->getIcon('inspect'), 'tooltip' => __d('croogo', 'View links')]);
    $actions[] = $this->Croogo->adminRowActions($menu->id);
    $actions[] = $this->Croogo->adminRowAction('', ['controller' => 'Menus', 'action' => 'edit', $menu->id],
        ['icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item')]);
    $actions[] = $this->Croogo->adminRowAction('', ['controller' => 'Menus', 'action' => 'delete', $menu->id],
        ['icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Remove this item')],
        __d('croogo', 'Are you sure?'));
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
        'status' => $menu->status,
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
