<div class="messages index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('Unread', true), array('action'=>'index', 'filter' => 'status:0;')); ?></li>
            <li><?php echo $html->link(__('Read', true), array('action'=>'index', 'filter' => 'status:1;')); ?></li>
        </ul>
    </div>

    <?php
    	if (isset($this->params['named'])) {
            foreach ($this->params['named'] AS $nn => $nv) {
                $paginator->options['url'][] = $nn . ':' . $nv;
            }
        }
    ?>

    <?php echo $form->create('Message', array('url' => array('controller' => 'messages', 'action' => 'process'))); ?>
    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            '',
            $paginator->sort('id'),
            $paginator->sort('contact_id'),
            $paginator->sort('name'),
            $paginator->sort('email'),
            $paginator->sort('title'),
            '',
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($messages AS $message) {
            $actions  = $html->link(__('Edit', true), array('action' => 'edit', $message['Message']['id']));
            $actions .= ' ' . $layout->adminRowActions($message['Message']['id']);
            $actions .= ' ' . $html->link(__('Delete', true), array(
                'action' => 'delete',
                $message['Message']['id'],
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $form->checkbox('Message.'.$message['Message']['id'].'.id'),
                $message['Message']['id'],
                $message['Contact']['title'],
                $message['Message']['name'],
                $message['Message']['email'],
                $message['Message']['title'],
                $html->image('/img/icons/comment.png'),
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
    <div class="bulk-actions">
    <?php
        echo $form->input('Message.action', array(
            'label' => false,
            'options' => array(
                'read' => __('Mark as read', true),
                'unread' => __('Mark as unread', true),
                'delete' => __('Delete', true),
            ),
            'empty' => true,
        ));
        echo $form->end(__('Submit', true));
    ?>
    </div>
</div>

<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>
