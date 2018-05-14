<?php

use \Cake\Utility\Inflector;

$this->extend('/Common/admin_edit');
$this->assign('title', sprintf(__d('croogo', 'Translate content: %s (%s)'), $language->title, $language->native));
$this->set('className', 'translate');

$crumbLabel = $model == 'Nodes' ? __d('croogo', 'Content') : Inflector::pluralize($model);

$this->Breadcrumbs
    ->add($crumbLabel)
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
    ->add(__d('croogo', 'Translate (%s)', $language->title), $this->request->getRequestTarget());

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
                'readonly' => true,
            ]);
        endforeach;
    echo $this->Html->tabEnd();

$this->end();

$this->start('panels');
    echo $this->Html->beginBox(__d('croogo', 'Publishing'));

    $out =
        $this->Form->button(__d('croogo', 'Apply'), [
            'name' => 'apply',
            'class' => 'btn-outline-primary',
        ]) .
        $this->Form->button(__d('croogo', 'Save'), [
            'class' => 'btn-outline-success',
        ]) .
        $this->Html->link(__d('croogo', 'Cancel'), ['action' => 'index',
            '?' => [
                'id' => $this->request->query('id'),
                'model' => urldecode($this->request->query('model')),
            ],
        ], [
            'class' => 'cancel',
            'button' => 'outline-danger'
        ]);
    echo $this->Html->div('card-buttons d-flex justify-content-center', $out);
    echo $this->Html->endBox();
$this->end();
