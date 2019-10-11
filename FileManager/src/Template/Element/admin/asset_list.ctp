<?php

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\view\Form\NullContext;

$tableHeaderClass = $this->Theme->getCssClass('tableHeaderClass');

$this->Html->script([
    'Croogo/FileManager.admin.js',
    'Croogo/FileManager.assets.js',
], [
    'block' => 'scriptBottom',
]);

$model = isset($model) ? $model : null;
if (!$model) :
    $context = $this->Form->context();
    if ($context instanceof NullContext) :
        $model = $this->getRequest()->getParam('controller');
    else :
        $modelSource = $this->Form->context()->entity()->getSource();
        list($junk, $model) = pluginSplit($modelSource);
    endif;
endif;
$primaryKey = isset($primaryKey) ? $primaryKey : 'id';

$varName = Inflector::variable(Inflector::singularize($this->getRequest()->getParam('controller')));
if (isset(${$varName})) :
    $data = ${$varName};
endif;

if (isset($foreignKey)) :
    $id = $foreignKey;
else :
    if (isset($data)) :
        $id = $data->get($primaryKey);
    endif;
endif;

$detailUrl = [
    'prefix' => 'admin',
    'plugin' => 'Croogo/FileManager',
    'controller' => 'Attachments',
    'action' => 'browse',
    '?' => [
        'manage' => true,
        'model' => $model,
        'foreign_key' => $id,
    ],
];

$changeTypeUrl = [
    'prefix' => 'admin',
    'plugin' => 'Croogo/FileManager',
    'controller' => 'AssetUsages',
    'action' => 'change_type',
];

$assetListUrl = $this->Url->build([
    'prefix' => 'admin',
    'plugin' => 'Croogo/FileManager',
    'controller' => 'Attachments',
    'action' => 'listing',
    '?' => [
        'model' => $model,
        'foreign_key' => $id,
    ],
]);

$unregisterUsageUrl = [
    'prefix' => 'admin',
    'plugin' => 'Croogo/FileManager',
    'controller' => 'AssetUsages',
    'action' => 'unregister',
];

if (!isset($attachments)) :
    $Attachment = TableRegistry::get('Croogo/FileManager.Attachments');
    if (isset($id)) :
        $attachments = $Attachment->find('modelAttachments', [
            'model' => $model,
            'foreign_key' => $id,
        ]);
    else :
        $attachments = [];
    endif;
endif;

$headers = [
    __d('croogo', 'Preview'),
    __d('croogo', 'Type'),
    __d('croogo', 'Size'),
    __d('croogo', 'Actions'),
];

//if (!$this->helpers()->loaded('AssetsImage')) {
    $this->loadHelper('Croogo/FileManager.AssetsImage');
//}

$this->Form->unlockField('usage-type');

