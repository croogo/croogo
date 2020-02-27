<?php

use Cake\Core\Configure;
use Cake\Utility\Hash;

$keys = Configure::read('Meta.keys');

$i = 0;
foreach ($keys as $key => $keyOptions):

    $metaId = $metaValue = null;
    foreach ((array)$entity->meta as $meta) {
        if ($meta->key == $key) {
            $metaId = $meta->id;
            $metaValue = $meta->value;
            break;
        }
    }
    $valueOptions = [
        'label' => $keyOptions['label'],
    ];
    if (isset($keyOptions['type'])):
        $valueOptions['type'] = $keyOptions['type'];
    endif;
    echo $this->Meta->field($key, $metaValue, $metaId, [
        'uuid' => $i,
        'tab' => false,
        'value' => $valueOptions,
    ]);

    $i++;
endforeach;
