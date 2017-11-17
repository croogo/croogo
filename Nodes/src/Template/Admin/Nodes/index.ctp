<?php

use Cake\Utility\Hash;
use Croogo\Core\Status;

$this->assign('title', __d('croogo', 'Contents'));

$this->extend('Croogo/Core./Common/admin_index');

$this->Croogo->adminScript('Croogo/Nodes.admin');

$this->Breadcrumbs
    ->add(__d('croogo', 'Content'), $this->request->getUri()->getPath());

$this->append('action-buttons');
echo $this->Croogo->adminAction(__d('croogo', 'Create content'), ['action' => 'create'], ['button' => 'success']);
$this->end();

$this->append('search', $this->element('admin/nodes_search'));

$this->append('form-start', $this->Form->create(null, [
    'url' => ['action' => 'process'],
    'align' => 'inline',
]));

$this->start('table-heading');
echo $this->Html->tableHeaders([
    $this->Form->checkbox('checkAll', ['id' => 'NodesCheckAll']),
    $this->Paginator->sort('title', __d('croogo', 'Title')),
    $this->Paginator->sort('type', __d('croogo', 'Type')),
    $this->Paginator->sort('user_id', __d('croogo', 'User')),
    $this->Paginator->sort('updated', __d('croogo', 'Updated')),
    $this->Paginator->sort('status', __d('croogo', 'Status')),
    '',
]);
$this->end();

$this->append('table-body');
?>
    <?php foreach ($nodes as $node): ?>
        <tr>
            <td><?= $this->Form->checkbox('Nodes.' . $node->id . '.id',
                    ['class' => 'row-select', 'id' => 'Nodes' . $node->id . 'Id']) ?></td>
            <td>
                <span>
                <?php
                echo $this->Html->link($this->Text->truncate($node->title, 40),
                    Hash::merge($node->url->getArrayCopy(), [
                        'prefix' => false,
                    ]),
                    ['target' => '_blank', 'title' => $node->title]
                );
                ?>
                </span>

                <?php if ($node->promote == 1): ?>
                    <span class="badge badge-info"><?= __d('croogo', 'promoted') ?></span>
                <?php endif ?>

                <?php if ($node->status == Status::PREVIEW): ?>
                    <span class="badge badge-warning"><?= __d('croogo', 'preview') ?></span>
                <?php endif ?>
            </td>
            <td>
                <?php
                echo $this->Html->link($node->type, [
                    'action' => 'index',
                    '?' => [
                        'type' => $node->type,
                    ],
                ]);
                ?>
            </td>
            <td>
                <?= $node->user->username ?>
            </td>
            <td>
                <?= $this->Time->i18nFormat($node->updated) ?>
            </td>
            <td>
                <?php
                echo $this->element('Croogo/Core.admin/toggle', [
                    'id' => $node->id,
                    'status' => (int)$node->status,
                ]);
                ?>
            </td>
            <td>
                <div class="item-actions">
                    <?php
                    echo $this->Croogo->adminRowActions($node->id);

                    if ($this->request->query('type')):
                        echo ' ' . $this->Croogo->adminRowAction('', ['action' => 'move', $node->id, 'up'], [
                                'method' => 'post',
                                'icon' => $this->Theme->getIcon('move-up'),
                                'tooltip' => __d('croogo', 'Move up'),
                            ]);
                        echo ' ' . $this->Croogo->adminRowAction('', ['action' => 'move', $node->id, 'down'], [
                                'method' => 'post',
                                'icon' => $this->Theme->getIcon('move-down'),
                                'tooltip' => __d('croogo', 'Move down'),
                            ]);
                    endif;

                    echo ' ' . $this->Croogo->adminRowAction('', ['action' => 'edit', $node->id], [
                            'icon' => $this->Theme->getIcon('update'),
                            'tooltip' => __d('croogo', 'Edit this item'),
                        ]);
                    echo ' ' . $this->Croogo->adminRowAction('', '#Nodes' . $node->id . 'Id', [
                            'icon' => $this->Theme->getIcon('copy'),
                            'tooltip' => __d('croogo', 'Create a copy'),
                            'rowAction' => 'copy',
                        ]);
                    echo ' ' . $this->Croogo->adminRowAction('', '#Nodes' . $node->id . 'Id', [
                            'icon' => $this->Theme->getIcon('delete'),
                            'class' => 'delete',
                            'tooltip' => __d('croogo', 'Remove this item'),
                            'rowAction' => 'delete',
                        ], __d('croogo', 'Are you sure?'));
                    ?>
                </div>
            </td>
        </tr>
    <?php endforeach ?>
<?php
$this->end();

$this->start('bulk-action');
echo $this->Form->input('action', [
    'label' => __d('croogo', 'Bulk actions'),
    'class' => 'c-select',
    'options' => [
        'publish' => __d('croogo', 'Publish'),
        'unpublish' => __d('croogo', 'Unpublish'),
        'promote' => __d('croogo', 'Promote'),
        'unpromote' => __d('croogo', 'Unpromote'),
        'delete' => __d('croogo', 'Delete'),
        [
            'value' => 'copy',
            'text' => __d('croogo', 'Copy'),
            'hidden' => true,
        ],
    ],
    'empty' => 'Bulk actions',
]);

$jsVarName = uniqid('confirmMessage_');
echo $this->Form->button(__d('croogo', 'Apply'), [
    'type' => 'button',
    'class' => 'bulk-process btn-outline-primary',
    'data-relatedElement' => '#action',
    'data-confirmMessage' => $jsVarName,
    'escape' => true,
]);

$this->Js->set($jsVarName, __d('croogo', '%s selected items?'));
$this->Js->buffer("$('.bulk-process').on('click', Nodes.confirmProcess);");

$this->end();
