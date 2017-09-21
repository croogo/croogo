<?php
    $options = \Cake\Utility\Hash::merge([
        'tagAttributes' => [
            'id' => 'menu-' . $menu['id'],
        ],
    ], $options);
    echo $this->Menus->nestedLinks($menu['threaded'], $options);
?>