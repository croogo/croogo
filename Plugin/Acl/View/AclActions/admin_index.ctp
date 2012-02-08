<?php
    $this->Html->script('/acl/js/acl_permissions.js', false);
    $this->Html->scriptBlock("$(document).ready(function(){ AclPermissions.documentReady(); });", array('inline' => false));
?>
<div class="acos index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('New Action'), array('action'=>'add')); ?></li>
            <li><?php echo $this->Html->link(__('Generate Actions'), array('action'=>'generate')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $this->Html->tableHeaders(array(
            __('Id'),
            __('Alias'),
            __('Actions'),
        ));
        echo $tableHeaders;

        $c = 0;
        foreach ($acos AS $id => $aco) {
            $class = '';
            $alias = $aco[0];
            $level = substr_count($alias, '-');

            switch ($aco['type']) {
            case 'action':
                $class .= 'level-'.$level;
                $actionClass = 'hidden controller-'.$aco['controller'];
                $oddOptions = array('class' => $actionClass);
                $evenOptions = array('class' => $actionClass);
                $alias = substr_replace($alias, '', 0, $level);
            break;
            case 'plugin':
                $class = 'plugin expand';
                $oddOptions = array();
                $evenOptions = array();
                $trClass = false;
            break;
            case 'controller':
                if ($aco['plugin']) {
                    $class .= ' controller expand';
                    $trClass = ' hidden plugin-controller plugin-' . $aco['plugin'];
                    $class .= ' plugin level-' . $level;
                } else {
                    $class .= ' controller collapse';
                    $trClass = false;
                }
                $oddOptions = array();
                $evenOptions = array();
                $alias = substr_replace($alias, '', 0, $level);
            break;
            }

            if ($aco['type'] == 'action') {
                $actions  = $this->Html->link(__('Edit'), array('action' => 'edit', $id));
                $actions .= ' ' . $this->Html->link(__('Delete'), array(
                    'action' => 'delete',
                    $id,
                    'token' => $this->params['_Token']['key'],
                ), null, __('Are you sure?', true));
                $actions .= ' ' . $this->Html->link(__('Move up'), array('action' => 'move', $id, 'up'));
                $actions .= ' ' . $this->Html->link(__('Move down'), array('action' => 'move', $id, 'down'));
            } else {
                $actions = '';
            }

            $row = array(
                $id,
                $this->Html->div($class, $alias),
                $actions,
            );

            $line = '';
            foreach ($row as $cell) {
                $tdOptions = ($c % 2 == 0) ? $evenOptions: $oddOptions;
                $line .= $this->Html->tag('td', $cell, $tdOptions);
            }
            $trOptions = empty($trClass) ? array() : array('class' => $trClass);
            echo $this->Html->tag('tr', $line, $trOptions);
            $c++;
        }
        echo $tableHeaders;
    ?>
    </table>
</div>