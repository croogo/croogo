<?php
if (count($taxonomy) > 0):
	$taxonomyIds = Hash::extract($this->data, 'Taxonomy.{n}.id');
	foreach ($taxonomy as $vocabularyId => $taxonomyTree):
		echo $this->Form->input('TaxonomyData.' . $vocabularyId, array(
			'label' => $vocabularies[$vocabularyId]['title'],
			'type' => 'select',
			'multiple' => $vocabularies[$vocabularyId]['multiple'],
			'options' => $taxonomyTree,
			'value' => $taxonomyIds,
			'class' => false,
			));
	endforeach;
endif;
