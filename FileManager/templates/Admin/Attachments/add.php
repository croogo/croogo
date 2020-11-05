<?php
/**
 * @var \App\View\AppView $this
 * @var \Croogo\FileManager\Model\Entity\Attachment $attachment
 */

use Croogo\FileManager\Utility\StorageManager;

$this->extend('Croogo/Core./Common/admin_edit');

$this->Html->css([
    'Croogo/FileManager.jquery.fileupload',
    'Croogo/FileManager.jquery.fileupload-ui',
], [
    'block' => true,
]);

$this->Croogo->adminScript([
//  'Croogo/FileManager.fileupload/vendor/jquery.ui.widget',
    'Croogo/FileManager.fileupload/tmpl.min.js',
    'Croogo/FileManager.fileupload/load-image.all.min',
    'Croogo/FileManager.fileupload/canvas-to-blob.min',
    'Croogo/FileManager.fileupload/jquery.iframe-transport',
    'Croogo/FileManager.fileupload/jquery.fileupload',
    'Croogo/FileManager.fileupload/jquery.fileupload-process',
    'Croogo/FileManager.fileupload/jquery.fileupload-image',
    'Croogo/FileManager.fileupload/jquery.fileupload-audio',
    'Croogo/FileManager.fileupload/jquery.fileupload-video',
    'Croogo/FileManager.fileupload/jquery.fileupload-validate',
    'Croogo/FileManager.fileupload/jquery.fileupload-ui',
    'Croogo/FileManager.admin',
    'Croogo/FileManager.assets',
]);

$indexUrl = [
    'plugin' => 'Croogo/FileManager',
    'controller' => 'Attachments',
    'action' => 'index'
];

if (!$this->getRequest()->getQuery('editor')) :
    $this->Breadcrumbs
        ->add(__d('croogo', 'Attachments'), $indexUrl)
        ->add(__d('croogo', 'Upload'), $this->getRequest()->getUri()->getPath());
endif;

if ($this->layout === 'admin_popup') :
    $this->append('title', ' ');
endif;

$formUrl = ['plugin' => 'Croogo/FileManager', 'controller' => 'Attachments', 'action' => 'add'];
if ($this->getRequest()->getQuery('editor')) {
    $formUrl['editor'] = 1;
}
$this->append('form-start', $this->Form->create($attachment, [
    'url' => $formUrl,
    'type' => 'file',
    'id' => 'attachment-upload-form',
]));

$model = $this->getRequest()->getQuery('model') ?: null;
$foreignKey = $this->getRequest()->getQuery('foreign_key') ?: null;

$this->append('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Upload'), '#attachment-upload');
$this->end();

$this->append('tab-content');

    echo $this->Html->tabStart('attachment-upload');

if (isset($model) && isset($foreignKey)) :
    $assetUsage = 'asset.asset_usage.0.';
    echo $this->Form->control($assetUsage . 'model', [
        'type' => 'hidden',
        'value' => $model,
    ]);
    echo $this->Form->control($assetUsage . 'foreign_key', [
        'type' => 'hidden',
        'value' => $foreignKey,
    ]);
endif;

        echo $this->element('Croogo/FileManager.admin/fileupload');

if (isset($model) && isset($foreignKey)) :
    echo $this->Form->control($assetUsage . 'featured_image', [
        'type' => 'checkbox',
        'label' => 'Featured Image',
    ]);
