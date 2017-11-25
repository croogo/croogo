<?php

namespace Croogo\Suppliers\Config;

use Croogo\Core\Croogo;

Croogo::hookBehavior('Shops.Orders', 'Suppliers.SuppliersOrderMonitor');
