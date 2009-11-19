<div class="extensions-locales">
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
            __('Locale', true),
            __('Default', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($locales AS $locale) {
            $actions  = '';
            $actions .= $html->link(__('Activate', true), array('action' => 'activate', $locale));
            $actions .= ' ' . $html->link(__('Edit', true), array('action' => 'edit', $locale));
            $actions .= ' ' . $html->link(__('Delete', true), array('action' => 'delete', $locale), null, __('Are you sure?', true));

            if ($locale == Configure::read('Site.locale')) {
                $status = $layout->status(1);
            } else {
                $status = $layout->status(0);
            }

            $rows[] = array(
                '',
                $locale,
                $status,
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>