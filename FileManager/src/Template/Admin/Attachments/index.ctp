<?php
$this->assign('title', __d('croogo', 'Attachments'));
$this->extend('Croogo/Core./Common/admin_index');

$this->Html->addCrumb(__d('croogo', 'Attachments'));

$this->Html->script([
    'Croogo/FileManager.lib/dropzone',
    'Croogo/FileManager.attachments/index'
], ['block' => 'scriptBottom']);

$this->start('body-footer');
echo $this->Html->tag('span', $this->Url->build(['action' => 'add']), ['id' => 'dropzone-url', 'class' => 'hidden']);
echo $this->Html->tag('div', $this->Html->tag('p', __d('croogo', 'Drop files here to upload')), ['id' => 'dropzone-target']);
//echo $this->Form->create(null, [
//    'type' => 'file',
//    'class' => 'dropzone',
//    'id' => 'upload-dropzone',
//    'url' => [
//        'action' => 'add'
//    ]
//]);
//echo $this->Html->div('dz-message', '');
//echo $this->Form->end();
$this->end();

$this->start('table-heading');
$tableHeaders = $this->Html->tableHeaders([
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
