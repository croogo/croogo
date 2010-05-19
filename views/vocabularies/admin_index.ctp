<div class="vocabularies index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('New Vocabulary', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            $paginator->sort('id'),
            $paginator->sort('title'),
            $paginator->sort('alias'),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($vocabularies AS $vocabulary) {
            $actions  = $html->link(__('View terms', true), array('controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id']));
            $actions .= ' ' . $html->link(__('Edit', true), array('action' => 'edit', $vocabulary['Vocabulary']['id']));
            $actions .= ' ' . $html->link(__('Move up', true), array('action' => 'moveup', $vocabulary['Vocabulary']['id']));
            $actions .= ' ' . $html->link(__('Move down', true), array('action' => 'movedown', $vocabulary['Vocabulary']['id']));
            $actions .= ' ' . $layout->adminRowActions($vocabulary['Vocabulary']['id']);
            $actions .= ' ' . $html->link(__('Delete', true), array(
                'controller' => 'vocabularies',
                'action' => 'delete',
                $vocabulary['Vocabulary']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $vocabulary['Vocabulary']['id'],
                $html->link($vocabulary['Vocabulary']['title'], array('controller' => 'terms', 'action' => 'index', $vocabulary['Vocabulary']['id'])),
                $vocabulary['Vocabulary']['alias'],
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>
