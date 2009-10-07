<div class="acl_actions form">
    <h2><? echo $this->pageTitle; ?></h2>
    <?php echo $form->create('Aco', array('url' => array('controller' => 'acl_actions', 'action' => 'add')));?>
        <fieldset>
        <?php
            echo $form->input('parent_id', array('options' => $acos, 'between' => '<br />', 'empty' => true));
            echo $form->input('alias', array('between' => '<br />'));
        ?>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>