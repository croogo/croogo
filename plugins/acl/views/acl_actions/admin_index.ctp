<?php
    $this->Html->script('/acl/js/acl_permissions.js', false);
    $this->Html->scriptBlock("$(document).ready(function(){ AclPermissions.documentReady(); });", array('inline' => false));
?>
<div class="acos index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('New Action', true), array('action'=>'add')); ?></li>
            <li><?php echo $this->Html->link(__('Generate Actions', true), array('action'=>'generate')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $this->Html->tableHeaders(array(
            __('Id', true),
            __('Alias', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $currentController = '';
        foreach ($acos AS $id => $alias) {
            $class = '';
            if(substr($alias, 0, 1) == '_') {
                $level = 1;
                $class .= 'level-'.$level;
                $oddOptions = array('class' => 'hidden controller-'.$currentController);
                $evenOptions = array('class' => 'hidden controller-'.$currentController);
                $alias = substr_replace($alias, '', 0, 1);
            } else {
                $level = 0;
                $class .= ' controller expand';
                $oddOptions = array();
                $evenOptions = array();
                $currentController = $alias;
            }

            $actions  = $this->Html->link(__('Edit', true), array('action' => 'edit', $id));
            $actions .= ' ' . $this->Html->link(__('Delete', true), array(
                'action' => 'delete',
                $id,
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));
            $actions .= ' ' . $this->Html->link(__('Move up', true), array('action' => 'move', $id, 'up'));
            $actions .= ' ' . $this->Html->link(__('Move down', true), array('action' => 'move', $id, 'down'));

            $row = array(
                $id,
                $this->Html->div($class, $alias),
                $actions,
            );

            echo $this->Html->tableCells(array($row), $oddOptions, $evenOptions);
        }
        echo $tableHeaders;
    ?>
    </table>
</div>