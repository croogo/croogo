<?php

use \Cake\Utility\Text;

$metaCollection = collection($entity->meta);

foreach ($customFields as $customField => $customFieldOptions) {
    $selector = [
        'key' => $customField,
    ];
    $meta = $metaCollection->firstMatch($selector);

    if ($meta) {
        $id = $meta->id;
        $value = $meta->value;
        foreach ($entity->meta as $i => $tmp) {
            if ($tmp->key == $customField) {
                unset($entity->meta[$i]);
                break;
            }
        }
    } else {
        $id = Text::uuid();
        $value = null;
    }

    $options = array_merge(['tab' => true], $customFieldOptions);
    echo $this->Meta->field($customField, $value, $id, $options);
}
