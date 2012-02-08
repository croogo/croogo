<div class="acl_actions form">
    <h2><?php echo $title_for_layout; ?></h2>

    <?php echo $this->Form->create('Aco', array('url' => array('controller' => 'acl_actions', 'action' => 'edit'))); ?>
        <fieldset>
        <?php
            foreach ($acos as $id => $aco) {
                if ($aco['type'] == 'action') {
                    unset($acos[$id]); continue;
                }
                $acos[$id] = str_replace('-', str_repeat('&nbsp;', 4), $aco[0]);
            }
            echo $this->Form->input('id');
            echo $this->Form->input('parent_id', array(
                'options' => $acos,
                'empty' => true,
                'rel' => __('Choose none if the Aco is a controller.'),
                'escape' => false,
            ));
            echo $this->Form->input('alias', array());
        ?>
        </fieldset>
    <?php echo $this->Form->end('Submit'); ?>
</div>