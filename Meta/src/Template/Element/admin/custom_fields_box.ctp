<div class="meta-fields">
    <?php

    use Cake\Core\Configure;
    use Cake\Utility\Hash;

    if (isset($entity) && !empty($entity->meta)) {
        $fields = Hash::combine($entity->meta, '{n}.key', '{n}.value');
        $fieldsKeyToId = Hash::combine($entity->meta, '{n}.key', '{n}.id');
    } else {
        $fields = $fieldsKeyToId = [];
    }
    if (count($fields) > 0) {
        $metaKeys = array_keys(Configure::read('Meta.keys'));
        $i = 0;
        foreach ($fields as $fieldKey => $fieldValue) {
            $id = isset($fieldsKeyToId[$fieldKey]) ? $fieldsKeyToId[$fieldKey] : $i;
            if (!in_array($fieldKey, $metaKeys)):
                echo $this->Meta->field($fieldKey, $fieldValue, $id);
            endif;
            $i++;
        }
    }
    ?>
</div>
<?php
echo $this->Html->link(
    __d('croogo', 'Add another field'),
    ['plugin' => 'Croogo/Meta', 'controller' => 'Meta', 'action' => 'addMeta'],
    ['class' => 'add-meta btn btn-secondary']
);
