<div class="contacts index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('New Contact', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            $paginator->sort('id'),
            $paginator->sort('title'),
            $paginator->sort('alias'),
            $paginator->sort('email'),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($contacts AS $contact) {
            $actions = '';
            //$actions .= $html->link(__('View Messages', true), array('controller'=> 'messages', 'action' => 'index', 'contact' => $contact['Contact']['id']));
            $actions .= ' ' . $html->link(__('Edit', true), array('action' => 'edit', $contact['Contact']['id']));
            $actions .= ' ' . $layout->adminRowActions($contact['Contact']['id']);
            $actions .= ' ' . $html->link(__('Delete', true), array(
                'action' => 'delete',
                $contact['Contact']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $contact['Contact']['id'],
                $html->link($contact['Contact']['title'], array(
                    'admin' => false,
                    'controller' => 'contacts',
                    'action' => 'view',
                    $contact['Contact']['alias'],
                )),
                $contact['Contact']['alias'],
                $contact['Contact']['email'],
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
