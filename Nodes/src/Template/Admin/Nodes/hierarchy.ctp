<?php

use Croogo\Core\Status;

$this->assign('title', __d('croogo', 'Content Tree'));

$this->extend('Croogo/Core./Common/admin_index');
$this->Croogo->adminScript('Croogo/Nodes.admin');

$indexUrl = [
    'prefix' => 'admin',
    'plugin' => 'Croogo/Nodes',
    'controller' => 'Nodes',
    'action' => 'index',
];
$this->Breadcrumbs
    ->add(__d('croogo', 'Content'), $indexUrl);

if (isset($type) && $this->request->getQuery('type')):
    $typeUrl = array_merge($indexUrl, [
        'type' => $type->alias,
    ]);
    $this->Breadcrumbs->add($type->title, $typeUrl);

endif;

$this->append('action-buttons');
    echo $this->Croogo->adminAction(
        __d('croogo', 'New %s', $type->title), [
            'action' => 'add',
            $type->alias,
        ]
    );
$this->end();

$this->append('search', $this->element('admin/nodes_search'));

$this->append('form-start', $this->Form->create(
    'Node',
    array(
        'url' => array('controller' => 'nodes', 'action' => 'process'),
        'class' => 'form-inline'
    )
));

$this->start('table-heading');
    $tableHeaders = $this->Html->tableHeaders(array(
        $this->Form->checkbox('checkAll'),
        __d('croogo', 'Id'),
        __d('croogo', 'Title'),
        __d('croogo', 'Type'),
        __d('croogo', 'User'),
        __d('croogo', 'Status'),
        ''
    ));
    echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
?>
<?php foreach ($nodes as $node): ?>
    <tr>
        <td><?php echo $this->Form->checkbox('Node.' . $node['Node']['id'] . '.id', array('class' => 'row-select')); ?></td>
        <td><?php echo $node->id; ?></td>
        <td class="level-<?php echo $node->depth; ?>">
            <span>
            <?php
                echo $this->Html->link($node->title, array(
                    'admin' => false,
                    'controller' => 'nodes',
                    'action' => 'view',
                    'type' => $node->type,
                    'slug' => $node->slug,
                ));
            ?>
            </span>

            <?php if ($node->promote == 1): ?>
            <span class="badge badge-info"><?php echo __d('croogo', 'promoted'); ?></span>
            <?php endif ?>

            <?php if ($node->status == Status::PREVIEW): ?>
            <span class="badge badge-warning"><?php echo __d('croogo', 'preview'); ?></span>
            <?php endif ?>
        </td>
        <td>
            <?php echo $node->type; ?>
        </td>
        <td>
            <?php echo $node->username; ?>
        </td>
        <td>
            <?php
                echo $this->element('admin/toggle', array(
                    'id' => $node->id,
                    'status' => (int)$node->status,
                ));
            ?>
        </td>
        <td>
            <div class="item-actions">
            <?php
                echo $this->Croogo->adminRowActions($node->id);

                echo $this->Croogo->adminRowAction('',
                    array('controller' => 'nodes', 'action' => 'move', $node->id, 'up'),
                    [
                        'icon' => $this->Theme->getIcon('move-up'),
                        'escapeTitle' => false,
                        'tooltip' => __d('croogo', 'Move up'),
                    ]
                );
                echo $this->Croogo->adminRowAction('',
                    array('controller' => 'nodes', 'action' => 'move', $node->id, 'down'),
                    [
                        'icon' => $this->Theme->getIcon('move-down'),
                        'escapeTitle' => false,
                        'tooltip' => __d('croogo', 'Move down'),
                    ]
                );
                echo ' ' . $this->Croogo->adminRowAction('',
                    array('action' => 'edit', $node->id),
                    [
                        'icon' => $this->Theme->getIcon('update'),
                        'escapeTitle' => false,
                        'tooltip' => __d('croogo', 'Edit this item')
                    ]
                );
                echo ' ' . $this->Croogo->adminRowAction('',
                    '#Node' . $node->id . 'Id',
                    [
                        'icon' => $this->Theme->getIcon('delete'),
                        'escapeTitle' => false,
                        'class' => 'delete',
                        'tooltip' => __d('croogo', 'Remove this item'),
                        'rowAction' => 'delete',
                    ],
                    __d('croogo', 'Are you sure?')
                );
            ?>
            </div>
        </td>
    </tr>
<?php endforeach ?>
<?php
$this->end();

$this->start('bulk-action');
    echo $this->Form->input('action', [
        'label' => false,
        'class' => 'c-select',
        'options' => [
            'publish' => __d('croogo', 'Publish'),
            'unpublish' => __d('croogo', 'Unpublish'),
            'promote' => __d('croogo', 'Promote'),
            'unpromote' => __d('croogo', 'Unpromote'),
            'delete' => __d('croogo', 'Delete'),
            'copy' => [
                'value' => 'copy',
                'name' => __d('croogo', 'Copy'),
                'hidden' => true,
            ],
        ],
        'empty' => 'Bulk actions',
    ]);

    $jsVarName = uniqid('confirmMessage_');
    $button = $this->Form->button(__d('croogo', 'Apply'), array(
        'type' => 'button',
        'class' => 'bulk-process btn-outline-primary',
        'data-relatedElement' => '#action',
        'data-confirmMessage' => $jsVarName,
    ));
    echo $button;
    $this->Js->set($jsVarName, __d('croogo', '%s selected items?'));
    $this->Js->buffer("$('.bulk-process').on('click', Nodes.confirmProcess);");

$this->end();

$this->append('paging', ' ');
