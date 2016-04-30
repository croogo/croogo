<?php

namespace Shops\Event;

use Cake\Event\EventListenerInterface;

class ShopsNodesEventHandler implements EventListenerInterface
{

    public function implementedEvents()
    {
        return [
            'Controller.Nodes.afterAdd' => [
                'callable' => 'catchAll',
                ],
            'Controller.Nodes.afterDelete' => [
                'callable' => 'catchAll',
                ],
            'Controller.Nodes.afterEdit' => [
                'callable' => 'catchAll',
                ],
            'Controller.Nodes.afterPromote' => [
                'callable' => 'catchAll',
                ],
            'Controller.Nodes.afterPublish' => [
                'callable' => 'catchAll',
                ],
            'Controller.Nodes.afterUnpromote' => [
                'callable' => 'catchAll',
                ],
            'Controller.Nodes.afterUnpublish' => [
                'callable' => 'catchAll',
                ],
            ];
    }

    public function catchAll($event)
    {
        return true;
    }
}
