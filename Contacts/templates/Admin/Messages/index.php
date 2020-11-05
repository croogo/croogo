<?php
/**
 * @var \App\View\AppView $this
 * @var \Croogo\Contacts\Model\Entity\Message[]|\Cake\Collection\CollectionInterface $messages
 */

$this->Croogo->adminScript('Croogo/Contacts.admin');

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs->add(__d('croogo', 'Contacts'), ['controller' => 'Contacts', 'action' => 'index']);

$status = $this->getRequest()->getQuery('status');

if (isset($status)) {
    $this->Breadcrumbs->add(__d('croogo', 'Messages'), ['action' => 'index']);
    if ($status == '1') {
        $this->Breadcrumbs->add(__d('croogo', 'Read'), $this->getRequest()->getUri()->getPath());
        $this->assign('title', __d('croogo', 'Messages: Read'));
    } else {
        $this->Breadcrumbs->add(__d('croogo', 'Unread'), $this->getRequest()->getUri()->getPath());
        $this->assign('title', __d('croogo', 'Messages: Unread'));
    }
} else {
    $this->Breadcrumbs->add(__d('croogo', 'Messages'), $this->getRequest()->getUri()->getPath());
}

$this->append('table-footer', $this->element('admin/modal', [
        'id' => 'comment-modal',
    ]));

$this->append('action-buttons');
echo $this->Croogo->adminAction(__d('croogo', 'Unread'), [
    'action' => 'index',
    '?' => [
        'status' => '0',
    ],
]);
echo $this->Croogo->adminAction(__d('croogo', 'Read'), [
    'action' => 'index',
    '?' => [
        'status' => '1',
    ],
]);
$this->end();

$this->append('form-start', $this->Form->create(null, [
    'url' => ['action' => 'process'],
    'align' => 'inline',
]));

$this->start('table-heading');
$tableHeaders = $this->Html->tableHeaders([
    $this->Form->checkbox('checkAll', ['id' => 'MessagesCheckAll']),
    $this->Paginator->sort('contact_id', __d('croogo', 'Contact')),
    $this->Paginator->sort('name', __d('croogo', 'Name')),
    $this->Paginator->sort('email', __d('croogo', 'Email')),
    $this->Paginator->sort('title', __d('croogo', 'Title')),
    $this->Paginator->sort('created', __d('croogo', 'Created')),
    __d('croogo', 'Actions'),
]);
echo $tableHeaders;
$this->end();

$this->append('table-body');
$commentIcon = $this->Html->icon($this->Theme->getIcon('comment'));
$rows = [];
foreach ($messages as $message) {
    $actions = [];

    $actions[] = $this->Croogo->adminRowAction('', ['action' => 'view', $message->id], [
        'icon' => $this->Theme->geticon('read'),
        'escapeTitle' => false,
        'tooltip' => __d('croogo', 'View this item'),
    ]);
    $actions[] = $this->Croogo->adminRowAction('', ['action' => 'edit', $message->id], [
        'icon' => $this->Theme->getIcon('update'),
        'escapeTitle' => false,
        'tooltip' => __d('croogo', 'Edit this item'),
    ]);
    $actions[] = $this->Croogo->adminRowAction('', '#Message' . $message->id . 'Id', [
        'icon' => $this->Theme->getIcon('delete'),
        'escapeTitle' => false,
        'class' => 'delete',
        'tooltip' => __d('croogo', 'Remove this item'),
        'rowAction' => 'delete',
    ], __d('croogo', 'Are you sure?'));
    $actions[] = $this->Croogo->adminRowActions($message->id);

    $actions = $this->Html->div('item-actions', implode(' ', $actions));

    $rows[] = [
        $this->Form->checkbox('Messages.' . $message->id . '.id', [
            'class' => 'row-select',
            'id' => 'Messages'. $message->id . 'Id',
        ]),
        h($message->contact->title),
        h($message->name),
        h($message->email),
        $commentIcon . ' ' . $this->Html->link($message->title, '#', [
            'class' => 'comment-view',
            'data-target' => '#comment-modal',
            'data-title' => h($message->title),
            'data-content' => h($message->body),
        ]),
        $this->Time->i18nFormat($message->created),
        $actions,
    ];
}
echo $this->Html->tableCells($rows);
$this->end();

$this->start('bulk-action');
echo $this->Form->control('action', [
    'label' => __d('croogo', 'Bulk action'),
    'class' => 'c-select',
    'options' => [
        'read' => __d('croogo', 'Mark as read'),
        'unread' => __d('croogo', 'Mark as unread'),
        'delete' => __d('croogo', 'Delete'),
    ],
    'empty' => __d('croogo', 'Bulk action'),
]);
echo $this->Form->button(__d('croogo', 'Apply'), [
    'type' => 'submit',
    'value' => 'submit',
    'class' => 'bulk-process btn-outline-primary',
]);
$this->end();