endif;

        echo $this->Form->control('asset.adapter', [
            'type' => 'select',
            'default' => 'LocalAttachment',
            'options' => StorageManager::configured(),
        ]);
        echo $this->Form->control('excerpt', [
            'label' => __d('croogo', 'Caption'),
        ]);
        echo $this->Form->control('title');
        echo $this->Form->control('status', [
            'type' => 'hidden', 'value' => true,
        ]);
        echo $this->Form->control('asset.model', [
            'type' => 'hidden',
            'value' => 'Attachments',
        ]);

        echo $this->Html->tabEnd();
        $this->end();

        $this->append('panels');
        $redirect = ['action' => 'index'];
        if ($this->getRequest()->getSession()->check('Wysiwyg.redirect')) {
            $redirect = $this->getRequest()->getSession()->read('Wysiwyg.redirect');
        }
        if ($this->getRequest()->getQuery('model')) {
            $redirect = array_merge(
                ['action' => 'browse'],
                ['?' => $this->getRequest()->getQuery()]
            );
            unset($redirect['?']['editor']);
        }
        echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
        $this->Form->button(__d('croogo', 'Upload'), [
            'icon' => 'upload',
            'button' => 'primary',
            'class' => 'start btn-outline-success',
            'type' => 'submit',
            'id' => 'start_upload',
        ]) .
        $this->Form->end() . ' ' .
        $this->Html->link(__d('croogo', 'Cancel'), $redirect, [
            'class' => 'btn btn-outline-danger',
        ]);
        echo $this->Html->endBox();
        echo $this->Croogo->adminBoxes();
        $this->end();

        $editorMode = isset($formUrl['editor']) ? $formUrl['editor'] : 0;
        $xhrUploadUrl = $this->Url->build($formUrl);
        $redirectUrl = $this->Url->build($indexUrl);
        $script = <<<EOF

    \$('[data-toggle=tab]:first').tab('show');
    var filesToUpload = [];
    var uploadContext = [];
    var uploadResults = [];
    var \$form = \$('#attachment-upload-form');
    \$form.fileupload({
        url: '$xhrUploadUrl',
        add: function(e, data) {
            var that = this;
            $.blueimp.fileupload.prototype.options.add.call(that, e, data)
            filesToUpload.push(data.files[0]);
            uploadContext.push(data.context);
        },
        fail: function(e, data) {
            var that = this;
            filesToUpload.pop(data.files[0])
            uploadContext.pop(data.context)
            $.blueimp.fileupload.prototype.options.fail.call(that, e, data)
        }
    });

    var \$startUpload = $('#start_upload');

    var uploadHandler = function(e) {
        var enableStartUpload = function() {
            \$startUpload
                .one('click', uploadHandler)
                .html('Upload')
                .removeAttr('disabled');
        }

        if (filesToUpload.length == 0) {
            alert('No files to upload');
            enableStartUpload();
            return false;
        }

        for (var i in filesToUpload) {
            var xhr = \$form.fileupload('send', {
                files: [filesToUpload[i]],
                context: uploadContext[i]
            })
                .then(function(result, textStatus, jqXHR) {
                    uploadResults.push(result);
                })
                .catch(function(jqXHR, textStatus, errorThrown) {
                    if (jqXHR.responseJSON) {
                        uploadResults.push(jqXHR.responseJSON);
                    } else {
                        uploadResults.push(errorThrown);
                    }
                });
        }

        \$startUpload.html('<i class="fa fa-spin fa-spinner"></i> Upload')
            .attr('disabled', true);

        var checkInterval = setInterval(function() {
            var uploadCount = filesToUpload.length;
            var uploadSuccess = false;
            var errorMessage = false;
            for (var i = 0; i < uploadCount; i++) {
                if (typeof uploadResults[i] !== 'undefined') {
                    var errorType = typeof uploadResults[i].error;
                    if (errorType !== 'undefined') {
                        if (errorType === 'string') {
                            uploadSuccess = false;
                            errorMessage = uploadResults[i].error;
                        } else {
                            uploadSuccess = uploadResults[i].error === false;
                        }
                    }
                    if (typeof uploadResults[i].message !== 'undefined') {
                        errorMessage = uploadResults[i].message;
                        uploadSuccess = false
                    }
                }
                if (!uploadSuccess) {
                    break;
                }
            }

            if (uploadSuccess) {
                clearInterval(checkInterval);
                if (uploadCount > 1) {
                    alert(uploadCount + ' files uploaded successfully');
                }
                if ($editorMode == 1) {
                    \$startUpload
                        .removeAttr('disabled')
                        .text('Close')
                        .one('click', function(e) {
                            window.close();
                            return false;
                        });
                } else {
                    window.location = '$redirectUrl';
                }
            }

            if (errorMessage) {
                clearInterval(checkInterval);
                alert(errorMessage)
                filesToUpload = [];
                uploadContext = [];
                uploadResults = [];
                enableStartUpload();
            }
        }, 1000);

        return false;
    };

    \$startUpload.one('click', uploadHandler);
    Attachments.init();
    Assets.init();
EOF;

        $this->Js->buffer($script);
