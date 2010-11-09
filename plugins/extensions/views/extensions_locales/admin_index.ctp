<div class="extensions-locales">
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
            __('Locale', true),
            __('Default', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($locales AS $locale) {
            $actions  = '';
            $actions .= $this->Html->link(__('Activate', true), array(
                'action' => 'activate',
                $locale,
                'token' => $this->params['_Token']['key'],
            ));
            $actions .= ' ' . $this->Html->link(__('Edit', true), array('action' => 'edit', $locale));
            $actions .= ' ' . $this->Html->link(__('Delete', true), array(
                'action' => 'delete',
                $locale,
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            if ($locale == Configure::read('Site.locale')) {
                $status = $this->Layout->status(1);
            } else {
                $status = $this->Layout->status(0);
            }

            $rows[] = array(
                '',
                $locale,
                $status,
                $actions,
            );
        }

        echo $this->Html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>