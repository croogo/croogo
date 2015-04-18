<?php

use Cake\Core\Configure;
use Croogo\Croogo\CroogoRouter;

CroogoRouter::connect('/admin', Configure::read('Croogo.dashboardUrl'));
