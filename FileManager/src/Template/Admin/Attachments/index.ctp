<?php
$this->assign('title', __d('croogo', 'Attachments'));
$this->extend('Croogo/Core./Common/admin_index');

$this->Html->addCrumb(__d('croogo', 'Attachments'));

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
        $thumbnail = $this->Html->thumbnail('/croogo/img/icons/page_white.png', ['alt' => $attachment->mime_type]) .
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
