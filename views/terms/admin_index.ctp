<div class="terms index">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('New Term', true), array('action'=>'add', 'vocabulary' => $vocabulary)); ?></li>
        </ul>
    </div>

    <?php
    	if (isset($this->params['named'])) {
            foreach ($this->params['named'] AS $nn => $nv) {
                $paginator->options['url'][] = $nn . ':' . $nv;
            }
        }
    ?>

    <?php echo $form->create('Term', array('url' => array('controller' => 'terms', 'action' => 'process', 'vocabulary' => $vocabulary))); ?>
    <table cellpadding="0" cellspacing="0">
    <?php
        $tableHeaders =  $html->tableHeaders(array(
            '',
            __('Id', true),
            __('Title', true),
            __('Actions', true),
        ));
        echo $tableHeaders;

        $rows = array();
        foreach ($termsTree AS $id => $term) {
            $actions  = $html->link(__('Move up', true), array('action' => 'moveup', $id, 'vocabulary' => $vocabulary));
            $actions .= ' ' . $html->link(__('Move down', true), array('action' => 'movedown', $id, 'vocabulary' => $vocabulary));
            $actions .= ' ' . $html->link(__('Edit', true), array('action' => 'edit', $id, 'vocabulary' => $vocabulary));
            $actions .= ' ' . $layout->adminRowActions($id);
            $actions .= ' ' . $html->link(__('Delete', true), array(
                'action' => 'delete',
                $id,
                'vocabulary' => $vocabulary,
                'token' => $this->params['_Token']['key'],
            ), null, __('Are you sure?', true));

            $rows[] = array(
                $form->checkbox('Term.'.$id.'.id'),
                $id,
                $term,
                $actions,
            );
        }

        echo $html->tableCells($rows);
        echo $tableHeaders;
    ?>
    </table>
    <div class="bulk-actions">
    <?php
        echo $form->input('Term.action', array(
            'label' => false,
            'options' => array(
                //'publish' => __('Publish', true),
                //'unpublish' => __('Unpublish', true),
                'delete' => __('Delete', true),
            ),
            'empty' => true,
        ));
        echo $form->end(__('Submit', true));
    ?>
    </div>
</div>
