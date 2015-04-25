<?php

namespace Croogo\Taxonomy\Model\Table;

use Croogo\Croogo\Model\Table\CroogoTable;

class TypesTable extends CroogoTable {

/**
 * Display fields for this model
 *
 * @var array
 */
	protected $_displayFields = array(
		'id',
		'title',
		'alias',
		'description',
		'plugin',
	);

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
			$conditions = array();
		} elseif ($plugin) {
			$conditions = array('plugin' => $plugin);
		} else {
			$conditions = array(
				'OR' => array(
					'plugin LIKE' => '',
					'plugin' => null,
				),
			);
		}
		return $this->find('list', compact('conditions'));
	}

}

