<?php
/**
 * @var \Croogo\Core\View\CroogoView $this
 */

$this->assign('title', __d('croogo', 'Attachments'));
$this->extend('Croogo/Core./Common/admin_index');

$this->Html->addCrumb(__d('croogo', 'Attachments'));

$this->Croogo->adminScript('Croogo/FileManager.admin');
$this->Html->script([
    'Croogo/FileManager.lib/dropzone',
    'Croogo/FileManager.attachments/index'
], ['block' => 'scriptBottom']);

$this->start('body-footer');
echo $this->Html->tag('span', $this->Url->build(['action' => 'add', 'prefix' => 'admin']), ['id' => 'dropzone-url', 'class' => 'hidden']);
echo $this->Html->tag('span', $this->Url->build('/', true), ['id' => 'base-url', 'class' => 'hidden']);
echo $this->Html->tag('div', $this->Html->tag('p', __d('croogo', 'Drop files here to upload')), ['id' => 'dropzone-target']);
echo $this->Html->tag('script', $this->element('Croogo/FileManager.admin/dropzone_preview'), ['id' => 'dropzone-preview', 'type' => 'text/html']);
$this->Form->create(null, ['url' => ['action' => 'add', 'prefix' => 'admin']]);
$this->Form->unlockField('file');
echo $this->Html->tag('div', $this->Form->secure([]), ['id' => 'tokens']);
$this->Form->end();
$this->end();

$this->append('form-start', $this->Form->create(null, [
    'url' => ['action' => 'process'],
    'align' => 'inline',
]));

$this->start('table-heading');
$tableHeaders = $this->Html->tableHeaders([
    $this->Form->checkbox('checkAll', ['id' => 'AttachmentsCheckAll']),
    $this->Paginator->sort('id', __d('croogo', 'Id')),
    '&nbsp;',
    $this->Paginator->sort('title', __d('croogo', 'Title')),
    __d('croogo', 'URL'),
    __d('croogo', 'Actions'),
]);
echo $this->Html->tag('thead', $tableHeaders);
$this->end();

$this->append('table-body');
$rows = [];
foreach ($attachments as $attachment) {
    $actions = [];
    $actions[] = $this->Croogo->adminRowActions($attachment->id);
    $actions[] = $this->Croogo->adminRowAction('', ['controller' => 'Attachments', 'action' => 'edit', $attachment->id],
        ['icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item')]);
    $actions[] = $this->Croogo->adminRowAction('',
        ['controller' => 'attachments', 'action' => 'delete', $attachment->id],
        ['icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Remove this item')],
        __d('croogo', 'Are you sure?'));

    $mimeType = explode('/', $attachment->mime_type);
    $imageType = $mimeType['1'];
    $mimeType = $mimeType['0'];
    $imagecreatefrom = ['gif', 'jpeg', 'png', 'string', 'wbmp', 'webp', 'xbm', 'xpm'];
    if ($mimeType == 'image' && in_array($imageType, $imagecreatefrom)) {
        $imgUrl = $this->Image->resize('/uploads/' . $attachment->slug, 100, 200, true, ['alt' => $attachment->title]);
        $thumbnail = $this->Html->link($imgUrl, $attachment->path,
            ['escape' => false, 'class' => 'thickbox', 'title' => $attachment->title]);
    } else {
        $thumbnail = $this->Html->thumbnail('/croogo/core/img/icons/page_white.png', ['alt' => $attachment->mime_type]) .
            ' ' .
            $attachment->mime_type .
            ' (' .
            $this->Filemanager->filename2ext($attachment->slug) .
            ')';
    }

    $actions = $this->Html->div('item-actions', implode(' ', $actions));

    $rows[] = [
        $this->Form->checkbox('Attachments.' . $attachment->id . '.id', ['class' => 'row-select']),
        $attachment->id,
        $thumbnail,
        $this->Html->tag('div', $attachment->title, ['class' => 'ellipsis']),
        $this->Html->tag('div',
            $this->Html->link($this->Url->build($attachment->path, true), $attachment->path, ['target' => '_blank']),
            ['class' => 'ellipsis']),
        $actions,
    ];
}
echo $this->Html->tableCells($rows);
$this->end();

$this->start('bulk-action');
echo $this->Form->input('Attachments.action', [
    'label' => __d('croogo', 'Bulk actions'),
    'class' => 'c-select',
    'options' => [
        'delete' => __d('croogo', 'Delete'),
    ],
    'empty' => 'Bulk actions',
]);

$jsVarName = uniqid('confirmMessage_');
echo $this->Form->button(__d('croogo', 'Apply'), [
    'type' => 'button',
    'class' => 'bulk-process btn-primary-outline',
    'data-relatedElement' => '#attachments-action',
    'data-confirmMessage' => $jsVarName,
    'escape' => true,
]);

$this->Js->set($jsVarName, __d('croogo', '%s selected items?'));
$this->Js->buffer("$('.bulk-process').on('click', Attachments.confirmProcess);");

$this->end();

$this->append('form-end', $this->Form->end());
