<?php

namespace Croogo\Nodes\Model\Table;

use Croogo\Croogo\Model\Table\CroogoTable;

class NodesTable extends CroogoTable {

	public function initialize(array $config) {
		$this->addBehavior('Croogo/Croogo.Encoder');
	}

}
