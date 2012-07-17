<?php
if (count($taxonomy) > 0):
	$taxonomyIds = Set::extract('/Taxonomy/id', $this->data);
	foreach ($taxonomy AS $vocabularyId => $taxonomyTree):
		echo $this->Form->input('TaxonomyData.'.$vocabularyId, array(
			'label' => $vocabularies[$vocabularyId]['title'],
			'type' => 'select',
			'multiple' => true,
			'options' => $taxonomyTree,
			'value' => $taxonomyIds,
			));
	endforeach;
endif;
