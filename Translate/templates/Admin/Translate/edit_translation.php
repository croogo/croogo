<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $displayField
 * @var object $entity
 * @var mixed $fields
 * @var mixed $id
 * @var object $language
 * @var string $locale
 * @var mixed $model
 * @var mixed $modelAlias
 */

use Cake\Utility\Inflector;

$this->extend('/Common/admin_edit');
$this->assign('title', sprintf(__d('croogo', 'Translate content: %s (%s)'), $language->title, $language->native ?: $language->alias));
$this->set('className', 'translate');

$crumbLabel = $model == 'Nodes' ? __d('croogo', 'Content') : Inflector::pluralize($model);

$this->Breadcrumbs
    ->add($crumbLabel)
    ->add($entity->get($displayField))
    ->add(
        __d('croogo', 'Translations'),
        [
            'plugin' => 'Croogo/Translate',
            'controller' => 'Translate',
            'action' => 'index',
            '?' => [
                'id' => $id,
                'model' => $modelAlias,
            ],
        ]
    )
    ->add(__d('croogo', 'Translate (%s)', $language->title), $this->getRequest()->getRequestTarget());

$this->append('form-start', $this->Form->create($entity, [
    'url' => [
        'plugin' => 'Croogo/Translate',
        'controller' => 'Translate',
        'action' => 'editTranslation',
        $id,
        '?' => [
            'id' => $entity->id,
            'model' => $modelAlias,
            'locale' => $locale,
        ],
    ]
]));

$this->append('tab-heading');
    echo $this->Croogo->adminTab(__d('croogo', 'Translate'), '#translate-main');
    echo $this->Croogo->adminTab(__d('croogo', 'Original'), '#translate-original');
$this->end();

$this->append('tab-content');

    echo $this->Html->tabStart('translate-main');
foreach ($fields as $field) :
    $name = '_translations.' . $locale . '.' . $field;
    echo $this->Form->control($name, [
        'default' => $entity->get($field),
    ]);
endforeach;
    echo $this->Html->tabEnd();

    echo $this->Html->tabStart('translate-original');
foreach ($fields as $field) :
    $name = '_original.' . $field;
    echo $this->Form->control($name, [
        'value' => $entity->$field,
        'readonly' => true,
    ]);
endforeach;
    echo $this->Html->tabEnd();

$this->end();

$this->start('panels');
    echo $this->Html->beginBox(__d('croogo', 'Publishing'));

        echo $this->element('admin/buttons', [
            'cancelUrl' => [
                'action' => 'index',
                '?' => [
                    'id' => $this->getRequest()->getQuery('id'),
                    'model' => urldecode($this->getRequest()->getQuery('model')),
                ],
            ],
        ]);

    echo $this->Html->endBox();
$this->end();
