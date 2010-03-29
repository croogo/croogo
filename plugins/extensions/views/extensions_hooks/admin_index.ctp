<div class="extensions-hooks">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('Upload', true), array('controller' => 'extensions_plugins', 'action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            __('Plugin', true),
            __('Hook', true),
            __('Hook type', true),
            __('Status', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($hooks AS $plugin => $pluginHooks) {
            if (count($pluginHooks) == 0) {
                continue;
            }

            $rows[] = array(
                '<div class="plugin">'.$plugin.'</div>',
                '',
                '',
                '',
                '',
            );

            foreach ($pluginHooks AS $pluginHook) {
                if ($pluginHook['status']) {
                    $icon = 'tick.png';
                    $toggleText = __('Deactivate', true);
                } else {
                    $icon = 'cross.png';
                    $toggleText = __('Activate', true);
                }
                $iconImage = $html->image('icons/'.$icon);

                $actions  = $html->link(__('Edit', true), '/admin/filemanager/editfile?path='.urlencode($pluginHook['path']));
                $actions .= ' ' . $html->link($toggleText, array(
                    'action' => 'toggle',
                    $pluginHook['plugin'] ? $plugin.'.'.$pluginHook['name'].$pluginHook['type'] : $pluginHook['name'].$pluginHook['type'],
                    'token' => $this->params['_Token']['key'],
                ));

                $rows[] = array(
                    '',
                    $pluginHook['name'],
                    $pluginHook['type'],
                    $html->link($iconImage, array(
                        'action' => 'toggle',
                        $pluginHook['plugin'] ? $plugin.'.'.$pluginHook['name'].$pluginHook['type'] : $pluginHook['name'].$pluginHook['type'],
                        'token' => $this->params['_Token']['key'],
                    ), array(
                        'escape' => false,
                    )),
                    $actions,
                );
            }
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>