<?php
if (isset($this->request->query['urls'])) {
	foreach ($permissions as $acoId => &$aco) {
		$aco[key($aco)]['url'] = array(
			'up' => $this->CroogoHtml->link('',
				array('controller' => 'Actions', 'action' => 'moveup', $acoId, 'up'),
				array('icon' => $_icons['move-up'], 'tooltip' => __d('croogo', 'Move up'))
			),
			'down' => $this->CroogoHtml->link('',
				array('controller' => 'Actions', 'action' => 'movedown', $acoId, 'down'),
				array('icon' => $_icons['move-down'], 'tooltip' => __d('croogo', 'Move down'))
			),
			'edit' => $this->CroogoHtml->link('',
				array('controller' => 'Actions', 'action' => 'edit', $acoId),
				array('icon' => $_icons['update'], 'tooltip' => __d('croogo', 'Edit this item'))
			),
			'del' => $this->Croogo->adminRowAction('',
				array('controller' => 'Actions', 'action' => 'delete', $acoId),
				array('icon' => $_icons['delete'], 'tooltip' => __d('croogo', 'Remove this item')),
				__d('croogo', 'Are you sure?')
			),
		);
	}
}
echo json_encode(compact('aros', 'permissions', 'level'));
