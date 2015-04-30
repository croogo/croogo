<?php

namespace Croogo\Taxonomy\Model\Table;

use Croogo\Croogo\Model\Table\CroogoTable;

class TaxonomiesTable extends CroogoTable {

	public function initialize(array $config) {
		$this->belongsTo('Croogo/Taxonomy.Terms');
		$this->belongsTo('Croogo/Taxonomy.Vocabularies');
		$this->addBehavior('Tree');
//		$this->addBehavior('Croogo/Croogo.Cached', [
//			'groups' => [
//				'nodes',
//				'taxonomy',
//			],
//		]);
	}

	/**
	 * Generates a tree of terms for a vocabulary
	 *
	 * @param  string $alias   Vocabulary alias (e.g., categories)
	 * @param  array  $options
	 * @return array
	 */
	public function getTree($alias, $options = array()) {
		$_options = array(
			'key' => 'slug', // Term.slug
			'value' => 'title', // Term.title
			'taxonomyId' => false,
			'cache' => false,
		);
		$options = array_merge($_options, $options);

		// Check if cached
		if ($this->useCache && isset($options['cache']['config'])) {
			if (isset($options['cache']['prefix'])) {
				$cacheName = $options['cache']['prefix'] . '_' . md5($alias . serialize($options));
			} elseif (isset($options['cache']['name'])) {
				$cacheName = $options['cache']['name'];
			}

			if (isset($cacheName)) {
				$cacheName .= '_' . Configure::read('Config.language');
				$cachedResult = Cache::read($cacheName, $options['cache']['config']);
				if ($cachedResult) {
					return $cachedResult;
				}
			}
		}

		$vocabulary = $this->Vocabularies->findByAlias($alias)->first();
		if (!isset($vocabulary->id)) {
			return false;
		}

		$this->behaviors()->get('Tree')->config([
			'scope' => [
				$this->alias() . '.vocabulary_id' => $vocabulary->id,
			]
		]);
		$treeConditions = array(
			$this->alias() . '.vocabulary_id' => $vocabulary->id,
		);
		$tree = $this->find('treeList', [
			'keyPath' => 'term_id',
			'valuePath' => 'id'
		])->where($treeConditions)->toArray();
		$termsIds = array_keys($tree);
		$terms = $this->Terms->find('list', [
			'keyField' => $options['key'],
			'valueField' => $options['value'],
			'groupField' => 'id',
		])->where(['id IN' => $termsIds])->toArray();

		$termsTree = array();
		foreach ($tree as $termId => $tvId) {
			if (isset($terms[$termId])) {
				$term = $terms[$termId];
				$key = array_keys($term);
				$key = $key['0'];
				$value = $term[$key];
				if (strstr($tvId, '_')) {
					$tvIdN = str_replace('_', '', $tvId);
					$tvIdE = explode($tvIdN, $tvId);
					$value = $tvIdE['0'] . $value;
				}

				if (!$options['taxonomyId']) {
					$termsTree[$key] = $value;
				} else {
					$termsTree[str_replace('_', '', $tvId)] = $value;
				}
			}
		}

		// Write cache
		if (isset($cacheName)) {
			Cache::write($cacheName, $termsTree, $options['cache']['config']);
		}

		return $termsTree;
	}

}

