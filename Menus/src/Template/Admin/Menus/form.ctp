<?php

use Croogo\Core\CroogoStatus;

$this->extend('Croogo/Core./Common/admin_edit');

$this->CroogoHtml
	->addCrumb('', '/admin', ['icon' => $_icons['home']])
	->addCrumb(__d('croogo', 'Menus'), ['action' => 'index']);

if ($this->request->params['action'] == 'edit') {
	$this->CroogoHtml->addCrumb($menu->title, '/' . $this->request->url);

	$this->assign('title', __d('croogo', 'Edit Menu'));
}

if ($this->request->params['action'] == 'add') {
	$this->CroogoHtml->addCrumb(__d('croogo', 'Add'), '/' . $this->request->url);

	$this->assign('title', __d('croogo', 'Add Menu'));
}

$this->append('form-start', $this->CroogoForm->create($menu));

$this->append('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Menu'), '#menu-basic');
    echo $this->Croogo->adminTab(__d('croogo', 'Misc.'), '#menu-misc');
    echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');
    echo $this->Html->tabStart('menu-basic');
        echo $this->CroogoForm->input('id');
        $this->CroogoForm->templates(array(
            'class' => 'span10',
        ));
        echo $this->CroogoForm->input('title', array(
            'label' => __d('croogo', 'Title'),
        ));
        echo $this->CroogoForm->input('alias', array(
            'label' => __d('croogo', 'Alias'),
        ));
        echo $this->CroogoForm->input('description', array(
            'label' => __d('croogo', 'Description'),
        ));
    echo $this->Html->tabEnd();
$this->end();

$this->append('tab-content');
    echo $this->Html->tabStart('menu-misc');
        echo $this->CroogoForm->input('params', array(
            'label' => __d('croogo', 'Params'),
        ));
    echo $this->Html->tabEnd();

    echo $this->Croogo->adminTabs();
$this->end();

$this->start('panels');
    echo $this->CroogoHtml->beginBox('Publishing') .
        $this->CroogoForm->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
        $this->CroogoForm->button(__d('croogo', 'Save'), array('button' => 'success')) .
        $this->CroogoHtml->link(__d('croogo', 'Cancel'), array('action' => 'index'), array('button' => 'danger')) .
        $this->CroogoForm->input('status', array(
            'type' => 'radio',
            'legend' => false,
            'class' => false,
            'default' => CroogoStatus::UNPUBLISHED,
            'options' => $this->Croogo->statuses(),
        )) .
        $this->CroogoHtml->div('input-daterange',
            $this->CroogoForm->input('publish_start', array(
                'label' => __d('croogo', 'Publish Start'),
                'type' => 'text',
            )) .
            $this->CroogoForm->input('publish_end', array(
                'label' => __d('croogo', 'Publish End'),
                'type' => 'text',
            ))
        ) .
        $this->CroogoHtml->endBox();

		$this->Croogo->adminBoxes();
echo $this->end('panels');

$this->append('form-end', $this->CroogoForm->end());
