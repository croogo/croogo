<?php

$this->assign('title', __d('croogo', 'Add Attachment'));
$this->extend('Croogo/Core./Common/admin_edit');

$this->Html->addCrumb(__d('croogo', 'Attachments'),
        ['plugin' => 'Croogo/FileManager', 'controller' => 'attachments', 'action' => 'index'])
    ->addCrumb(__d('croogo', 'Upload'));

$formUrl = ['controller' => 'attachments', 'action' => 'add'];
if (isset($this->request->params['named']['editor'])) {
    $formUrl['editor'] = 1;
}

$this->append('form-start', $this->Form->create($attachment, [
    'url' => $formUrl,
    'type' => 'file',
]));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Upload'), '#attachment-upload');
$this->end();

$this->append('tab-content');
echo $this->Html->tabStart('attachment-upload') . $this->Form->input('file', [
        'type' => 'file',
        'label' => __d('croogo', 'Upload'),
        'templates' => [
            ''
        ]
    ]);
echo $this->Html->tabEnd();

$this->end();

$this->start('panels');
$redirect = ['action' => 'index'];
$session = $this->request->session();
if ($session->check('Wysiwyg.redirect')) {
    $redirect = $ession->read('Wysiwyg.redirect');
}
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
echo $this->element('Croogo/Core.admin/buttons', ['saveText' => __d('croogo', 'Upload file')]);
echo $this->Html->endBox();
$this->end();

$this->append('form-end', $this->Form->end());
