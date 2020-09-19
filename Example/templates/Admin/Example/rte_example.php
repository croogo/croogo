<?php

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

$this->extend('Croogo/Core./Common/admin_index');
$this->Breadcrumbs
    ->add('Example', ['controller' => 'Example', 'action' => 'index'])
    ->add('RTE Example', $this->getRequest()->getRequestTarget());

echo $this->Form->create('Example');

$options = ['type' => 'textarea'];
$rteConfigs = Configure::read('Wysiwyg.actions.' . base64_encode('Croogo/Example.Admin/Example/rteExample'));

$para = '<p>This editor was configured with the following setting:</p>';
foreach (['basic', 'standard', 'full', 'custom'] as $preset) :
    $query = sprintf('{n}[elements=#Example%s]', Inflector::camelize($preset));
    $presetConfig = Hash::extract($rteConfigs, $query);
    $pre = '<blockquote><pre>' . print_r($presetConfig[0], true) . '</pre></blockquote>';
    echo $this->Form->input($preset, Hash::merge([
        'id' => 'Example' . ucfirst($preset),
        'value' => $para . $pre,
    ], $options));
endforeach;

echo $this->Form->end();
