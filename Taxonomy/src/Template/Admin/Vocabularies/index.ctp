<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Content'),
        ['plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index'])
    ->add(__d('croogo', 'Vocabularies'), $this->request->getRequestTarget());

$this->start('table-heading');
$tableHeaders = $this->Html->tableHeaders([
    $this->Paginator->sort('title', __d('croogo', 'Title')),
    $this->Paginator->sort('alias', __d('croogo', 'Alias')),
    $this->Paginator->sort('plugin', __d('croogo', 'Plugin')),
    __d('croogo', 'Actions'),
]);

echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
$rows = [];
foreach ($vocabularies as $vocabulary) :
    $actions = [];
    $actions[] = $this->Croogo->adminRowAction('', ['controller' => 'Terms', 'action' => 'index', '?' => ['vocabulary_id' => $vocabulary->id]],
        ['icon' => $this->Theme->getIcon('view'), 'tooltip' => __d('croogo', 'View terms')]);
    $actions[] = $this->Croogo->adminRowAction('', ['action' => 'moveUp', $vocabulary->id],
        ['icon' => $this->Theme->getIcon('move-up'), 'tooltip' => __d('croogo', 'Move up'), 'method' => 'post']);
    $actions[] = $this->Croogo->adminRowAction('', ['action' => 'moveDown', $vocabulary->id],
        ['icon' => $this->Theme->getIcon('move-down'), 'tooltip' => __d('croogo', 'Move down'), 'method' => 'post']);
    $actions[] = $this->Croogo->adminRowActions($vocabulary->id);
    $actions[] = $this->Croogo->adminRowAction('', ['action' => 'edit', $vocabulary->id],
        ['icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item')]);
    $actions[] = $this->Croogo->adminRowAction('', ['action' => 'delete', $vocabulary->id],
        ['icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Remove this item')],
        __d('croogo', 'Are you sure?'));
    $actions = $this->Html->div('item-actions', implode(' ', $actions));
    $rows[] = [
        $this->Html->link($vocabulary->title, ['controller' => 'Terms', 'action' => 'index', '?' => ['vocabulary_id' => $vocabulary->id]]),
        $vocabulary->alias,
        $vocabulary->plugin,
        $actions,
    ];
endforeach;

echo $this->Html->tableCells($rows);

$this->end();
