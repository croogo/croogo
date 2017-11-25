<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs
    ->add(__d('croogo', 'Comments'), ['action' => 'index']);

$this->append('form-start', $this->Form->create($comment));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Comment'), '#comment-main');

$this->end();

$this->append('tab-content');

echo $this->Html->tabStart('comment-main') . $this->Form->input('id') . $this->Form->input('title', [
        'label' => __d('croogo', 'Title'),
    ]) . $this->Form->input('body', [
        'label' => __d('croogo', 'Body'),
    ]);
echo $this->Html->tabEnd();
$this->end();

$this->start('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
echo $this->element('Croogo/Core.admin/buttons', ['type' => 'comments']);
echo $this->Html->endBox();

echo $this->Html->beginBox(__d('croogo', 'Contact'));
echo $this->Form->input('name', ['label' => __d('croogo', 'Name')]);
echo $this->Form->input('email', ['label' => __d('croogo', 'Email')]);
echo $this->Form->input('website', ['label' => __d('croogo', 'Website')]);
echo $this->Form->input('ip', [
    'disabled' => 'disabled',
    'label' => __d('croogo', 'Ip'),
]);
echo $this->Html->endBox();

$this->end();
