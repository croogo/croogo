<?php
if (count($taxonomies) > 0):
    foreach ($taxonomies as $vocabularyId => $taxonomyTree):

        $hasEmpty = !$vocabularies[$vocabularyId]->multiple;
        echo $this->Form->input('taxonomies._ids', [
            'label' => $vocabularies[$vocabularyId]->title,
            'type' => 'select',
            'multiple' => $vocabularies[$vocabularyId]->multiple,
            'options' => $taxonomyTree,
            'empty' => $hasEmpty,
        ]);
    endforeach;
endif;
