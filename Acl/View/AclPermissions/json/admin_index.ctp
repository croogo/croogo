<?php
if (isset($this->request->query['urls'])) {
	foreach ($permissions as $acoId => &$aco) {
		$aco[key($aco)]['url'] = array(
			'up' => $this->Html->link('',
				array('controller' => 'acl_actions', 'action' => 'moveup', $acoId, 'up'),
				array('icon' => $this->Theme->icon('move-up'), 'tooltip' => __d('croogo', 'Move up'))
			),
			'down' => $this->Html->link('',
				array('controller' => 'acl_actions', 'action' => 'movedown', $acoId, 'down'),
				array('icon' => $this->Theme->icon('move-down'), 'tooltip' => __d('croogo', 'Move down'))
			),
			'edit' => $this->Html->link('',
				array('controller' => 'acl_actions', 'action' => 'edit', $acoId),
				array('icon' => $this->Theme->icon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
			),
			'del' => $this->Form->postLink('',
				array('controller' => 'acl_actions', 'action' => 'delete', $acoId),
				array('icon' => $this->Theme->icon('delete'), 'tooltip' => __d('croogo', 'Remove this item'), 'escapeTitle' => false, 'escape' => true, 'class' => 'red'),
				__d('croogo', 'Are you sure?')
			),
		);
	}
}
echo json_encode(compact('aros', 'permissions', 'level'));
