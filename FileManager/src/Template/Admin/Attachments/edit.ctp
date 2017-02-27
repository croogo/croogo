<?php

$this->assign('title', __d('croogo', 'Edit Attachment'));
$this->extend('Croogo/Core./Common/admin_edit');

$this->Html->addCrumb(__d('croogo', 'Attachments'),
        ['plugin' => 'Croogo/FileManager', 'controller' => 'attachments', 'action' => 'index'])
    ->addCrumb($attachment->title, $this->request->here());

$this->append('form-start', $this->Form->create($attachment));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Attachment'), '#attachment-main');
$this->end();

$this->append('tab-content');

echo $this->Html->tabStart('attachment-main') . $this->Form->input('title', [
        'label' => __d('croogo', 'Title'),
    ]) . $this->Form->input('excerpt', [
        'label' => __d('croogo', 'Caption'),
    ]) . $this->Form->input('file_url', [
        'label' => __d('croogo', 'File URL'),
        'value' => $this->Url->build($attachment->path, true),
        'readonly' => 'readonly',
    ]) . $this->Form->input('file_type', [
            'label' => __d('croogo', 'Mime Type'),
            'value' => $attachment->mime_type,
            'readonly' => 'readonly',
        ]);
echo $this->Html->tabEnd();
$this->end();

$this->append('panels');
$redirect = ['action' => 'index'];
$session = $this->request->session();
if ($session->check('Wysiwyg.redirect')) {
    $redirect = $session->read('Wysiwyg.redirect');
}
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
    echo $this->element('Croogo/Core.admin/buttons', ['cancelUrl' => $redirect]);
echo $this->Html->endBox();

$fileType = explode('/', $attachment->mime_type);
$fileType = $fileType['0'];
if ($fileType == 'image'):
    $imgUrl = $this->Image->resize('/uploads/' . $attachment->slug, 200, 300, true);
else:
    $imgUrl = $this->Html->thumbnail('/croogo/core/img/icons/' .
            $this->Filemanager->mimeTypeToImage($attachment->mime_type)) . ' ' . $attachment->mime_type;
endif;
$preview = $this->Html->link($imgUrl, $attachment->path, [
    'class' => 'thickbox',
]);
echo $this->Html->beginBox(__d('croogo', 'Preview')) . $preview;
echo $this->Html->endBox();

$this->end();

$this->append('form-end', $this->Form->end());
