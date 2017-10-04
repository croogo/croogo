<?php
echo $this->Form->create(null, [
    'align' => 'inline',
]);

$this->Form->templates([
    'label' => false,
    'submitContainer' => '{{content}}',
]);

echo $this->Form->input('filter', [
    'title' => __d('croogo', 'Search'),
    'placeholder' => __d('croogo', 'Search...'),
    'tooltip' => false,
    'default' => $this->request->query('filter'),
]);

if (!isset($this->request->query['chooser'])):

    echo $this->Form->input('type', [
        'options' => $nodeTypes,
        'empty' => __d('croogo', 'Type'),
        'class' => 'c-select',
        'default' => $this->request->query('type'),
    ]);

    echo $this->Form->input('status', [
        'options' => [
            '1' => __d('croogo', 'Published'),
            '0' => __d('croogo', 'Unpublished'),
        ],
        'empty' => __d('croogo', 'Status'),
        'class' => 'c-select',
        'default' => $this->request->query('status'),
    ]);

    echo $this->Form->input('promote', [
        'options' => [
            '1' => __d('croogo', 'Yes'),
            '0' => __d('croogo', 'No'),
        ],
        'empty' => __d('croogo', 'Promoted'),
        'class' => 'c-select',
        'default' => $this->request->query('promote'),
    ]);

endif;

echo $this->Form->submit(__d('croogo', 'Filter'), [
    'class' => 'btn-outline-success',
]);
echo $this->Html->link('Reset', [
    'action' => 'index',
], [
    'class' => 'btn btn-outline-secondary',
]);
echo $this->Form->end();
