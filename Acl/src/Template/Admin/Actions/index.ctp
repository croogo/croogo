<?php

$this->extend('Croogo/Core./Common/admin_index');

$this->Html->script('Croogo/Acl.acl_permissions', ['block' => true]);

$this->Croogo->adminScript('Croogo/Acl.acl_permissions');

$this->Breadcrumbs
    ->add(__d('croogo', 'Users'), array('plugin' => 'Croogo/Users', 'controller' => 'Users', 'action' => 'index'))
    ->add(__d('croogo', 'Permissions'), array(
        'plugin' => 'Croogo/Acl', 'controller' => 'Permissions',
    ))
    ->add(__d('croogo', 'Actions'), array('plugin' => 'Croogo/Acl', 'controller' => 'Actions', 'action' => 'index', 'permission' => 1));

$this->append('action-buttons');
    $toolsButton = $this->Html->link(
        __d('croogo', 'Tools'),
        '#',
        array(
            'button' => 'secondary',
            'class' => 'dropdown-toggle',
            'data-toggle' => 'dropdown',
            'escape' => false
        )
    );

    $generateUrl = array(
        'plugin' => 'Croogo/Acl',
        'controller' => 'Actions',
        'action' => 'generate',
        'permissions' => 1
    );
    $out = $this->Croogo->adminAction(__d('croogo', 'Generate'),
        $generateUrl,
        array(
            'button' => false,
            'list' => true,
            'method' => 'post',
            'class' => 'dropdown-item',
            'tooltip' => array(
                'data-title' => __d('croogo', 'Create new actions (no removal)'),
                'data-placement' => 'left',
            ),
        )
    );
    $out .= $this->Croogo->adminAction(__d('croogo', 'Synchronize'),
        $generateUrl + array('sync' => 1),
        array(
            'button' => false,
            'list' => true,
            'method' => 'post',
            'class' => 'dropdown-item',
            'tooltip' => array(
                'data-title' => __d('croogo', 'Create new & remove orphaned actions'),
                'data-placement' => 'left',
            ),
        )
    );
    echo $this->Html->div('btn-group',
        $toolsButton .
        $this->Html->tag('ul', $out, [
            'class' => 'dropdown-menu dropdown-menu-right',
        ])
    );
$this->end();

$this->set('tableClass', 'table permission-table');
$this->start('table-heading');
    $tableHeaders = $this->Html->tableHeaders(array(
        __d('croogo', 'Id'),
        __d('croogo', 'Alias'),
        __d('croogo', 'Actions'),
    ));
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
            $oddOptions = array('class' => 'hidden controller-' . $currentController);
            $evenOptions = array('class' => 'hidden controller-' . $currentController);
            $alias = substr_replace($alias, '', 0, 1);
        } else {
            $level = 0;
            $class .= ' controller';
            if ($aco->children > 0) {
                $class .= ' perm-expand';
            }
            $oddOptions = array();
            $evenOptions = array();
            $currentController = $alias;
        }

        $actions = array();
        $actions[] = $this->Croogo->adminRowAction('',
            array('action' => 'move', $id, 'up'),
            array('icon' => $this->Theme->getIcon('move-up'), 'tooltip' => __d('croogo', 'Move up'))
        );
        $actions[] = $this->Croogo->adminRowAction('',
            array('action' => 'move', $id, 'down'),
            array('icon' => $this->Theme->getIcon('move-down'), 'tooltip' => __d('croogo', 'Move down'))
        );

        $actions[] = $this->Croogo->adminRowAction('',
            array('action' => 'edit', $id),
            array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
        );
        $actions[] = $this->Croogo->adminRowAction('',
            array('action' => 'delete', $id),
            array(
                'icon' => $this->Theme->getIcon('delete'),
                'tooltip' => __d('croogo', 'Remove this item'),
                'escapeTitle' => false,
                'escape' => true,
            ),
            __d('croogo', 'Are you sure?')
        );

        $actions = $this->Html->div('item-actions', implode(' ', $actions));
        $row = array(
            $id,
            $this->Html->div(trim($class), $alias . $icon, array(
                'data-id' => $id,
                'data-alias' => $alias,
                'data-level' => $level,
            )),
            $actions,
        );

        echo $this->Html->tableCells($row, $oddOptions, $evenOptions);
    }
    echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->Js->buffer('AclPermissions.documentReady();');
