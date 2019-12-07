<?php

use Cake\Utility\Hash;

$this->extend('Croogo/Core./Common/admin_index');

$this->append('page-heading');
?>
<style>
.popover-content { word-wrap: break-word; }
a i[class^=icon]:hover { text-decoration: none; }
</style>
<?php
$this->end();

$this->Breadcrumbs->add(__d('croogo', 'Attachments'));

if (!$this->getRequest()->is('ajax')) :
    $this->Croogo->adminScript([
        'Croogo/FileManager.admin',
        'Croogo/FileManager.assets',
    ]);
endif;

$assetId = $filter = $filename = $type = $all = null;
if (empty($model) && !empty($this->getRequest()->getQuery('model'))) :
    $model = $this->getRequest()->getQuery('model');
endif;
if (empty($foreignKey) && !empty($this->getRequest()->getQuery('foreign_key'))) :
    $foreignKey = $this->getRequest()->getQuery('foreign_key');
endif;
if (!empty($this->getRequest()->getQuery('asset_id'))) :
    $assetId = $this->getRequest()->getQuery('asset_id');
endif;
if (!empty($this->getRequest()->getQuery('type'))) :
    $type = $this->getRequest()->getQuery('type');
endif;
if (!empty($this->getRequest()->getQuery('filter'))) :
    $filter = $this->getRequest()->getQuery('filter');
endif;
if (!empty($this->getRequest()->getQuery('filename'))) :
    $filename = $this->getRequest()->getQuery('filename');
endif;
if (!empty($this->getRequest()->getQuery('all'))) :
    $all = $this->getRequest()->getQuery('all');
endif;
if (!empty($this->getRequest()->getQuery('editor'))) :
    $editor = $this->getRequest()->getQuery('editor');
endif;
if (empty($editor) && !empty($this->getRequest()->getQuery('CKEditor'))) :
    $editor = $this->getRequest()->getQuery('CKEditor');
endif;
if (!empty($this->getRequest()->getQuery('manage'))) :
    $manage = $this->getRequest()->getQuery('manage');
endif;

