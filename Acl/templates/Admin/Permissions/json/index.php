<?php

$this->loadHelper('Croogo/Core.Croogo');
if ($this->getRequest()->getQuery('urls')) {
    foreach ($permissions as $acoId => &$aco) {
        $aco[key($aco)]['url'] = array(
            'up' => $this->Html->link('',
                array('controller' => 'Actions', 'action' => 'moveup', $acoId, 'up'),
                [
                    'icon' => $this->Theme->getIcon('move-up'),
                    'escapeTitle' => false,
                    'tooltip' => __d('croogo', 'Move up')
                ]
            ),
            'down' => $this->Html->link('',
                array('controller' => 'Actions', 'action' => 'movedown', $acoId, 'down'),
                [
                    'icon' => $this->Theme->getIcon('move-down'),
                    'escapeTitle' => false,
                    'tooltip' => __d('croogo', 'Move down')
                ]
            ),
            'edit' => $this->Html->link('',
                array('controller' => 'Actions', 'action' => 'edit', $acoId),
                [
                    'icon' => $this->Theme->getIcon('update'),
                    'escapeTitle' => false,
                    'tooltip' => __d('croogo', 'Edit this item')
                ]
            ),
            'del' => $this->Croogo->adminRowAction('',
                array('controller' => 'Actions', 'action' => 'delete', $acoId),
                [
                    'icon' => $this->Theme->getIcon('delete'),
                    'escapeTitle' => false,
                    'tooltip' => __d('croogo', 'Remove this item')
                ],
                __d('croogo', 'Are you sure?')
            ),
        );
    }
}
echo json_encode(compact('aros', 'permissions', 'level'));
