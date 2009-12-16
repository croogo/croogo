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
        $tableHeaders =  $html->tableHeaders(array(
            '',
            __('Hook', true),
            __('Hook type', true),
            __('Status', true),
            __('Actions', true),
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

            $filePath = APP;
            if (strstr($hook, '.')) {
                $pluginHook = explode('.', $hook);
                $plugin = $pluginHook['0'];
                $filePath .= 'plugins'.DS.Inflector::underscore($plugin).DS;
                $hookTitleE = explode('.', $hookTitle);
                $hookFile = Inflector::underscore($hookTitleE['1']).'.php';
            } else {
                $hookFile = Inflector::underscore($hookTitle).'.php';
            }
            if ($hookType == 'Component') {
                $filePath .= 'controllers'.DS.'components'.DS.$hookFile;
            } else {
                $filePath .= 'views'.DS.'helpers'.DS.$hookFile;
            }

            if (array_search($hook, $siteHooks) !== false) {
                $icon = 'tick.png';
            } else {
                $icon = 'cross.png';
            }

            $rows[] = array(
                '',
                $hookTitle,
                $hookType,
                $html->image('icons/'.$icon, array('class' => 'hook-toggle', 'rel' => $hook)),
                $html->link(__('Edit', true), '/admin/filemanager/editfile?path='.urlencode($filePath)),
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>