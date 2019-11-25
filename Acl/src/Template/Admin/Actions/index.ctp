<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->Croogo->adminScript('Croogo/Acl.acl_permissions');

$this->Breadcrumbs
    ->add(__d('croogo', 'Users'), ['plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'])
    ->add(__d('croogo', 'Permissions'), [
        'plugin' => 'Croogo/Acl', 'controller' => 'Permissions',
    ])
    ->add(__d('croogo', 'Actions'), ['plugin' => 'Croogo/Acl', 'controller' => 'Actions', 'action' => 'index', 'permission' => 1]);

$this->append('action-buttons');
    $toolsButton = $this->Html->link(
        __d('croogo', 'Tools'),
        '#',
        [
            'button' => 'outline-secondary',
            'class' => 'btn-sm dropdown-toggle',
            'data-toggle' => 'dropdown',
            'escape' => false
        ]
    );

    $generateUrl = [
        'plugin' => 'Croogo/Acl',
        'controller' => 'Actions',
        'action' => 'generate',
        'permissions' => 1
    ];
    $out = $this->Croogo->adminAction(
        __d('croogo', 'Generate'),
        $generateUrl,
        [
            'button' => false,
            'list' => true,
            'method' => 'post',
            'class' => 'dropdown-item',
            'tooltip' => [
                'data-title' => __d('croogo', 'Create new actions (no removal)'),
                'data-placement' => 'left',
            ],
        ]
    );
    $out .= $this->Croogo->adminAction(
        __d('croogo', 'Synchronize'),
        $generateUrl + ['sync' => 1],
        [
            'button' => false,
            'list' => true,
            'method' => 'post',
            'class' => 'dropdown-item',
            'tooltip' => [
                'data-title' => __d('croogo', 'Create new & remove orphaned actions'),
                'data-placement' => 'left',
            ],
        ]
    );
    echo $this->Html->div(
        'btn-group',
        $toolsButton .
        $this->Html->tag('ul', $out, [
            'class' => 'dropdown-menu dropdown-menu-right',
        ])
    );
    $this->end();

    $this->set('tableClass', 'table permission-table');
    $this->start('table-heading');
    $tableHeaders = $this->Html->tableHeaders([
        __d('croogo', 'Id'),
        __d('croogo', 'Alias'),
        __d('croogo', 'Actions'),
    ]);
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

        $actions = [];
        $actions[] = $this->Croogo->adminRowAction(
            '',
            ['action' => 'move', $id, 'up'],
            [
                'icon' => $this->Theme->getIcon('move-up'),
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Move up')
            ]
        );
        $actions[] = $this->Croogo->adminRowAction(
            '',
            ['action' => 'move', $id, 'down'],
            [
                'icon' => $this->Theme->getIcon('move-down'),
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Move down')
            ]
        );

        $actions[] = $this->Croogo->adminRowAction(
            '',
            ['action' => 'edit', $id],
            [
                'icon' => $this->Theme->getIcon('update'),
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Edit this item')
            ]
        );
        $actions[] = $this->Croogo->adminRowAction(
            '',
            ['action' => 'delete', $id],
            [
                'icon' => $this->Theme->getIcon('delete'),
                'tooltip' => __d('croogo', 'Remove this item'),
                'escapeTitle' => false,
                'escape' => true,
            ],
            __d('croogo', 'Are you sure?')
        );

        $actions = $this->Html->div('item-actions', implode(' ', $actions));
        $row = [
            $id,
            $this->Html->div(trim($class), $alias . $icon, [
                'data-id' => $id,
                'data-alias' => $alias,
                'data-level' => $level,
            ]),
            $actions,
        ];

        echo $this->Html->tableCells($row, $oddOptions, $evenOptions);
    }
    echo $this->Html->tag('thead', $tableHeaders);
    $this->end();

    $this->Js->buffer('AclPermissions.documentReady();');
