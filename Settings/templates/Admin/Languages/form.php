<?php
/**
 * @var \App\View\AppView $this
 * @var \Croogo\Settings\Model\Entity\Language $language
 */

$this->extend('Croogo/Core./Common/admin_edit');

$this->Breadcrumbs->add(
    __d('croogo', 'Settings'),
    ['plugin' => 'Croogo/Settings', 'controller' => 'Settings', 'action' => 'prefix', 'Site']
)
    ->add(
        __d('croogo', 'Language'),
        ['plugin' => 'Croogo/Settings', 'controller' => 'Languages', 'action' => 'index']
    );

if ($this->getRequest()->getParam('action') == 'edit') {
    $this->Breadcrumbs->add($language->title);
}

if ($this->getRequest()->getParam('action') == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->getRequest()->getRequestTarget());
}

$this->append('form-start', $this->Form->create($language));

$this->start('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Language'), '#language-main');
$this->end();

$this->start('tab-content');
echo $this->Html->tabStart('language-main');
echo $this->Form->control('title', [
    'label' => __d('croogo', 'Title'),
]);
echo $this->Form->control('native', [
    'label' => __d('croogo', 'Native'),
]);
echo $this->Form->control('locale', [
    'label' => __d('croogo', 'Locale'),
]);
echo $this->Form->control('alias', [
    'label' => __d('croogo', 'Alias'),
    'help' => __d('croogo', 'Locale alias, typically a two letter country/locale code'),
]);
echo $this->Html->tabEnd();
$this->end();

$this->start('panels');
echo $this->Html->beginBox(__d('croogo', 'Publishing'));
echo $this->element('Croogo/Core.admin/buttons', ['type' => 'language']);
echo $this->Form->control('status', [
    'label' => __d('croogo', 'Status'),
]);
echo $this->Html->endBox();
$this->end();
