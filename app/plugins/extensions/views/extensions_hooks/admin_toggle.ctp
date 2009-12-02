<?php
    $icon = 'cross.png';
    if ($status == 1) {
        $icon = 'tick.png';
    }
    echo $html->image('icons/'.$icon, array('class' => 'hook-toggle', 'rel' => $hook));
    Configure::write('debug', 0);
?>