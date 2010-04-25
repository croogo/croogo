<div class="extensions-plugins">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('Upload', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            '',
            __('Alias', true),
            __('Name', true),
            __('Description', true),
            __('Hooks Active', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($plugins AS $pluginAlias => $pluginData) {
            if (in_array($pluginAlias, $corePlugins)) continue;

            $actions  = '';
            $actions .= ' ' . $html->link(__('Delete', true), array(
                'action' => 'delete',
                $pluginAlias,
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                '',
                $pluginAlias,
                $pluginData['name'],
                $pluginData['description'],
                $layout->status($pluginData['active']),
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>