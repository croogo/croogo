<?php

use Croogo\Core\Croogo;

Croogo::hookComponent('*', ['Croogo/Meta.Meta' => ['priority' => 8]]);

Croogo::hookHelper('*', 'Croogo/Meta.Meta');

\Cake\Utility\Inflector::rules('uninflected', ['meta']);
