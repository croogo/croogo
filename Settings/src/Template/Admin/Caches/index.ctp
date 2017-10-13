<?php

use Cake\Core\App;
$this->extend('Croogo/Core./Common/admin_index');

$clearUrl = [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Settings',
    'controller' => 'Caches',
    'action' => 'clear',
];

$this->Breadcrumbs->add(__d('croogo', 'Settings'),
    ['plugin' => 'Croogo/Settings', 'controller' => 'Settings', 'action' => 'prefix', 'Site'])
    ->add(__d('croogo', 'Caches'), $this->request->getUri()->getPath());

$this->append('action-buttons');
    echo $this->Croogo->adminAction(__d('croogo', 'Clear All'), array_merge(
            $clearUrl, ['config' => 'all']
        ), [
        'method' => 'post',
        'tooltip' => [
            'data-title' => __d('croogo', 'Clear all cache'),
            'data-placement' => 'left',
        ],
    ]);
$this->end();

$tableHeaders = $this->Html->tableHeaders([
    $this->Paginator->sort('title', __d('croogo', 'Cache')),
    __d('croogo', 'Engine'),
    __d('croogo', 'Duration'),
    __d('croogo', 'Actions')
]);
$this->append('table-heading', $tableHeaders);

$rows = [];
foreach ($caches as $cache => $engine):
    $actions = [];
    $actions[] = $this->Croogo->adminAction('',
        array_merge($clearUrl, ['config' => $cache]), [
        'button' => false,
        'class' => 'red',
        'icon' => 'delete',
        'method' => 'post',
        'tooltip' => [
            'data-title' => __d('croogo', 'Clear cache: %s', $cache),
            'data-placement' => 'left',
        ],
    ]);
    $actions = $this->Html->div('item-actions', implode(' ', $actions));

    $rows[] = [
        $cache,
        App::shortName(get_class($engine), 'Cache/Engine', 'Engine'),
        $engine->config('duration'),
        $actions,
    ];
endforeach;

$this->append('table-body', $this->Html->tableCells($rows));
