<?php

$this->loadHelper('Croogo/FileManager.Filemanager');

$this->extend('Croogo/Core./Common/admin_edit');

$this->Croogo->adminScript(['Croogo/FileManager.admin', 'Croogo/FileManager.assets']);

$this->Breadcrumbs
    ->add(__d('croogo', 'Attachments'), ['plugin' => 'Croogo/FileManager', 'controller' => 'Attachments', 'action' => 'index'])
    ->add(h($attachment->title), $this->getRequest()->getUri()->getPath());

if ($this->layout === 'admin_popup') :
    $this->append('title', ' ');
endif;

$formUrl = ['controller' => 'Attachments', 'action' => 'edit'];
if ($this->getRequest()->getQuery()) {
    $formUrl = array_merge($formUrl, $this->getRequest()->getQuery());
}

$this->append('form-start', $this->Form->create($attachment, [
    'url' => $formUrl,
]));

$this->append('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Attachment'), '#attachment-main');
    echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');
    echo $this->Html->tabStart('attachment-main');
        echo $this->Form->input('id');

        echo $this->Form->input('title', [
            'label' => __d('croogo', 'Title'),
        ]);
        echo $this->Form->input('excerpt', [
            'label' => __d('croogo', 'Excerpt'),
        ]);

        echo $this->Form->input('file_url', [
            'label' => __d('croogo', 'File URL'),
            'value' => $this->Url->build($attachment->asset->path, true),
            'readonly' => 'readonly']);

        echo $this->Form->input('file_type', [
            'label' => __d('croogo', 'Mime Type'),
            'value' => $attachment->asset->mime_type,
            'readonly' => 'readonly']);
        echo $this->Html->tabEnd();

        echo $this->Croogo->adminTabs();
        $this->end();

        $this->append('panels');
        $redirect = $this->getRequest()->getQuery('redirect') ?: ['action' => 'index'];
        if ($this->getRequest()->getSession()->check('Wysiwyg.redirect')) {
            $redirect = $this->getRequest()->getSession()->read('Wysiwyg.redirect');
        }
        if ($this->getRequest()->getQuery('model')) {
            $redirect = array_merge(
                ['action' => 'browse'],
                ['?' => $this->getRequest()->getQuery()]
            );
        }
        echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
        $this->Form->button($this->Html->icon('save') . __d('croogo', 'Save'), [
            'icon' => 'save',
            'class' => 'btn-outline-success',
        ]) . ' ' .
        $this->Html->link(
            $this->Html->icon('times') . __d('croogo', 'Cancel'),
            $redirect,
            [
                'class' => 'cancel btn btn-outline-danger',
                'escapeTitle' => false,
            ]
        );
        echo $this->Html->endBox();

        $fileType = explode('/', $attachment->asset->mime_type);
        $fileType = $fileType['0'];
        $path = $attachment->asset->path;
        if ($fileType == 'image') :
            $imgUrl = $this->AssetsImage->resize(
                $path,
                200,
                300,
                ['adapter' => $attachment->asset->adapter]
            );
        else :
            $imgUrl = $this->Html->image('Croogo/Core./img/icons/' . $this->FileManager->mimeTypeToImage($attachment->mime_type)) . ' ' . $attachment->mime_type;
        endif;

        if (preg_match('/^image/', $attachment->asset->mime_type)) :
            echo $this->Html->beginBox(__d('croogo', 'Preview')) .
            $this->Html->link($imgUrl, $attachment->asset->path, [
                'data-toggle' => 'lightbox',
                'escapeTitle' => false,
            ]);
            echo $this->Html->endBox();
        endif;

        $this->end();

        $this->append('form-end', $this->Form->end());
