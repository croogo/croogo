<?php

use Croogo\Core\Croogo;

Croogo::hookComponent('Nodes', array('Meta.Meta' => array('priority' => 8)));

Croogo::hookHelper('*', 'Meta.Meta');
