<?php

$this->Croogo->adminScript('Croogo/Comments.admin');

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Content'), ['plugin' => 'Croogo/Nodes', 'controller' => 'Nodes', 'action' => 'index']);

if (isset($criteria['Comment.status'])) {
    $this->Breadcrumbs->add(__d('croogo', 'Comments'), ['action' => 'index']);
    if ($criteria['Comment.status'] == '1') {
        $this->Breadcrumbs->add(__d('croogo', 'Published'), $this->request->getRequestTarget());
        $this->assign('title', __d('croogo', 'Comments: Published'));
    } else {
        $this->Breadcrumbs->add(__d('croogo', 'Awaiting approval'), $this->request->getRequestTarget());
        $this->assign('title', __d('croogo', 'Comments: Published'));
    }
} else {
    $this->Breadcrumbs->add(__d('croogo', 'Comments'), $this->request->getRequestTarget());
}

$this->append('table-footer', $this->element('Croogo/Core.admin/modal', array(
    'id' => 'comment-modal',
    'class' => 'hide',
    )));

$this->append('action-buttons');
echo $this->Croogo->adminAction(
    __d('croogo', 'Published'),
    ['action' => 'index', '?' => ['status' => '1']],
    ['class' => 'btn btn-secondary']
);
echo $this->Croogo->adminAction(
    __d('croogo', 'Awaiting approval'),
    ['action' => 'index', '?' => ['status' => '0']],
    ['class' => 'btn btn-secondary']
);
$this->end();

$this->append('form-start', $this->Form->create(null, [
    'url' => ['action' => 'process'],
    'align' => 'inline'
]));

$this->start('table-heading');
$tableHeaders = $this->Html->tableHeaders([
    $this->Form->checkbox('checkAll'),
    $this->Paginator->sort('name', __d('croogo', 'Name')),
    $this->Paginator->sort('email', __d('croogo', 'Email')),
    $this->Paginator->sort('node_id', __d('croogo', 'Node')),
    '',
    $this->Paginator->sort('created', __d('croogo', 'Created')),
    __d('croogo', 'Actions'),
]);
echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
$rows = [];
foreach ($comments as $comment) {
    $actions = [];
    $actions[] = $this->Croogo->adminRowActions($comment->id);
    $actions[] = $this->Croogo->adminRowAction('', ['action' => 'edit', $comment->id],
        ['icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item')]);
    $actions[] = $this->Croogo->adminRowAction('', '#Comment' . $comment->id . 'Id', [
            'icon' => $this->Theme->getIcon('delete'),
            'class' => 'delete',
            'tooltip' => __d('croogo', 'Remove this item'),
            'rowAction' => 'delete',
        ], __d('croogo', 'Are you sure?'));

    $actions = $this->Html->div('item-actions', implode(' ', $actions));

    $title = empty($comment->title) ? 'Comment' : $comment->title;
    $relatedUrl = $comment->related ?
        $this->Html->link($comment->related->title, $comment->related->url->getUrl() + ['prefix' => false], ['target' => '_blank']) :
        null;
    $rows[] = [
        $this->Form->checkbox('Comments.' . $comment->id . '.id', ['class' => 'row-select']),
        $comment->name,
        $comment->email,
        $relatedUrl,
        $this->Html->link($this->Html->image('Croogo/Core./img/icons/comment.png'), '#', [
            'class' => 'comment-view',
            'data-title' => $title,
            'data-content' => $comment->body,
            'escape' => false,
        ]),
        $comment->created,
        $actions,
    ];
}

echo $this->Html->tableCells($rows);
$this->end();

$this->start('bulk-action');
echo $this->Form->input('Comments.action', [
    'label' => __d('croogo', 'Bulk actions'),
    'div' => 'input inline',
    'class' => 'c-select',
    'options' => [
        'publish' => __d('croogo', 'Publish'),
        'unpublish' => __d('croogo', 'Unpublish'),
        'delete' => __d('croogo', 'Delete'),
    ],
    'empty' => 'Bulk actions',
]);
echo $this->Form->button(__d('croogo', 'Apply'), [
    'type' => 'submit',
    'value' => 'submit',
]);
$this->end();
$this->append('form-end', $this->Form->end());