$this->append('action-buttons');
    echo $this->Croogo->adminAction(
        __d('croogo', 'New Attachment'),
        array_merge(
            ['controller' => 'Attachments', 'action' => 'add', 'editor' => 1],
            ['?' => (array)$this->getRequest()->getQuery()]
        )
    ) . ' ';

    $listUrl = [
        'controller' => 'Attachments',
        'action' => 'browse',
    ];
    if (isset($model) && isset($foreignKey)) :
        $listUrl['?'] = [
            'model' => $model,
            'foreign_key' => $foreignKey,
        ];
    endif;

    if (!$all) :
        $listUrl['?']['all'] = true;
        $listTitle = __d('croogo', 'List All Attachments');
    else :
        $listTitle = __d('croogo', 'List Attachments');
    endif;
    echo $this->Croogo->adminAction($listTitle, $listUrl, [
        'button' => 'outline-success',
    ]);
    $this->end();

    $this->append('table-heading');
    $tableHeaders = $this->Html->tableHeaders([
        $this->Paginator->sort('AssetsAsset.id', __d('croogo', 'Id')),
        '&nbsp;',
        $this->Paginator->sort('title', __d('croogo', 'Title')) . ' ' .
        $this->Paginator->sort('filename', __d('croogo', 'Filename')) . ' ' .
        $this->Paginator->sort('width', __d('croogo', 'Width')) . ' ' .
        $this->Paginator->sort('height', __d('croogo', 'Height')) . ' ' .
        $this->Paginator->sort('filesize', __d('croogo', 'Size')),
        __d('croogo', 'Actions'),
    ]);
    echo $tableHeaders;
    $this->end();

    $this->append('table-body');
    $query = ['?' => (array)$this->getRequest()->getQuery()];
    $rows = [];
    foreach ($attachments as $attachment) :
        $actions = [];
        $mimeType = explode('/', $attachment->asset->mime_type);
        $mimeType = $mimeType['0'];

        if (isset($editor)) :
            $actions[] = $this->Croogo->adminRowAction('', 'javascript:void(0)', [
                'onclick' => "Croogo.Wysiwyg.choose('" . $attachment->asset->path . "');",
                'icon' => 'attach',
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Insert')
            ]);
        endif;

        if (!isset($all) && !isset($assetId)) {
            $deleteUrl = Hash::merge($query, [
                'controller' => 'Attachments',
                'action' => 'delete',
                $attachment->id,
                'editor' => 1,
            ]);
            $actions[] = $this->Croogo->adminRowAction(
                '',
                $deleteUrl,
                [
                'icon' => $this->Theme->getIcon('delete'),
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Delete Attachment')
                ],
                __d('croogo', 'Are you sure?')
            );
        } elseif (isset($manage) && isset($assetId)) {
            $deleteAssetUrl = Hash::merge($query, [
                'controller' => 'Assets',
                'action' => 'delete',
                $attachment->asset->id,
            ]);
            $actions[] = $this->Croogo->adminRowAction(
                '',
                $deleteAssetUrl,
                [
                'icon' => $this->Theme->getIcon('delete'),
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Delete Attachment version')
                ],
                __d('croogo', 'Are you sure?')
            );
        }

        if ($mimeType === 'image' &&
            $attachment->hash == $attachment->asset->hash
        ) {
            $resizeUrl = Hash::merge($query, [
                'action' => 'resize',
                $attachment->id,
                'ext' => 'json'
            ]);
            $actions[] = $this->Croogo->adminRowAction('', $resizeUrl, [
                'icon' => $this->Theme->getIcon('resize'),
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Resize this item'),
                'data-toggle' => 'resize-asset'
            ]);
        }

        if (isset($assetId) || isset($all)) :
            unset($query['?']['asset_id']);

            if (isset($model) && isset($foreignKey)) :
                $extractPath = "asset.asset_usage.{n}[model=$model][foreign_key=$foreignKey]";
                $usage = Hash::extract($attachment, $extractPath);
                if (empty($usage) && $model !== 'Attachments') :
                    $addUrl = Hash::merge([
                        'controller' => 'AssetUsages',
                        'action' => 'add',
                        '?' => [
                            'asset_id' => $attachment->asset->id,
                            'model' => $model,
                            'foreign_key' => $foreignKey,
                        ]
                    ], $query);
                    $actions[] = $this->Croogo->adminRowAction('', $addUrl, [
                        'icon' => 'create',
                        'escapeTitle' => false,
                        'tooltip' => __d('croogo', 'Register asset with this resource'),
                        'method' => 'post',
                    ]);
                endif;
            endif;
        elseif ($mimeType === 'image') :
            if (!$this->getRequest()->getQuery('manage')) :
                $detailUrl = Hash::merge([
                    'action' => 'browse',
                    '?' => [
                        'asset_id' => $attachment->asset->id,
                    ]
                ], $query);
                $actions[] = $this->Croogo->adminRowAction('', $detailUrl, [
                    'icon' => 'suitcase',
                    'escapeTitle' => false,
                    'tooltip' => __d('croogo', 'View other sizes'),
                ]);
            endif;
        endif;

        if ($mimeType == 'image') {
            try {
                $img = $this->AssetsImage->resize(
                    $attachment->asset->path,
                    100,
                    200,
                    ['adapter' => $attachment->asset->adapter]
                );
            } catch (Exception $e) {
                $img = $this->Html->image($attachment->asset->path, ['style' => 'max-width: 200px']);
            }
            $thumbnail = $this->Html->link(
                $img,
                $attachment->asset->path,
                [
                    'data-toggle' => 'lightbox',
                    'escape' => false,
                    'title' => $attachment->title,
                ]
            );
            if (!empty($attachment['AssetsAssetUsage']['type']) &&
                $attachment['AssetsAssetUsage']['foreign_key'] === $foreignKey &&
                $attachment['AssetsAssetUsage']['model'] === $model
            ) :
                $thumbnail .= $this->Html->div(
                    null,
                    $this->Html->link(
                        $this->Html->tag(
                            'span',
                            $attachment['AssetsAssetUsage']['type'],
                            ['class' => 'badge badge-info']
                        ),
                        [
                            'action' => 'browse',
                            '?' => [
                                'type' => $attachment['AssetsAssetUsage']['type']
                            ] + $this->getRequest()->query,
                        ],
                        [
                            'escape' => false,
                        ]
                    )
                );
            endif;
        } elseif ($mimeType == 'video') {
            $thumbnail = $this->Html->media($attachment->asset->path, [
                'width' => 200,
                'controls' => true,
            ]);
        } else {
            $thumbnail = $this->Html->image('Croogo/Core./img/icons/page_white.png') . ' ' . $attachment->asset->mime_type . ' (' . $this->FileManager->filename2ext($attachment->slug) . ')';
            $thumbnail = $this->Html->link($thumbnail, '#', [
                'escape' => false,
            ]);
        }

        $actions = $this->Html->div('item-actions', implode(' ', $actions));

        $url = $this->Html->link(
            $this->Url->build($attachment->asset->path),
            $attachment->asset->path,
            [
                'onclick' => "Croogo.Wysiwyg.choose('" . $attachment->asset->path . "'); return false;",
                'target' => '_blank',
            ]
        );
        $urlPopover = $this->Croogo->adminRowAction('', '#', [
            'class' => 'popovers',
            'icon' => 'info-circle',
            'escapeTitle' => false,
            'iconSize' => 'small',
            'data-title' => __d('croogo', 'URL'),
            'data-html' => 'true',
            'data-placement' => 'top',
            'data-trigger' => 'click|focus',
            'data-content' => $url,
        ]);

        $title = $this->Html->para(null, h($attachment->title));
        $title .= $this->Html->para(
            'small',
            $this->Text->truncate(
                $attachment->asset->filename,
                30
            ) . '&nbsp;' . $urlPopover,
            ['title' => $attachment->asset->filename]
        );

        $title .= $this->Html->para('small', 'Dimension: ' .
            $attachment->asset->width . ' x ' .
            $attachment->asset->height);

        $title .= $this->Html->para(
            'small',
            'Size: ' . $this->Number->toReadableSize($attachment->asset->filesize)
        );

        if (empty($all) && empty($assetId)) {
            $title .= $this->Html->para(
                'small',
                'Number of versions: ' . $attachment->asset_count
            );
        }

        $rows[] = [
            $attachment->id,
            $thumbnail,
            $title,
            $actions,
        ];
    endforeach;

    echo $this->Html->tableCells($rows);
    $this->end();
    $this->set('tableFooter', $tableHeaders);

    if (!$this->getRequest()->is('ajax')) :
        $script = <<<EOF
        $('.popovers').popover().on('click', function() { return false; });
        Assets.init();
EOF;
        $this->Js->buffer($script);
    endif;
