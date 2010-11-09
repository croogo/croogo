<div class="languages index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('New Language', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $this->Html->tableHeaders(array(
            $paginator->sort('id'),
            $paginator->sort('title'),
            $paginator->sort('native'),
            $paginator->sort('alias'),
            $paginator->sort('status'),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($languages AS $language) {
            $actions  = $this->Html->link(__('Move up', true), array('action' => 'moveup', $language['Language']['id']));
            $actions .= ' ' . $this->Html->link(__('Move down', true), array('action' => 'movedown', $language['Language']['id']));
            $actions .= ' ' . $this->Html->link(__('Edit', true), array('action' => 'edit', $language['Language']['id']));
            $actions .= ' ' . $this->Layout->adminRowActions($language['Language']['id']);
            $actions .= ' ' . $this->Html->link(__('Delete', true), array(
                'action' => 'delete',
                $language['Language']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $language['Language']['id'],
                $language['Language']['title'],
                $language['Language']['native'],
                $language['Language']['alias'],
                $this->Layout->status($language['Language']['status']),
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
