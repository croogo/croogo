<div class="extensions-plugins">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('Upload', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $this->Html->tableHeaders(array(
            '',
            __('Alias', true),
            __('Name', true),
            __('Description', true),
            __('Active', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($plugins AS $pluginAlias => $pluginData) {
            if (in_array($pluginAlias, $corePlugins)) {
                continue;
            }

            if ($pluginData['active']) {
                $icon = 'tick.png';
                $toggleText = __('Deactivate', true);
            } else {
                $icon = 'cross.png';
                $toggleText = __('Activate', true);
            }
            $iconImage = $this->Html->image('icons/'.$icon);

            $actions  = '';
            $actions .= ' ' . $this->Html->link($toggleText, array(
                'action' => 'toggle',
                $pluginAlias,
                'token' => $this->params['_Token']['key'],
            ));
            $actions .= ' ' . $this->Html->link(__('Delete', true), array(
                'action' => 'delete',
                $pluginAlias,
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                '',
                $pluginAlias,
                $pluginData['name'],
                $pluginData['description'],
                $this->Html->link($iconImage, array(
                    'action' => 'toggle',
                    $pluginAlias,
                    'token' => $this->params['_Token']['key'],
                ), array(
                    'escape' => false,
                )),
                $actions,
            );
        }

        echo $this->Html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>