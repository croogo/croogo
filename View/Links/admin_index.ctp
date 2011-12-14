<div class="links index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('New Link'), array('action'=>'add', $menu['Menu']['id'])); ?></li>
        </ul>
    </div>

    <?php
    	if (isset($this->params['named'])) {
            foreach ($this->params['named'] AS $nn => $nv) {
                $this->Paginator->options['url'][] = $nn . ':' . $nv;
            }
        }

        echo $this->Form->create('Link', array(
            'url' => array(
                'action' => 'process',
                $menu['Menu']['id'],
            ),
        ));
    ?>
    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $this->Html->tableHeaders(array(
            '',
            __('Id'),
            __('Title'),
            __('Status'),
            __('Actions'),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($linksTree AS $linkId => $linkTitle) {
            $actions  = $this->Html->link(__('Move up'), array('controller' => 'links', 'action' => 'moveup', $linkId));
            $actions .= ' ' . $this->Html->link(__('Move down'), array('controller' => 'links', 'action' => 'movedown', $linkId));
            $actions .= ' ' . $this->Html->link(__('Edit'), array('controller' => 'links', 'action' => 'edit', $linkId));
            $actions .= ' ' . $this->Layout->adminRowActions($linkId);
            $actions .= ' ' . $this->Html->link(__('Delete'), array(
                'controller' => 'links',
                'action' => 'delete',
                $linkId,
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?'));

            $rows[] = array(
                $this->Form->checkbox('Link.'.$linkId.'.id'),
                $linkId,
                $linkTitle,
                $this->Layout->status($linksStatus[$linkId]),
                $actions,
            );
        }

        echo $this->Html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
    <div class="bulk-actions">
    <?php
        echo $this->Form->input('Link.action', array(
            'label' => false,
            'options' => array(
                'publish' => __('Publish'),
                'unpublish' => __('Unpublish'),
                'delete' => __('Delete'),
            ),
            'empty' => true,
        ));
        echo $this->Form->end(__('Submit'));
    ?>
    </div>
</div>