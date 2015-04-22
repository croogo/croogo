<?php

namespace Croogo\Taxonomy\Model\Table;

use Croogo\Croogo\Model\Table\CroogoTable;

class TypesTable extends CroogoTable {

	public function initialize(array $config) {
		$this->belongsToMany('Croogo/Taxonomy.Vocabularies', [
			'joinTable' => 'types_vocabularies',
		]);
	}

}

