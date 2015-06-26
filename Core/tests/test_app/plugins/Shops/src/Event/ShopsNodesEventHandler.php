<?php

namespace Shops\Event;

use Cake\Event\EventListenerInterface;

class ShopsNodesEventHandler implements EventListenerInterface {

	public function implementedEvents() {
		return array(
			'Controller.Nodes.afterAdd' => array(
				'callable' => 'catchAll',
				),
			'Controller.Nodes.afterDelete' => array(
				'callable' => 'catchAll',
				),
			'Controller.Nodes.afterEdit' => array(
				'callable' => 'catchAll',
				),
			'Controller.Nodes.afterPromote' => array(
				'callable' => 'catchAll',
				),
			'Controller.Nodes.afterPublish' => array(
				'callable' => 'catchAll',
				),
			'Controller.Nodes.afterUnpromote' => array(
				'callable' => 'catchAll',
				),
			'Controller.Nodes.afterUnpublish' => array(
				'callable' => 'catchAll',
				),
			);
	}

	public function catchAll($event) {
		return true;
	}

}
