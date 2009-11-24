<div class="extensions-plugins">
    <h2><?php echo $this->pageTitle; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('Upload', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            '',
            __('Plugin', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($plugins AS $plugin) {
            $actions  = '';
            $actions .= ' ' . $html->link(__('Delete', true), array('action' => 'delete', $plugin), null, __('Are you sure?', true));

            $rows[] = array(
                '',
                $plugin,
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>