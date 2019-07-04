<?php

$this->Croogo->adminScript('Croogo/FileManager.admin');
$this->Croogo->adminScript('Croogo/FileManager.assets');

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs
    ->add(__d('croogo', 'Attachments'), $this->request->getUri()->getPath());

if (!empty($this->request->query)) {
    $query = $this->request->query;
} else {
    $query = array();
}

$this->append('action-buttons');

echo $this->Croogo->adminAction(
    __d('croogo', 'New ' . __d('croogo', 'Attachment')),
    array_merge(['?' => $query], ['action' => 'add'])
);

$this->end();

$detailUrl = array(
    'plugin' => 'Croogo/FileManager',
    'controller' => 'Attachments',
    'action' => 'browse',
    '?' => array(
        'manage' => true,
    ),
);

$this->append('form-start', $this->Form->create(null, [
    'url' => ['action' => 'process'],
    'align' => 'inline',
]));

$this->append('table-heading');
    $tableHeaders = $this->Html->tableHeaders(array(
        $this->Form->checkbox('checkAll', ['id' => 'AttachmentsCheckAll']),
        $this->Paginator->sort('id', __d('croogo', 'Id')),
        '&nbsp;',
        $this->Paginator->sort('title', __d('croogo', 'Title')),
        __d('croogo', 'Versions'),
        __d('croogo', 'Actions'),
    ));

    echo $tableHeaders;
$this->end();

$this->append('search', $this->element('Croogo/Core.admin/search'));

$this->append('table-body');
    $rows = array();
    foreach ($attachments as $attachment) {
        $actions = array();

        $mimeType = explode('/', $attachment->asset->mime_type);
        $mimeType = $mimeType['0'];
        $assetCount = $attachment->asset_count . '&nbsp;';
        if ($mimeType == 'image') {
            $detailUrl['?']['id'] = $attachment->id;
            $actions[] = $this->Croogo->adminRowAction('', $detailUrl, array(
                'icon' => 'suitcase',
                'data-toggle' => 'browse',
                'tooltip' => __d('assets', 'View other sizes'),
            ));

            $actions[] = $this->Croogo->adminRowActions($attachment->id);
            $resizeUrl = array_merge(
                array(
                    'action' => 'resize',
                    $attachment->id,
                    '_ext' => 'json'
                ),
                array('?' => $query)
            );

            $actions[] = $this->Croogo->adminRowAction('', $resizeUrl, array(
                'icon' => $this->Theme->getIcon('resize'),
                'tooltip' => __d('croogo', 'Resize this item'),
                'data-toggle' => 'resize-asset'
            ));
        }

        $editUrl = array_merge(
            array('action' => 'edit', $attachment->id),
            array('?' => $query)
        );
        $actions[] = $this->Croogo->adminRowAction('', $editUrl, array(
            'icon' => 'update',
            'tooltip' => __d('croogo', 'Edit this item'),
        ));
        $deleteUrl = array('action' => 'delete', $attachment->id);
        $deleteUrl = array_merge(array('?' => $query), $deleteUrl);
        $actions[] = $this->Croogo->adminRowAction('', $deleteUrl, array(
            'icon' => 'delete',
            'tooltip' => __d('croogo', 'Remove this item'),
            'escapeTitle' => false,
        ), __d('croogo', 'Are you sure?'));

        $path = $attachment->asset->path;
        switch ($mimeType) {
            case 'image':
                $imgUrl = $this->AssetsImage->resize($path, 100, 200, [
                    'adapter' => $attachment->asset->adapter,
                ], [
                    'alt' => $attachment->title
                ]);
                $thumbnail = $this->Html->link($imgUrl, $path, [
                    'escape' => false,
                    'data-toggle' => 'lightbox',
                    'title' => $attachment['AssetsAttachment']['title'],
                ]);
            break;
            case 'video':
                $thumbnail = $this->Html->media($attachment->asset->path, [
                    'width' => 200,
                    'controls' => true,
                ]);
            break;
            default:
                $thumbnail = sprintf('%s %s (%s)',
                    $this->Html->image('Croogo/Core./img/icons/page_white.png', [
                        'alt' => $mimeType,
                    ]),
                    $mimeType,
                    $this->Assets->filename2ext($attachment->asset->path)
                );
            break;
        }

        $actions = $this->Html->div('item-actions', implode(' ', $actions));

        $rows[] = array(
            $this->Form->checkbox('Attachments.' . $attachment->id . '.id', ['class' => 'row-select']),
            $attachment->id,
            $thumbnail,
            [
                $this->Html->div(null, $attachment->title) .
                $this->Html->link(
                    $this->Url->build($path, true),
                    $path,
                    [
                        'target' => '_blank',
                    ]
                ),
                ['class' => 'title']
            ],
            $assetCount,
            $actions,
        );
    }

    echo $this->Html->tableCells($rows);
$this->end();

$this->start('bulk-action');
echo $this->Form->input('action', [
    'label' => __d('croogo', 'Bulk action'),
    'class' => 'c-select',
    'options' => [
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

$this->append('page-footer');
?>
<style>
    td.title {
        text-overflow: ellipsis;
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
    }
</style>
<?php
$this->end();
