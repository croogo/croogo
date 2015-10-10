<?php

namespace Croogo\Taxonomy\Model\Table;

use Croogo\Core\Model\Table\CroogoTable;

class TypesTable extends CroogoTable {

/**
 * Display fields for this model
 *
 * @var array
 */
	protected $_displayFields = [
		'id',
		'title',
		'alias',
		'description',
		'plugin',
	];

	public function initialize(array $config) {
		$this->belongsToMany('Croogo/Taxonomy.Vocabularies', [
			'joinTable' => 'types_vocabularies',
		]);
	}

/**
 * Get a list of relevant types for given plugin
 */
	public function pluginTypes($plugin = null) {
		if ($plugin === null) {
			$conditions = [];
		} elseif ($plugin) {
			$conditions = ['plugin' => $plugin];
		} else {
			$conditions = [
				'OR' => [
					'plugin LIKE' => '',
					'plugin' => null,
				],
			];
		}
		return $this->find('list', compact('conditions'));
	}

}

