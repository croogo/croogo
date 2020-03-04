<?php

$this->Croogo->adminScript('Croogo/FileManager.admin');
$this->Croogo->adminScript('Croogo/FileManager.assets');

$this->extend('Croogo/Core./Common/admin_index');

$this->Breadcrumbs
    ->add(__d('croogo', 'Attachments'), $this->getRequest()->getUri()->getPath());

$query = (array)$this->getRequest()->getQuery();

$this->append('action-buttons');

echo $this->Croogo->adminAction(
    __d('croogo', 'New ' . __d('croogo', 'Attachment')),
    array_merge(['?' => $query], ['action' => 'add'])
);

$this->end();

$detailUrl = [
    'plugin' => 'Croogo/FileManager',
    'controller' => 'Attachments',
    'action' => 'browse',
    '?' => [
        'manage' => true,
    ],
];

$this->append('form-start', $this->Form->create(null, [
    'url' => ['action' => 'process'],
    'align' => 'inline',
]));

$this->append('table-heading');
    $tableHeaders = $this->Html->tableHeaders([
        $this->Form->checkbox('checkAll', ['id' => 'AttachmentsCheckAll']),
        $this->Paginator->sort('id', __d('croogo', 'Id')),
        '&nbsp;',
        $this->Paginator->sort('title', __d('croogo', 'Title')),
        __d('croogo', 'Versions'),
        __d('croogo', 'Actions'),
    ]);

    echo $tableHeaders;
    $this->end();

    $this->append('search', $this->element('Croogo/Core.admin/search'));

    $this->append('table-body');
    $rows = [];
    foreach ($attachments as $attachment) {
        $actions = [];

        $mimeType = explode('/', $attachment->asset->mime_type);
        $mimeType = $mimeType['0'];
        $assetCount = $attachment->asset_count . '&nbsp;';
        if ($mimeType == 'image' || $mimeType == 'video') {
            $detailUrl['?']['id'] = $attachment->id;
            $actions[] = $this->Croogo->adminRowAction('', $detailUrl, [
                'icon' => 'suitcase',
                'escapeTitle' => false,
                'data-toggle' => 'browse',
                'tooltip' => __d('croogo', 'View other sizes'),
            ]);

            $actions[] = $this->Croogo->adminRowActions($attachment->id);

            if ($mimeType == 'image'):
                $resizeUrl = array_merge(
                    [
                        'action' => 'resize',
                        $attachment->id,
                        '_ext' => 'json'
                    ],
                    ['?' => $query]
                );

                $actions[] = $this->Croogo->adminRowAction('', $resizeUrl, [
                    'icon' => $this->Theme->getIcon('resize'),
                    'escapeTitle' => false,
                    'tooltip' => __d('croogo', 'Resize this item'),
                    'data-toggle' => 'resize-asset'
                ]);
            endif;
        }

        $editUrl = array_merge(
            ['action' => 'edit', $attachment->id],
            ['?' => $query]
        );
        $actions[] = $this->Croogo->adminRowAction('', $editUrl, [
            'icon' => 'update',
            'escapeTitle' => false,
            'tooltip' => __d('croogo', 'Edit this item'),
        ]);
        $deleteUrl = ['action' => 'delete', $attachment->id];
        $deleteUrl = array_merge(['?' => $query], $deleteUrl);
        $actions[] = $this->Croogo->adminRowAction('', $deleteUrl, [
            'icon' => 'delete',
            'escapeTitle' => false,
            'tooltip' => __d('croogo', 'Remove this item'),
            'escapeTitle' => false,
        ], __d('croogo', 'Are you sure?'));

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
                $thumbnail = $this->Html->media([$attachment->asset->path], [
                    'width' => 200,
                    'controls', 'playsinline',
                    'poster' => $attachment->asset->poster_path ?: null,
                ]);
                break;
            default:
                $thumbnail = sprintf(
                    '%s %s (%s)',
                    $this->Html->image('Croogo/Core./img/icons/page_white.png', [
                        'alt' => $mimeType,
                    ]),
                    $mimeType,
                    $this->FileManager->filename2ext($attachment->asset->path)
                );
                break;
        }

        $actions = $this->Html->div('item-actions', implode(' ', $actions));

        $rows[] = [
            $this->Form->checkbox('Attachments.' . $attachment->id . '.id', ['class' => 'row-select']),
            $attachment->id,
            $thumbnail,
            [
                $this->Html->div(null, h($attachment->title)) .
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
        ];
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

if (!$this->getRequest()->is('ajax')) :
    $script = <<< EOF
        Assets.init();
        Attachments.init();
EOF;
    $this->Js->buffer($script);
endif;
