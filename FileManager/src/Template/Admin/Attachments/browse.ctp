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

if (!$this->request->is('ajax')):
    $this->Croogo->adminScript([
        'Croogo/FileManager.admin',
        'Croogo/FileManager.assets',
    ]);
endif;

$assetId = $filter = $filename = $type = $all = null;
if (empty($model) && !empty($this->request->getQuery('model'))):
    $model = $this->request->getQuery('model');
endif;
if (empty($foreignKey) && !empty($this->request->getQuery('foreign_key'))):
    $foreignKey = $this->request->getQuery('foreign_key');
endif;
if (!empty($this->request->getQuery('asset_id'))):
    $assetId = $this->request->getQuery('asset_id');
endif;
if (!empty($this->request->getQuery('type'))):
    $type = $this->request->getQuery('type');
endif;
if (!empty($this->request->getQuery('filter'))):
    $filter = $this->request->getQuery('filter');
endif;
if (!empty($this->request->getQuery('filename'))):
    $filename = $this->request->getQuery('filename');
endif;
if (!empty($this->request->getQuery('all'))):
    $all = $this->request->getQuery('all');
endif;
if (!empty($this->request->getQuery('editor'))):
    $editor = $this->request->getQuery('editor');
endif;
if (empty($editor) && !empty($this->request->getQuery('CKEditor'))):
    $editor = $this->request->getQuery('CKEditor');
endif;
if (!empty($this->request->getQuery('manage'))):
    $manage = $this->request->getQuery('manage');
endif;

$this->append('action-buttons');
    echo $this->Croogo->adminAction(
        __d('croogo', 'New Attachment'),
        array_merge(
            array('controller' => 'Attachments', 'action' => 'add', 'editor' => 1),
            array('?' => (array)$this->request->getQuery())
        )
    ) . ' ';

    $listUrl = [
        'controller' => 'Attachments',
        'action' => 'browse',
    ];
    if (isset($model) && isset($foreignKey)):
        $listUrl['?'] = [
            'model' => $model,
            'foreign_key' => $foreignKey,
        ];
    endif;

    if (!$all):
        $listUrl['?']['all'] = true;
        $listTitle = __d('assets', 'List All Attachments');
    else:
        $listTitle = __d('assets', 'List Attachments');
    endif;
    echo $this->Croogo->adminAction($listTitle, $listUrl, array(
        'button' => 'outline-success',
    ));
$this->end();

$this->append('table-heading');
    $tableHeaders = $this->Html->tableHeaders(array(
        $this->Paginator->sort('AssetsAsset.id', __d('croogo', 'Id')),
        '&nbsp;',
        $this->Paginator->sort('title', __d('croogo', 'Title')) . ' ' .
        $this->Paginator->sort('filename', __d('croogo', 'Filename')) . ' ' .
        $this->Paginator->sort('width', __d('assets', 'Width')) . ' ' .
        $this->Paginator->sort('height', __d('assets', 'Height')) . ' ' .
        $this->Paginator->sort('filesize', __d('croogo', 'Size')),
        __d('croogo', 'Actions'),
    ));
    echo $tableHeaders;
$this->end();

