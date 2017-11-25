<?php

$this->loadHelper('Croogo/Core.Croogo');
if (isset($this->request->query['urls'])) {
    foreach ($permissions as $acoId => &$aco) {
        $aco[key($aco)]['url'] = array(
            'up' => $this->Html->link('',
                array('controller' => 'Actions', 'action' => 'moveup', $acoId, 'up'),
                array('icon' => $this->Theme->getIcon('move-up'), 'tooltip' => __d('croogo', 'Move up'))
            ),
            'down' => $this->Html->link('',
                array('controller' => 'Actions', 'action' => 'movedown', $acoId, 'down'),
                array('icon' => $this->Theme->getIcon('move-down'), 'tooltip' => __d('croogo', 'Move down'))
            ),
            'edit' => $this->Html->link('',
                array('controller' => 'Actions', 'action' => 'edit', $acoId),
                array('icon' => $this->Theme->getIcon('update'), 'tooltip' => __d('croogo', 'Edit this item'))
            ),
            'del' => $this->Croogo->adminRowAction('',
                array('controller' => 'Actions', 'action' => 'delete', $acoId),
                array('icon' => $this->Theme->getIcon('delete'), 'tooltip' => __d('croogo', 'Remove this item')),
                __d('croogo', 'Are you sure?')
            ),
        );
    }
}
echo json_encode(compact('aros', 'permissions', 'level'));
