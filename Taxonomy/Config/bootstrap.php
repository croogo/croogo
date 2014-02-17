<?php

$cacheConfig = array_merge(
	Configure::read('Cache.defaultConfig'),
	array('groups' => array('taxonomy'))
);
CroogoCache::config('croogo_types', $cacheConfig);
CroogoCache::config('croogo_vocabularies', $cacheConfig);

Croogo::hookComponent('*', 'Taxonomy.Taxonomies');

Croogo::hookHelper('*', 'Taxonomy.Taxonomies');

Croogo::mergeConfig('Translate.models.Term', array(
	'fields' => array(
		'title' => 'titleTranslation',
		'description' => 'descriptionTranslation',
	),
	'translateModel' => 'Taxonomy.Term',
));