$this->append('table-body');
    $query = array('?' => (array)$this->request->getQuery());
    $rows = array();
    foreach ($attachments as $attachment):
        $actions = array();
        $mimeType = explode('/', $attachment->asset->mime_type);
        $mimeType = $mimeType['0'];

        if (isset($editor)):

            $actions[] = $this->Croogo->adminRowAction('', 'javascript:void(0)', array(
                'onclick' => "Croogo.Wysiwyg.choose('" . $attachment->asset->path . "');",
                'icon' => 'attach',
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Insert')
            ));

        endif;

        if (!isset($all) && !isset($assetId)) {
            $deleteUrl = Hash::merge($query, array(
                'controller' => 'Attachments',
                'action' => 'delete',
                $attachment->id,
                'editor' => 1,
            ));
            $actions[] = $this->Croogo->adminRowAction('', $deleteUrl, array(
                'icon' => $this->Theme->getIcon('delete'),
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Delete Attachment')
                ),
                __d('croogo', 'Are you sure?')
            );
        } elseif (isset($manage) && isset($assetId)) {
            $deleteAssetUrl = Hash::merge($query, array(
                'controller' => 'Assets',
                'action' => 'delete',
                $attachment->asset->id,
            ));
            $actions[] = $this->Croogo->adminRowAction('', $deleteAssetUrl, array(
                'icon' => $this->Theme->getIcon('delete'),
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Delete Attachment version')
                ),
                __d('croogo', 'Are you sure?')
            );
        }

        if ($mimeType === 'image' &&
            $attachment->hash == $attachment->asset->hash
        ) {
            $resizeUrl = Hash::merge($query, array(
                'action' => 'resize',
                $attachment->id,
                'ext' => 'json'
            ));
            $actions[] = $this->Croogo->adminRowAction('', $resizeUrl, array(
                'icon' => $this->Theme->getIcon('resize'),
                'escapeTitle' => false,
                'tooltip' => __d('croogo', 'Resize this item'),
                'data-toggle' => 'resize-asset'
            ));
        }

        if (isset($assetId) || isset($all)):
            unset($query['?']['asset_id']);

            if (isset($model) && isset($foreignKey)):
                $extractPath = "asset.asset_usage.{n}[model=$model][foreign_key=$foreignKey]";
                $usage = Hash::extract($attachment, $extractPath);
                if (empty($usage) && $model !== 'Attachments'):
                    $addUrl = Hash::merge(array(
                        'controller' => 'AssetUsages',
                        'action' => 'add',
                        '?' => array(
                            'asset_id' => $attachment->asset->id,
                            'model' => $model,
                            'foreign_key' => $foreignKey,
                        )
                    ), $query);
                    $actions[] = $this->Croogo->adminRowAction('', $addUrl, array(
                        'icon' => 'create',
                        'escapeTitle' => false,
                        'tooltip' => __d('croogo', 'Register asset with this resource'),
                        'method' => 'post',
                    ));
                endif;
            endif;
        elseif ($mimeType === 'image'):

            if (!$this->request->getQuery('manage')):
                $detailUrl = Hash::merge(array(
                    'action' => 'browse',
                    '?' => array(
                        'asset_id' => $attachment->asset->id,
                    )
                ), $query);
                $actions[] = $this->Croogo->adminRowAction('', $detailUrl, array(
                    'icon' => 'suitcase',
                    'escapeTitle' => false,
                    'tooltip' => __d('assets', 'View other sizes'),
                ));
            endif;

        endif;

        if ($mimeType == 'image') {
            $img = $this->AssetsImage->resize(
                $attachment->asset->path, 100, 200,
                array('adapter' => $attachment->asset->adapter)
            );
            $thumbnail = $this->Html->link($img,
                $attachment->asset->path,
                array(
                    'data-toggle' => 'lightbox',
                    'escape' => false,
                    'title' => $attachment->title,
                )
            );
            if (!empty($attachment['AssetsAssetUsage']['type']) &&
                $attachment['AssetsAssetUsage']['foreign_key'] === $foreignKey &&
                $attachment['AssetsAssetUsage']['model'] === $model
            ):
                $thumbnail .= $this->Html->div(null,
                    $this->Html->link(
                        $this->Html->tag('span',
                            $attachment['AssetsAssetUsage']['type'],
                            array('class' => 'badge badge-info')
                        ),
                        array(
                            'action' => 'browse',
                            '?' => array(
                                'type' => $attachment['AssetsAssetUsage']['type']
                            ) + $this->request->query,
                        ),
                        array(
                            'escape' => false,
                        )
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
            $thumbnail = $this->Html->link($thumbnail, '#', array(
                'escape' => false,
            ));
        }

        $actions = $this->Html->div('item-actions', implode(' ', $actions));

        $url = $this->Html->link(
            $this->Url->build($attachment->asset->path),
            $attachment->asset->path,
            array(
                'onclick' => "Croogo.Wysiwyg.choose('" . $attachment->asset->path . "'); return false;",
                'target' => '_blank',
            )
        );
        $urlPopover = $this->Croogo->adminRowAction('', '#', array(
            'class' => 'popovers',
            'icon' => 'link',
            'escapeTitle' => false,
            'iconSize' => 'small',
            'data-title' => __d('croogo', 'URL'),
            'data-html' => 'true',
            'data-placement' => 'top',
            'data-trigger' => 'click|focus',
            'data-content' => $url,
        ));

        $title = $this->Html->para(null, h($attachment->title));
        $title .= $this->Html->para('small',
            $this->Text->truncate(
                $attachment->asset->filename, 30
            ) . '&nbsp;' . $urlPopover,
            array('title' => $attachment->asset->filename)
        );

        $title .= $this->Html->para('small', 'Dimension: ' .
            $attachment->asset->width . ' x ' .
            $attachment->asset->height
        );

        $title .= $this->Html->para('small',
            'Size: ' . $this->Number->toReadableSize($attachment->asset->filesize)
        );

        if (empty($all) && empty($assetId)) {
            $title .= $this->Html->para('small',
                'Number of versions: ' . $attachment->asset_count
            );
        }

        $rows[] = array(
            $attachment->id,
            $thumbnail,
            $title,
            $actions,
        );
    endforeach;

    echo $this->Html->tableCells($rows);
$this->end();
$this->set('tableFooter', $tableHeaders);

$this->Js->buffer("$('.popovers').popover().on('click', function() { return false; });");
