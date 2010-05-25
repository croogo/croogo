<div class="links index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('New Link', true), array('action'=>'add', $menu['Menu']['id'])); ?></li>
        </ul>
    </div>

    <?php
    	if (isset($this->params['named'])) {
            foreach ($this->params['named'] AS $nn => $nv) {
                $paginator->options['url'][] = $nn . ':' . $nv;
            }
        }

        echo $form->create('Link', array(
            'url' => array(
                'action' => 'process',
                $menu['Menu']['id'],
            ),
        ));
    ?>
    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            '',
            __('Id', true),
            __('Title', true),
            __('Status', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($linksTree AS $linkId => $linkTitle) {
            $actions  = $html->link(__('Move up', true), array('controller' => 'links', 'action' => 'moveup', $linkId));
            $actions .= ' ' . $html->link(__('Move down', true), array('controller' => 'links', 'action' => 'movedown', $linkId));
            $actions .= ' ' . $html->link(__('Edit', true), array('controller' => 'links', 'action' => 'edit', $linkId));
            $actions .= ' ' . $layout->adminRowActions($linkId);
            $actions .= ' ' . $html->link(__('Delete', true), array(
                'controller' => 'links',
                'action' => 'delete',
                $linkId,
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $form->checkbox('Link.'.$linkId.'.id'),
                $linkId,
                $linkTitle,
                $layout->status($linksStatus[$linkId]),
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
    <div class="bulk-actions">
    <?php
        echo $form->input('Link.action', array(
            'label' => false,
            'options' => array(
                'publish' => __('Publish', true),
                'unpublish' => __('Unpublish', true),
                'delete' => __('Delete', true),
            ),
            'empty' => true,
        ));
        echo $form->end(__('Submit', true));
    ?>
    </div>
</div>