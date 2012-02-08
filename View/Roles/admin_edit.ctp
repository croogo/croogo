<div class="roles form">
    <h2><?php echo $title_for_layout; ?></h2>

    <?php echo $this->Form->create('Role');?>
    <fieldset>
        <div class="tabs">
            <ul>
                <li><a href="#role-main"><span><?php echo __('Role'); ?></span></a></li>
                <?php echo $this->Layout->adminTabs(); ?>
            </ul>

            <div id="role-main">
            <?php
                echo $this->Form->input('id');
                echo $this->Form->input('parent_id', array('empty' => true));
                echo $this->Form->input('title');
                echo $this->Form->input('alias');
            ?>
            </div>
            <?php echo $this->Layout->adminTabs(); ?>
        </div>
    </fieldset>

    <div class="buttons">
    <?php
        echo $this->Form->end(__('Save'));
        echo $this->Html->link(__('Cancel'), array(
            'action' => 'index',
        ), array(
            'class' => 'cancel',
        ));
    ?>
    </div>
</div>