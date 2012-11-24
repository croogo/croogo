<?php

if ($success == 1) {
	if ($permitted == 1) {
		echo $this->Html->image('/img/icons/tick.png', array('class' => 'permission-toggle', 'data-aco_id' => $acoId, 'data-aro_id' => $aroId));
	} else {
		echo $this->Html->image('/img/icons/cross.png', array('class' => 'permission-toggle', 'data-aco_id' => $acoId, 'data-aro_id' => $aroId));
	}
} else {
	__('error');
}

Configure::write('debug', 0);
?>