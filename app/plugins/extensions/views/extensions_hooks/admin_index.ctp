<?php
    echo $javascript->link('/extensions/js/extensions_hooks.js');
?>
<div class="extensions-hooks">
    <h2><?php echo $this->pageTitle; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('Upload', true), array('controller' => 'extensions_plugins', 'action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $hookComponents = explode(',', Configure::read('Hook.components'));
        $hookHelpers = explode(',', Configure::read('Hook.helpers'));
        $siteHooks = array_merge($hookComponents, $hookHelpers);

        $tableHeaders =  $html->tableHeaders(array(
            '',
            __('Hook', true),
            __('Hook type', true),
            __('Status', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($hooks AS $hook) {
            if (strstr(Inflector::underscore($hook), '_helper')) {
                $hookType = 'Helper';
                $hookTitle = str_replace($hookType, '', $hook);
            } else {
                $hookType = 'Component';
                $hookTitle = str_replace($hookType, '', $hook);
            }

            if (array_search($hookTitle, $siteHooks)) {
                $status = 1;
                $icon = 'tick.png';
            } else {
                $status = 0;
                $icon = 'cross.png';
            }

            $rows[] = array(
                '',
                $hookTitle,
                $hookType,
                //$html->tag('span', $layout->status($status), array('class' => 'hook-toggle', 'rel' => $hook)),
                $html->image('icons/'.$icon, array('class' => 'hook-toggle', 'rel' => $hook)),
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>