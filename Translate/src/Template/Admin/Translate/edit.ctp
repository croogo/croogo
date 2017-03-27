<?php

use \Cake\Utility\Inflector;

$this->extend('/Common/admin_edit');
$this->assign('title', sprintf(__d('croogo', 'Translate content: %s (%s)'), $language->title, $language->native));
$this->set('className', 'translate');

$this->Breadcrumbs
    ->add(Inflector::humanize(Inflector::pluralize($model)))
    ->add($entity->get($displayField))
    ->add(
        __d('croogo', 'Translations'),
        array(
            'plugin' => 'Croogo/Translate',
            'controller' => 'Translate',
            'action' => 'index',
            '?' => [
                'id' => $id,
                'model' => $modelAlias,
            ],
        )
    )
    ->add(__d('croogo', 'Translate'), $this->request->here());

$this->append('form-start', $this->Form->create($entity, array(
    'url' => array(
        'plugin' => 'Croogo/Translate',
        'controller' => 'Translate',
        'action' => 'edit',
        $id,
        '?' => [
            'id' => $entity->id,
            'model' => $modelAlias,
            'locale' => $locale,
        ],
    )
)));

$this->append('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Translate'), '#translate-main');
    echo $this->Croogo->adminTab(__d('croogo', 'Original'), '#translate-original');
    echo $this->Croogo->adminTabs();
$this->end();

$this->append('tab-content');

    echo $this->Html->tabStart('translate-main');
        foreach ($fields as $field):
            $name = '_translations.' . $locale . '.' . $field;
            echo $this->Form->input($name, [
                'default' => $entity->get($field),
            ]);
        endforeach;
    echo $this->Html->tabEnd();

    echo $this->Html->tabStart('translate-original');
        foreach ($fields as $field):
            $name = '_original.' . $field;
            echo $this->Form->input($name, [
                'value' => $entity->$field,
            ]);
        endforeach;
    echo $this->Html->tabEnd();

    echo $this->Croogo->adminTabs();
$this->end();

$this->start('panels');
    echo $this->Html->beginBox(__d('croogo', 'Publishing')) .
        $this->Form->button(__d('croogo', 'Apply'), array('name' => 'apply')) .
        $this->Form->button(__d('croogo', 'Save'), array('button' => 'success')) .
        $this->Html->link(__d('croogo', 'Cancel'), array('action' => 'index',
            '?' => [
                'id' => $this->request->query('id'),
                'model' => urldecode($this->request->query('model')),
            ],
        ), array(
            'class' => 'cancel',
            'button' => 'danger'
        ));
    echo $this->Html->endBox();
$this->end();

$this->append('form-end', $this->Form->end());
