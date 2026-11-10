<?php

$this->extend('/Common/admin_edit');

$this->Breadcrumbs
    ->add(__d('croogo', 'Extensions'), array('plugin' => 'Croogo/Extensions', 'controller' => 'Plugins', 'action' => 'index'))
    ->add(__d('croogo', 'Locales'), array('plugin' => 'Croogo/Extensions', 'controller' => 'Locales', 'action' => 'index'))
    ->add($this->getRequest()->getParam('pass')[0], $this->getRequest()->getRequestTarget());

$this->append('form-start', $this->Form->create($locale, array(
    'url' => array(
        'plugin' => 'Croogo/Extensions',
        'controller' => 'Locales',
        'action' => 'edit',
        $locale['locale'],
    ),
)));

$this->append('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Content'), '#locale-content');
$this->end();

$this->append('tab-content');
    echo $this->Html->tabStart('locale-content') .
        $this->Form->input('content', array(
            'label' => __d('croogo', 'Content'),
            'data-placement' => 'top',
            'value' => $content,
            'type' => 'textarea',
        ));
    echo $this->Html->tabEnd();

$this->end();

$this->append('panels');
    echo $this->Html->beginBox(__d('croogo', 'Actions')) .
        $this->Form->button(__d('croogo', 'Save')) .
        $this->Html->link(__d('croogo', 'Cancel'),
            array('action' => 'index'),
            array('button' => 'danger')
        );
    echo $this->Html->endBox();

    echo $this->Croogo->adminBoxes();
$this->end();
