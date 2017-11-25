<?php
use Cake\Utility\Hash;

if (count($taxonomies) > 0):
    $taxonomyIds = Hash::extract((array)$entity->taxonomies, '{n}.id');

    foreach ($taxonomies as $vocabularyId => $taxonomyTree):
        $error = implode('', $entity->errors('taxonomy_data.' . $vocabularyId));
        $templates = [];
        if ($error) {
            $allTemplates = $this->Form->templates();
            $templates['inputContainer'] = $allTemplates['inputContainerError'];
        }
        $hasEmpty = !$vocabularies[$vocabularyId]->multiple;
        echo $this->Form->input('taxonomy_data.' . $vocabularyId, [
            'label' => $vocabularies[$vocabularyId]->title,
            'type' => 'select',
            'multiple' => $vocabularies[$vocabularyId]->multiple,
            'options' => $taxonomyTree,
            'empty' => $hasEmpty,
            'value' => $taxonomyIds,
            'help' => $error,
            'templates' => $templates
        ]);
    endforeach;
endif;
