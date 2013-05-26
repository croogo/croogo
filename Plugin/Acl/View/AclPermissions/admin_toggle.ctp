<?php

if ($success == 1) {
	if ($permitted == 1) {
		echo $this->Html->tag('i', null, array(
			'class' => 'permission-toggle icon-ok green',
			'data-aco_id' => $acoId,
			'data-aro_id' => $aroId
		));
	} else {
		echo $this->Html->tag('i', null, array(
			'class' => 'permission-toggle icon-remove red',
			'data-aco_id' => $acoId,
			'data-aro_id' => $aroId
		));
	}
} else {
	echo __d('croogo', 'error');
}

Configure::write('debug', 0);
?>