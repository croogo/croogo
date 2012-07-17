<?php
if (isset($this->request->query['urls'])) {
	foreach ($permissions as $acoId => &$aco) {
		$aco[key($aco)]['url'] = array(
			'edit' => $this->Html->link(__('Edit'), array(
				'controller' => 'acl_actions',
				'action' => 'edit', $acoId,
				)),
			'del' => $this->Form->postLink(__('Delete'), array(
				'controller' => 'acl_actions',
				'action' => 'delete', $acoId,
				), null, __('Are you sure?')),
			'up' => $this->Html->link(__('Move up'), array(
				'controller' => 'acl_actions',
				'action' => 'move', $acoId, 'up',
				)),
			'down' => $this->Html->link(__('Move down'), array(
				'controller' => 'acl_actions',
				'action' => 'move', $acoId, 'down',
				)),
			);
	}
}
echo json_encode(compact('aros', 'permissions', 'level'));
