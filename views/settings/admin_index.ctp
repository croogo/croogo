<div class="settings index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('New Setting', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $this->Html->tableHeaders(array(
            $paginator->sort('id'),
            $paginator->sort('key'),
            $paginator->sort('value'),
            $paginator->sort('editable'),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($settings AS $setting) {
            $actions  = $this->Html->link(__('Move up', true), array('controller' => 'settings', 'action' => 'moveup', $setting['Setting']['id']));
            $actions .= ' ' . $this->Html->link(__('Move down', true), array('controller' => 'settings', 'action' => 'movedown', $setting['Setting']['id']));
            $actions .= ' ' . $this->Html->link(__('Edit', true), array('controller' => 'settings', 'action' => 'edit', $setting['Setting']['id']));
            $actions .= ' ' . $this->Layout->adminRowActions($setting['Setting']['id']);
            $actions .= ' ' . $this->Html->link(__('Delete', true), array(
                'controller' => 'settings',
                'action' => 'delete',
                $setting['Setting']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $key = $setting['Setting']['key'];
            $keyE = explode('.', $key);
            $keyPrefix = $keyE['0'];
            if (isset($keyE['1'])) {
                $keyTitle = '.' . $keyE['1'];
            } else {
                $keyTitle = '';
            }

            $rows[] = array(
                $setting['Setting']['id'],
                $this->Html->link($keyPrefix, array('controller' => 'settings', 'action' => 'index', 'p' => $keyPrefix)) . $keyTitle,
                $this->Text->truncate($setting['Setting']['value'], 20),
                $setting['Setting']['editable'],
                $actions,
            );
        }

        echo $this->Html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>
