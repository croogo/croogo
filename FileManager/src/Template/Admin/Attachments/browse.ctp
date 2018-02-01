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

$this->Croogo->adminScript([
    'Croogo/FileManager.admin',
    'Croogo/FileManager.assets',
]);

$assetId = $filter = $filename = $type = $all = null;
if (empty($model) && !empty($this->request->query['model'])):
    $model = $this->request->query['model'];
endif;
if (empty($foreignKey) && !empty($this->request->query['foreign_key'])):
    $foreignKey = $this->request->query['foreign_key'];
endif;
if (!empty($this->request->query['asset_id'])):
    $assetId = $this->request->query['asset_id'];
endif;
if (!empty($this->request->query['type'])):
    $type = $this->request->query['type'];
endif;
if (!empty($this->request->query['filter'])):
    $filter = $this->request->query['filter'];
endif;
if (!empty($this->request->query['filename'])):
    $filename = $this->request->query['filename'];
endif;
if (!empty($this->request->query['all'])):
    $all = $this->request->query['all'];
endif;

$this->append('action-buttons');
    echo $this->Croogo->adminAction(
        __d('croogo', 'New Attachment'),
        array_merge(
            array('controller' => 'Attachments', 'action' => 'add', 'editor' => 1),
            array('?' => $this->request->query)
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
        'button' => 'success',
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
    $query = array('?' => $this->request->query);
    $rows = array();
    foreach ($attachments as $attachment):
        $actions = array();
        $mimeType = explode('/', $attachment->asset->mime_type);
        $mimeType = $mimeType['0'];

        if (isset($this->request->query['editor'])):

            $actions[] = $this->Html->link('', 'javascript:void(0)', array(
                'onclick' => "Croogo.Wysiwyg.choose('" . $attachment->asset->path . "');",
                'icon' => 'attach',
                'tooltip' => __d('croogo', 'Insert')
            ));

        endif;

        $deleteUrl = Hash::merge($query, array(
            'controller' => 'Attachments',
            'action' => 'delete',
            $attachment->id,
            'editor' => 1,
        ));

        $deleteAssetUrl = Hash::merge($query, array(
            'controller' => 'Assets',
            'action' => 'delete',
            $attachment->asset->id,
        ));

        $resizeUrl = array_merge(
            array('action' => 'resize', $attachment->id, 'ext' => 'json'),
            array('?' => $query)
        );

        if (!isset($this->request->query['all']) &&
            !isset($this->request->query['asset_id'])
        ) {
            $actions[] = $this->Croogo->adminRowAction('', $deleteUrl, array(
                'icon' => $this->Theme->getIcon('delete'),
                'tooltip' => __d('croogo', 'Delete Attachment')
                ),
                __d('croogo', 'Are you sure?')
            );
        } elseif (isset($this->request->query['manage']) &&
            isset($this->request->query['asset_id'])
        ) {
            $actions[] = $this->Croogo->adminRowAction('', $deleteAssetUrl, array(
                'icon' => 'delete',
                'icon' => $this->Theme->getIcon('delete'),
                'tooltip' => __d('croogo', 'Delete Attachment version')
                ),
                __d('croogo', 'Are you sure?')
            );
        }

        if ($mimeType === 'image' &&
            $attachment->hash == $attachment->asset->hash
        ) {
            $resizeUrl = array_merge(
                array('action' => 'resize', $attachment->id, 'ext' => 'json'),
                array('?' => $query)
            );
            $actions[] = $this->Croogo->adminRowAction('', $resizeUrl, array(
                'icon' => $this->Theme->getIcon('resize'),
                'tooltip' => __d('croogo', 'Resize this item'),
                'data-toggle' => 'resize-asset'
            ));
        }

        if (isset($this->request->query['asset_id']) ||
            isset($this->request->query['all'])
        ):
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
                        'method' => 'post',
                    ));
                endif;
            endif;
        elseif ($mimeType === 'image'):

            if (!$this->request->query('manage')):
                $detailUrl = Hash::merge(array(
                    'action' => 'browse',
                    '?' => array(
                        'asset_id' => $attachment->asset->id,
                    )
                ), $query);
                $actions[] = $this->Html->link('', $detailUrl, array(
                    'icon' => 'suitcase',
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
            'iconSize' => 'small',
            'data-title' => __d('croogo', 'URL'),
            'data-html' => 'true',
            'data-placement' => 'top',
            'data-trigger' => 'click|focus',
            'data-content' => $url,
        ));

        $title = $this->Html->para(null, $attachment->title);
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

        if (empty($this->request->query['all']) && empty($this->request->query['asset_id'])) {
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
