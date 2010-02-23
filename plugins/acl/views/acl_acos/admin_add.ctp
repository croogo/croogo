<div class="acl_acos form">
    <h2><?php __('Add Aco') ?></h2>
    <?php echo $form->create('AclAco', array('url' => array('action' => 'add')));?>
        <fieldset>
        <?php
            echo $form->input('parent_id', array('between' => '<br />'));
            echo $form->input('model', array('between' => '<br />'));
            echo $form->input('foreign_key', array('between' => '<br />'));
            echo $form->input('alias', array('between' => '<br />'));
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>