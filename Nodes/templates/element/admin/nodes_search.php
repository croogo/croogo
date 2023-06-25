<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $nodeTypes
 */
echo $this->Form->create(null, [
    'align' => 'inline',
]);

$this->Form->setTemplates([
    'label' => '',
    'submitContainer' => '{{content}}',
]);

echo $this->Form->control('filter', [
    'title' => __d('croogo', 'Search'),
    'placeholder' => __d('croogo', 'Search...'),
    'tooltip' => false,
    'default' => $this->getRequest()->getQuery('filter'),
]);

echo $this->Form->control('type', [
    'options' => $nodeTypes,
    'empty' => __d('croogo', 'Type'),
    'class' => 'c-select',
    'default' => $this->getRequest()->getQuery('type'),
]);

if (!$this->getRequest()->getQuery('chooser')) :
    echo $this->Form->control('status', [
        'options' => [
            '1' => __d('croogo', 'Published'),
            '0' => __d('croogo', 'Unpublished'),
        ],
        'empty' => __d('croogo', 'Status'),
        'class' => 'c-select',
        'default' => $this->getRequest()->getQuery('status'),
    ]);

    echo $this->Form->control('promote', [
        'options' => [
            '1' => __d('croogo', 'Yes'),
            '0' => __d('croogo', 'No'),
        ],
        'empty' => __d('croogo', 'Promoted'),
        'class' => 'c-select',
        'default' => $this->getRequest()->getQuery('promote'),
    ]);
endif;

echo $this->Form->submit(__d('croogo', 'Filter'), [
    'class' => 'btn-outline-success mr-1',
]);
echo $this->Html->link('Reset', array_merge([
    'action' => 'index',
], [
    '?' => [
        'chooser' => $this->getRequest()->getQuery('chooser')
    ],
]), [
    'class' => 'btn btn-outline-secondary',
]);
echo $this->Form->end();
