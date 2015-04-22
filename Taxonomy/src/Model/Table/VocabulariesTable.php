<?php

namespace Croogo\Taxonomy\Model\Table;

use Croogo\Croogo\Model\Table\CroogoTable;

class VocabulariesTable extends CroogoTable {

	public function initialize(array $config) {
		$this->belongsToMany('Croogo/Taxonomy.Types', [
			'joinTable' => 'types_vocabularies',
		]);
	}

}