$rows = [];
foreach ($attachments as $attachment) :
    $row = $action = [];
    $path = $attachment->asset->path;
    list($mimeType, ) = explode('/', $attachment->asset->mime_type);

    if ($mimeType === 'image') :
        $imgUrl = $this->AssetsImage->resize(
            $path,
            100,
            200,
            ['adapter' => $attachment->asset->adapter],
            ['alt' => $attachment->title, 'class' => 'img-thumbnail']
        );
        $thumbnail = $this->Html->link($imgUrl, $path, [
            'escape' => false,
            'data-toggle' => 'lightbox',
            'title' => $attachment->title
        ]);
    elseif ($mimeType === 'video') :
        $thumbnail = $this->Html->media($attachment->asset->path, [
            'width' => 200,
            'controls' => true,
        ]);
    else :
        $imgUrl = $this->Html->image('Croogo/Core./img/icons/page_white.png') . ' ';
        $filename = $attachment->asset->filename;
        $thumbnail = $imgUrl . $this->Html->link(
            $this->Text->truncate($filename, 15),
            $attachment->asset->path,
            [
                'escape' => false,
                'target' => '_blank',
                'data-title' => $filename,
            ]
        );
    endif;

    $preview = $this->Html->div(null, $thumbnail);
    if ($mimeType === 'image') :
        $preview .= $this->Html->div(null, sprintf(
            '<small>Shortcode: [image:%s]</small>',
            $attachment->asset_usage->id
        ));
        $preview .= $this->Html->tag('small', sprintf(
            'Dimension: %sx%s',
            $attachment->asset->width,
            $attachment->asset->height
        ));
    else :
        if ($attachment->title) :
            $preview .= sprintf(
                '<small title="%s">%s</small>',
                $attachment->title,
                h($attachment->title)
            );
        endif;
    endif;

    $detailUrl['?']['asset_id'] = $attachment->asset->id;

    $typeCell = $this->Form->input('usage-type', [
        'type' => 'select',
        'label' => false,
        'options' => [
            $attachment->asset_usage->type => $attachment->asset_usage->type,
        ],
        'class' => 'change-usage-type',
        'data-pk' => $attachment->asset_usage->id,
        'data-url' => $this->Url->build($changeTypeUrl),
        'data-name' => 'type',
        'data-theme' => $this->Theme->settings('select2Defaults.theme'),
        'data-width' => '150px',
        'data-tags' => 'true',
        'data-placeholder' => '',
        'data-allow-clear' => 'true',
        'data-token-separators' => [' ', ','],
    ]);

    $row[] = $preview;
    $row[] = $typeCell;
    $row[] = $this->Number->toReadableSize($attachment->asset->filesize);

    if ($mimeType === 'image') :
        $action[] = $this->Croogo->adminRowAction('', $detailUrl, [
            'icon' => 'suitcase',
            'escapeTitle' => false,
            'data-toggle' => 'browse',
            'tooltip' => __d('assets', 'View other sizes'),
        ]);

        $action[] = $this->Croogo->adminRowAction('', $changeTypeUrl, [
            'icon' => 'star',
            'escapeTitle' => false,
            'class' => 'set-featured-image',
            'data-pk' => $attachment->asset_usage->id,
            'tooltip' => __d('assets', 'Set as FeaturedImage'),
        ]);
    endif;

    $action[] = $this->Croogo->adminRowAction('', $unregisterUsageUrl, [
        'icon' => 'minus',
        'escapeTitle' => false,
        'class' => 'unregister-usage red',
        'data-id' => $attachment->asset_usage->id,
        'tooltip' => __d('assets', 'Unregister asset from this resource'),
    ]);
    $row[] = '<span class="actions">' . implode('&nbsp;', $action) . '</span>';
    $rows[] = $row;
endforeach;

$browseUrl = array_merge(
    Configure::read('Wysiwyg.attachmentBrowseUrl'),
    [
        'model' => $model,
        'foreign_key' => $id,
        'all' => true
    ]
);

$uploadUrl = [
    'prefix' => 'admin',
    'plugin' => 'Croogo/FileManager',
    'controller' => 'Attachments',
    'action' => 'add',
    'editor' => true,
    '?' => [
        'model' => $model,
        'foreign_key' => $id,
    ],
];

if (!isset($_assetButtons)) :
    $this->append('action-buttons');
    echo $this->Croogo->adminAction(
        __d('assets', 'Reload'),
        $browseUrl,
        [
            'div' => false,
            'icon' => 'refresh',
            'iconSize' => 'small',
            'data-toggle' => 'refresh',
            'tooltip' => __d('assets', 'Reload asset list for this content'),
        ]
    );
    echo $this->Croogo->adminAction(
        __d('assets', 'Browse'),
        $browseUrl,
        [
            'div' => false,
            'icon' => 'folder-open',
            'iconSize' => 'small',
            'data-toggle' => 'browse',
            'tooltip' => __d('assets', 'Browse available assets'),
        ]
    );
    echo $this->Croogo->adminAction(
        __d('assets', 'Upload'),
        $uploadUrl,
        [
            'div' => false,
            'icon' => 'upload',
            'iconSize' => 'small',
            'data-toggle' => 'browse',
            'tooltip' => __d('assets', 'Upload new asset for this content'),
        ]
    );
    $this->end();
    $this->set('_assetButtons', true);
endif;
?>
<table class="<?php echo $this->Theme->getCssClass('tableClass'); ?> asset-list" data-url="<?php echo $assetListUrl; ?>">
    <thead class="<?= $tableHeaderClass ?>"><?php echo $this->Html->tableHeaders($headers); ?></thead>
    <tbody><?php echo $this->Html->tableCells($rows); ?></tbody>
</table>
<?php

$script = <<<EOF
    if (typeof $.fn.select2 == 'function') {
        $('.change-usage-type').select2();
    }
    Assets.init();
EOF;
if ($this->getRequest()->is('ajax')) :
    // re-initialize the tooltips on an ajax reload
    $script .= "\n    Admin.form();\n";
    echo $this->Html->scriptBlock($script);
else :
    $this->Js->buffer($script);
endif;
