<?php

use Croogo\Core\Croogo;

Croogo::hookComponent('Nodes', ['Meta.Meta' => ['priority' => 8]]);

Croogo::hookHelper('*', 'Croogo/Meta.Meta');
