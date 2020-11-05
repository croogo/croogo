<?php
/**
 * @var \App\View\AppView $this
 * @var \Croogo\Menus\Model\Entity\Menu $menu
 */

$this->extend('Croogo/Core./Common/admin_edit');
$this->Croogo->adminScript('Croogo/Menus.admin');

$this->Breadcrumbs->add(__d('croogo', 'Menus'), ['action' => 'index']);

if ($this->getRequest()->getParam('action') == 'edit') {
    $this->Breadcrumbs->add(h($menu->title), $this->getRequest()->getRequestTarget());

    $this->assign('title', __d('croogo', 'Edit Menu'));
}

if ($this->getRequest()->getParam('action') == 'add') {
    $this->Breadcrumbs->add(__d('croogo', 'Add'), $this->getRequest()->getRequestTarget());

    $this->assign('title', __d('croogo', 'Add Menu'));
}

$this->append('form-start', $this->Form->create($menu));

$this->append('tab-heading');
echo $this->Croogo->adminTab(__d('croogo', 'Menu'), '#menu-basic');
echo $this->Croogo->adminTab(__d('croogo', 'Misc.'), '#menu-misc');
$this->end();

$this->append('tab-content');
echo $this->Html->tabStart('menu-basic');
echo $this->Form->control('title', [
    'label' => __d('croogo', 'Title'),
    'data-slug' => '#alias'
]);
echo $this->Form->control('alias', [
    'label' => __d('croogo', 'Alias'),
]);
echo $this->Form->control('description', [
    'label' => __d('croogo', 'Description'),
]);
echo $this->Html->tabEnd();
$this->end();

$this->append('tab-content');
echo $this->Html->tabStart('menu-misc');
echo $this->Form->control('params', [
    'label' => __d('croogo', 'Params'),
    'type' => 'stringlist',
]);
echo $this->Form->control('class');
echo $this->Html->tabEnd();

$this->end();

$this->start('panels');
echo $this->Html->beginBox('Publishing');
echo $this->element('Croogo/Core.admin/buttons', ['type' => 'menu']);
echo $this->element('Croogo/Core.admin/publishable');
echo $this->Html->endBox();
$this->end();
