<?php

use Croogo\Core\Status;

$this->Croogo->adminScript('Croogo/Blocks.admin');

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Blocks'));

$this->append('form-start', $this->Form->create(null, [
    'url' => ['action' => 'process'],
    'align' => 'inline'
]));

$chooser = isset($this->request->query['chooser']);
$this->start('table-heading');
$tableHeaders = $this->Html->tableHeaders([
    $this->Form->checkbox('checkAll', ['id' => 'BlocksCheckAll']),
    $this->Paginator->sort('title', __d('croogo', 'Title')),
    $this->Paginator->sort('alias', __d('croogo', 'Alias')),
    $this->Paginator->sort('region_id', __d('croogo', 'Region')),
    $this->Paginator->sort('status', __d('croogo', 'Status')),
    __d('croogo', 'Actions'),
]);
echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
$rows = [];
foreach ($blocks as $block) {
    $actions = [];
    $actions[] = $this->Croogo->adminRowAction('', ['action' => 'moveUp', $block->id], [
            'icon' => $this->Theme->getIcon('move-up'),
            'tooltip' => __d('croogo', 'Move up'),
            'method' => 'post',
        ]);
    $actions[] = $this->Croogo->adminRowAction('', ['action' => 'moveDown', $block->id], [
            'icon' => $this->Theme->getIcon('move-down'),
            'tooltip' => __d('croogo', 'Move down'),
            'method' => 'post',
        ]);
    $actions[] = $this->Croogo->adminRowActions($block->id);
    $actions[] = $this->Croogo->adminRowAction('', ['action' => 'edit', $block->id],
        ['icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item')]);
    $actions[] = $this->Croogo->adminRowAction('', '#Blocks' . $block->id . 'Id', [
            'icon' => $this->Theme->getIcon('copy'),
            'tooltip' => __d('croogo', 'Create a copy'),
            'rowAction' => 'copy',
        ], __d('croogo', 'Create a copy of this Block?'));
    $actions[] = $this->Croogo->adminRowAction('', '#Blocks' . $block->id . 'Id', [
            'icon' => $this->Theme->getIcon('delete'),
            'class' => 'delete',
            'tooltip' => __d('croogo', 'Remove this item'),
            'rowAction' => 'delete',
        ], __d('croogo', 'Are you sure?'));

    if ($chooser) {
        $checkbox = null;
        $actions = [
            $this->Croogo->adminRowAction(__d('croogo', 'Choose'), '#', [
                'class' => 'item-choose',
                'data-chooser_type' => 'Block',
                'data-chooser_id' => $block->id,
                'data-chooser_title' => $block->title,
            ]),
        ];
    } else {
        $checkbox = $this->Form->checkbox('Blocks.' . $block->id . '.id', ['class' => 'row-select']);
    }

    $actions = $this->Html->div('item-actions', implode(' ', $actions));
    $title = $this->Html->link($block->title, [
        'action' => 'edit',
        $block->id,
    ]);

    if ($block->status == Status::PREVIEW) {
        $title .= ' ' . $this->Html->tag('span', __d('croogo', 'preview'), ['class' => 'label label-warning']);
    }

    $rows[] = [
        $checkbox,
        $title,
        $block->alias,
        $block->region->title,
        $this->element('Croogo/Core.admin/toggle', [
            'id' => $block->id,
            'status' => (int)$block->status,
        ]),
        $actions,
    ];
}

echo $this->Html->tableCells($rows);
?>
    </table>
<?php
$this->end();
if (!$chooser):
    $this->start('bulk-action');
    echo $this->Form->input('Blocks.action', [
        'label' => __d('croogo', 'Bulk action'),
        'class' => 'c-select',
        'options' => [
            'publish' => __d('croogo', 'Publish'),
            'unpublish' => __d('croogo', 'Unpublish'),
            'delete' => __d('croogo', 'Delete'),
            'copy' => __d('croogo', 'Copy'),
        ],
        'empty' => __d('croogo', 'Bulk action'),
    ]);
    echo $this->Form->button(__d('croogo', 'Submit'), [
        'type' => 'submit',
        'value' => 'submit',
        'class' => 'btn-primary-ouline'
    ]);
    $this->end();
endif;
$this->append('form-end', $this->Form->end());
