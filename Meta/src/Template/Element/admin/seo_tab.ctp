<?php

use Cake\Core\Configure;
use Cake\Utility\Hash;

$keys = Configure::read('Meta.keys');
$outputKeys = $keys;
$output = [];

$lastIndex = 0;
foreach ((array)$entity->meta as $i => $meta):
    $keyOptions = [
        'label' => null,
        'type' => 'text',
        'help' => null,
    ];
    if (isset($keys[$meta->key])):
        $keyOptions = $keys[$meta->key];
    endif;

    $valueOptions = [
        'label' => $keyOptions['label'],
    ];
    if (isset($keyOptions['type'])):
        $valueOptions['type'] = $keyOptions['type'];
    endif;
    if (isset($keyOptions['help'])):
        $valueOptions['help'] = $keyOptions['help'];
    endif;
    $output[$meta->key] = $this->Meta->field($meta->key, $meta->value, $meta->id, [
        'tab' => false,
        'value' => $valueOptions,
    ]);

    unset($keys[$meta->key]);
    $lastIndex = $i;
endforeach;

$i = ++$lastIndex;
foreach ($keys as $key => $keyOptions):

    $valueOptions = [
        'label' => $keyOptions['label'],
    ];
    if (isset($keyOptions['type'])):
        $valueOptions['type'] = $keyOptions['type'];
    endif;
    if (isset($keyOptions['help'])):
        $valueOptions['help'] = $keyOptions['help'];
    endif;
    $output[$key] = $this->Meta->field($key, null, null, [
        'uuid' => $i,
        'tab' => false,
        'value' => $valueOptions,
    ]);

    $i++;
endforeach;

foreach ($outputKeys as $key => $val):
    echo $output[$key];
endforeach;
