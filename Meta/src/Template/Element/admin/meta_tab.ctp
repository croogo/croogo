<div class="meta-fields">
    <?php
    if ($entity->has('meta')) {
        $fields = \Cake\Utility\Hash::combine($entity->meta, '{n}.key', '{n}.value');
        $fieldsKeyToId = \Cake\Utility\Hash::combine($entity->meta, '{n}.key', '{n}.id');
    } else {
        $fields = $fieldsKeyToId = [];
    }
    if (count($fields) > 0) {
        foreach ($fields as $fieldKey => $fieldValue) {
            echo $this->Meta->field($fieldKey, $fieldValue, $fieldsKeyToId[$fieldKey]);
        }
    }
    ?>
</div>
<?php
echo $this->Html->link(__d('croogo', 'Add another field'),
    ['plugin' => 'Croogo/Meta', 'controller' => 'Meta', 'action' => 'addMeta'],
    ['class' => 'add-meta btn btn-secondary']
);

