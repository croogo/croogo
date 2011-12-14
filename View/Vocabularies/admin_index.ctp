<div class="vocabularies index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('New Vocabulary'), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $this->Html->tableHeaders(array(
            $this->Paginator->sort('id'),
            $this->Paginator->sort('title'),
            $this->Paginator->sort('alias'),
            __('Actions'),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($vocabularies AS $vocabulary) {
            $actions  = $this->Html->link(__('View terms'), array('controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id']));
            $actions .= ' ' . $this->Html->link(__('Edit'), array('action' => 'edit', $vocabulary['Vocabulary']['id']));
            $actions .= ' ' . $this->Html->link(__('Move up'), array('action' => 'moveup', $vocabulary['Vocabulary']['id']));
            $actions .= ' ' . $this->Html->link(__('Move down'), array('action' => 'movedown', $vocabulary['Vocabulary']['id']));
            $actions .= ' ' . $this->Layout->adminRowActions($vocabulary['Vocabulary']['id']);
            $actions .= ' ' . $this->Html->link(__('Delete'), array(
                'controller' => 'vocabularies',
                'action' => 'delete',
                $vocabulary['Vocabulary']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?'));

            $rows[] = array(
                $vocabulary['Vocabulary']['id'],
                $this->Html->link($vocabulary['Vocabulary']['title'], array('controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id'])),
                $vocabulary['Vocabulary']['alias'],
                $actions,
            );
        }

        echo $this->Html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>

<div class="paging"><?php echo $this->Paginator->numbers(); ?></div>
<div class="counter"><?php echo $this->Paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%'))); ?></div>
