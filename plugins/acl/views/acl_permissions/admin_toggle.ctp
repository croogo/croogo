<?php
    if ($success == 1) {
        if ($permitted == 1) {
            echo $html->image('/img/icons/tick.png', array('class' => 'permission-toggle', 'rel' => $acoId.'-'.$aroId));
        } else {
            echo $html->image('/img/icons/cross.png', array('class' => 'permission-toggle', 'rel' => $acoId.'-'.$aroId));
        }
    } else {
        __('error');
    }

    Configure::write('debug', 0);
?>