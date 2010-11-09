<div class="acl_aros form">
    <h2><?php __('Add Aro') ?></h2>
    <?php echo $this->Form->create('AclAro', array('url' => array('action' => 'edit')));?>
        <fieldset>
        <?php
            echo $this->Form->input('id');
            echo $this->Form->input('parent_id', array('between' => '<br />'));
            echo $this->Form->input('model', array('between' => '<br />'));
            echo $this->Form->input('foreign_key', array('between' => '<br />'));
            echo $this->Form->input('alias', array('between' => '<br />'));
        ?>
        </fieldset>
    <?php echo $this->Form->end('Submit');?>
</div>