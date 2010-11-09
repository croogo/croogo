<div class="acl_actions form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $this->Form->create('Aco', array('url' => array('controller' => 'acl_actions', 'action' => 'add'))); ?>
        <fieldset>
        <?php
            echo $this->Form->input('parent_id', array(
                'options' => $acos,
                'empty' => true,
                'rel' => __('Choose none if the Aco is a controller.', true),
            ));
            echo $this->Form->input('alias', array());
        ?>
        </fieldset>
    <?php echo $this->Form->end('Submit'); ?>
</div>