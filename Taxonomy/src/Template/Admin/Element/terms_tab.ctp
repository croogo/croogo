<?php

use Cake\Utility\Hash;

if (count($taxonomies) > 0) :
    $taxonomyIds = Hash::extract((array)$entity->taxonomies, '{n}.id');

    foreach ($taxonomies as $vocabularyId => $taxonomyTree) :
        $error = implode('', $entity->getErrors('taxonomy_data.' . $vocabularyId));
        $templates = [];
        if ($error) {
            $allTemplates = $this->Form->templates();
            $templates['inputContainer'] = $allTemplates['inputContainerError'];
        }
        $currVocabulary = $vocabularies[$vocabularyId];
        $hasEmpty = !$currVocabulary->multiple;
        $inputOptions = [
            'label' => $currVocabulary->title,
            'type' => 'select',
            'multiple' => $currVocabulary->multiple,
            'options' => $taxonomyTree,
            'empty' => $hasEmpty ? '-- Please choose --' : false,
            'value' => $taxonomyIds,
            'help' => $error,
            'templates' => $templates
        ];
        if ($currVocabulary->tags === true):
            $inputOptions += [
                'data-tags' => true,
                'data-token-separators' => json_encode([
                    ',',
                    ' ',
                ]),
            ];
        endif;
        if ($currVocabulary->required === true):
            $inputOptions += [
                'required' => true,
            ];
        endif;
        echo $this->Form->input('taxonomy_data.' . $vocabularyId, $inputOptions);
    endforeach;
endif;
