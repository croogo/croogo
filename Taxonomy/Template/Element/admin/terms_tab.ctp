<?php
if (count($taxonomy) > 0):
	$taxonomyIds = Hash::extract($this->data, 'Taxonomy.{n}.id');

	// extract error message from the 'virtual' field and inject it accordingly
	$path = '{' . $this->Form->defaultModel . '}[/^Taxonomy/]';
	$taxonomyErrors = Hash::expand(
		$this->Form->validationErrors[$this->Form->defaultModel]
	);
	if (isset($taxonomyErrors['TaxonomyData'])):
		foreach ($taxonomyErrors['TaxonomyData'] as $id => $message):
			unset($this->Form->validationErrors['Node']['TaxonomyData.' . $id]);
			$this->Form->validationErrors['TaxonomyData'][$id] = $message[0];
		endforeach;
	endif;

	foreach ($taxonomy as $vocabularyId => $taxonomyTree):
		// retrieve default values from POSTed data in case of errors
		if (empty($taxonomyIds) && isset($this->data['TaxonomyData'][$vocabularyId])):
			$value = $this->data['TaxonomyData'][$vocabularyId];
		else:
			$value = $taxonomyIds;
		endif;

		echo $this->Form->input('TaxonomyData.' . $vocabularyId, array(
			'label' => $vocabularies[$vocabularyId]['title'],
			'type' => 'select',
			'multiple' => $vocabularies[$vocabularyId]['multiple'],
			'options' => $taxonomyTree,
			'value' => $value,
			'class' => false,
		));
	endforeach;
endif;
