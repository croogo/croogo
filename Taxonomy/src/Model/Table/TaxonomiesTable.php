<?php

namespace Croogo\Taxonomy\Model\Table;

use Croogo\Croogo\Model\Table\CroogoTable;

class TaxonomiesTable extends CroogoTable {

	public function initialize(array $config) {
		$this->belongsTo('Croogo/Taxonomy.Terms');
		$this->belongsTo('Croogo/Taxonomy.Vocabularies');
	}

}

