<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->Html->script('Croogo/Acl.acl_permissions', ['block' => true]);

$this->Croogo->adminScript('Croogo/Acl.acl_permissions');

$this->Breadcrumbs->add(__d('croogo', 'Users'),
        ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'])
    ->add(__d('croogo', 'Permissions'), $this->request->getUri()->getPath());

$this->append('action-buttons');
$toolsButton = $this->Html->link(__d('croogo', 'Tools'), '#', [
        'button' => 'secondary',
        'class' => 'dropdown-toggle',
        'data-toggle' => 'dropdown',
        'escape' => false,
    ]);

$generateUrl = [
    'plugin' => 'Croogo/Acl',
    'controller' => 'Actions',
    'action' => 'generate',
    'permissions' => 1,
];
$out = $this->Croogo->adminAction(__d('croogo', 'Generate'), $generateUrl, [
        'button' => false,
        'list' => true,
        'method' => 'post',
        'class' => 'dropdown-item',
        'tooltip' => [
            'data-title' => __d('croogo', 'Create new actions (no removal)'),
            'data-placement' => 'right',
        ],
    ]);
$out .= $this->Croogo->adminAction(__d('croogo', 'Synchronize'), $generateUrl + ['sync' => 1], [
        'button' => false,
        'list' => true,
        'method' => 'post',
        'class' => 'dropdown-item',
        'tooltip' => [
            'data-title' => __d('croogo', 'Create new & remove orphaned actions'),
            'data-placement' => 'right',
        ],
    ]);
echo $this->Html->div('btn-group', $toolsButton . $this->Html->div('dropdown-menu', $out));

echo $this->Croogo->adminAction(__d('croogo', 'Edit Actions'),
    ['controller' => 'Actions', 'action' => 'index', 'permissions' => 1]);
$this->end();

$this->set('tableClass', 'table permission-table');
$this->start('table-heading');
$roleTitles = array_values($roles->toArray());
$roleIds = array_keys($roles->toArray());

$tableHeaders = [
    __d('croogo', 'Id'),
    __d('croogo', 'Alias'),
];
$tableHeaders = array_merge($tableHeaders, $roleTitles);
$tableHeaders = $this->Html->tableHeaders($tableHeaders);
$this->end();

$this->append('table-heading');
    echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
$currentController = '';
$icon = '<i class="icon-none float-right"></i>';
foreach ($acos as $aco) {
    $id = $aco->id;
    $alias = $aco->alias;
    $class = '';
    if (substr($alias, 0, 1) == '_') {
        $level = 1;
        $class .= 'level-' . $level;
        $oddOptions = ['class' => 'hidden controller-' . $currentController];
        $evenOptions = ['class' => 'hidden controller-' . $currentController];
        $alias = substr_replace($alias, '', 0, 1);
    } else {
        $level = 0;
        $class .= ' controller';
        if ($aco->children > 0) {
            $class .= ' perm-expand';
        }
        $oddOptions = [];
        $evenOptions = [];
        $currentController = $alias;
    }

    $row = [
        $id,
        $this->Html->div(trim($class), $alias . $icon, [
            'data-id' => $id,
            'data-alias' => $alias,
            'data-level' => $level,
        ]),
    ];

    foreach ($roles as $roleId => $roleTitle) {
        $row[] = '';
    }

    echo $this->Html->tableCells($row, $oddOptions, $evenOptions);
}
echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->Js->buffer('AclPermissions.documentReady();');
