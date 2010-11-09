<div class="acl_acos form">
    <h2><?php __('Add Aco') ?></h2>
    <?php echo $this->Form->create('AclAco', array('url' => array('action' => 'add')));?>
        <fieldset>
        <?php
            echo $this->Form->input('parent_id', array('between' => '<br />'));
            echo $this->Form->input('model', array('between' => '<br />'));
            echo $this->Form->input('foreign_key', array('between' => '<br />'));
            echo $this->Form->input('alias', array('between' => '<br />'));
        ?>
        </fieldset>
    <?php echo $this->Form->end('Submit');?>
</div>