<div class="acl_aros form">
    <h2><?php __('Add Aro') ?></h2>
    <?php echo $form->create('AclAro', array('url' => array('action' => 'edit')));?>
        <fieldset>
        <?php
            echo $form->input('id');
            echo $form->input('parent_id', array('between' => '<br />'));
            echo $form->input('model', array('between' => '<br />'));
            echo $form->input('foreign_key', array('between' => '<br />'));
            echo $form->input('alias', array('between' => '<br />'));
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>