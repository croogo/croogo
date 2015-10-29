<?php

use Croogo\Core\Croogo;

Croogo::hookComponent('Croogo\Nodes\Controller\Admin\NodesController', array('Croogo/Meta.Meta' => array('priority' => 8)));

Croogo::hookBehavior('Croogo/Nodes.Nodes', 'Croogo/Meta.Meta');
