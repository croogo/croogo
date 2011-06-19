<?php
    $this->Html->script('/acl/js/acl_permissions.js', false);
?>
<div class="acl_permissions index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('Generate Actions'), array('controller' => 'acl_actions', 'action'=>'generate', 'permissions' => 1)); ?></li>
            <li><?php echo $this->Html->link(__('Edit Actions'), array('controller' => 'acl_actions', 'action'=>'index', 'permissions' => 1)); ?></li>
            <li><?php echo $this->Html->link(__('Upgrade Acl for Standard Plugins'), array('controller' => 'acl_permissions', 'action'=>'upgrade')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $roleTitles = array_values($roles);
        $roleIds   = array_keys($roles);

        $tableHeaders = array(
            __('Id'),
            __('Alias'),
        );
        $tableHeaders = array_merge($tableHeaders, $roleTitles);
        $tableHeaders =  $this->Html->tableHeaders($tableHeaders);
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
            
            $row = array(
                $id,
                $this->Html->div($class, $alias),
            );

            foreach ($roles AS $roleId => $roleTitle) {
                if ($aco['type'] == 'action') {
                    if ($roleId != 1) {
                        if ($permissions[$id][$roleId] == 1) {
                            $row[] = $this->Html->image('/img/icons/tick.png', array('class' => 'permission-toggle', 'rel' => $id.'-'.$rolesAros[$roleId]));
                        } else {
                            $row[] = $this->Html->image('/img/icons/cross.png', array('class' => 'permission-toggle', 'rel' => $id.'-'.$rolesAros[$roleId]));
                        }
                    } else {
                        $row[] = $this->Html->image('/img/icons/tick_disabled.png', array('class' => 'permission-disabled'));
                    }
                } else {
                    $row[] = '';
                }
            }

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