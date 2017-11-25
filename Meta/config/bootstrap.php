<?php

use Cake\Utility\Inflector;
use Croogo\Core\Croogo;

Croogo::hookComponent('*', ['Croogo/Meta.Meta' => ['priority' => 8]]);

Croogo::hookHelper('*', 'Croogo/Meta.Meta');

Inflector::rules('uninflected', ['meta']);
