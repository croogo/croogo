<?php

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(__d('croogo', 'Settings'),
    ['plugin' => 'Croogo/Settings', 'controller' => 'settings', 'action' => 'prefix', 'Site'])
    ->add(__d('croogo', 'Language'),
        ['plugin' => 'Croogo/Settings', 'controller' => 'languages', 'action' => 'index']);

if ($this->request->params['action'] == 'edit') {
    $this->Breadcrumbs->add($language->title);
}

if ($this->request->params['action'] == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->request->getRequestTarget());
}

$this->append('form-start', $this->Form->create($language));

$this->start('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Language'), '#language-main');
$this->end();

$this->start('tab-content');
echo $this->Html->tabStart('language-main');
echo $this->Form->input('title', [
    'label' => __d('croogo', 'Title'),
]);
echo $this->Form->input('native', [
    'label' => __d('croogo', 'Native'),
]);
echo $this->Form->input('locale', [
    'label' => __d('croogo', 'Locale'),
]);
echo $this->Form->input('alias', [
    'label' => __d('croogo', 'Alias'),
    'help' => __d('croogo', 'Locale alias, typically a two letter country/locale code'),
]);
echo $this->Html->tabEnd();
$this->end();

$this->start('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
echo $this->element('Croogo/Core.admin/buttons', ['type' => 'language']);
echo $this->Form->input('status', [
    'label' => __d('croogo', 'Status'),
]);
echo $this->Html->endBox();
$this->end();
