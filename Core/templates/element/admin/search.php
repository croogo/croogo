<?php
/**
 * @var \App\View\AppView $this
 * @var mixed $searchFields
 */

use Cake\Utility\Hash;
use Cake\Utility\Inflector;

if (empty($modelClass)) {
    $modelClass = $this->name;
}
if (!empty($searchFields)) :
    echo $this->Form->create(null, [
        'align' => 'inline',
        'novalidate' => true,
        'url' => [
            'plugin' => $this->getRequest()->getParam('plugin'),
            'controller' => $this->getRequest()->getParam('controller'),
            'action' => $this->getRequest()->getParam('action'),
        ],
    ]);
    $this->Form->setTemplates([
        'label' => '',
        'submitContainer' => '{{content}}',
    ]);
    if ($this->getRequest()->getQuery('chooser')) :
        echo $this->Form->control('chooser', [
            'type' => 'hidden',
            'value' => $this->getRequest()->getQuery('chooser'),
        ]);
    endif;
    foreach ($searchFields as $field => $fieldOptions) {
        $options = ['empty' => true, 'required' => false];
        if (is_numeric($field) && is_string($fieldOptions)) {
            $field = $fieldOptions;
            $fieldOptions = [];
        }
        if (!empty($fieldOptions)) {
            $options = Hash::merge($fieldOptions, $options);
        }
        $label = $field;
        if (substr($label, -3) === '_id') {
            $label = substr($label, 0, -3);
        }
        $options['default'] = $this->getRequest()->getQuery($field);
        $label = __(Inflector::humanize(Inflector::underscore($label)));
        $options['placeholder'] = __d('croogo', $label);
        $this->Form->unlockField($field);
        echo $this->Form->control($field, $options);
    }

    echo $this->Form->submit(__d('croogo', 'Filter'), ['type' => 'submit', 'class' => 'btn-outline-success']);
    echo $this->Html->link('Reset', [
        'action' => 'index',
    ], [
        'class' => 'btn btn-outline-secondary',
    ]);

    echo $this->Form->end();
endif;
