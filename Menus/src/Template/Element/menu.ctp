<?php

use Cake\Utility\Hash;

$options = Hash::merge([
        'tagAttributes' => [
            'id' => 'menu-' . $menu['id'],
        ],
    ], $options);
    echo $this->Menus->nestedLinks($menu['threaded'], $options);
