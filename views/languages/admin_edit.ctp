<div class="languages form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $this->Form->create('Language'); ?>
    <fieldset>
        <div class="tabs">
            <ul>
                <li><a href="#language-basic"><?php __('Language'); ?></a></li>
                <?php echo $this->Layout->adminTabs(); ?>
            </ul>

            <div id="language-basic">
            <?php
                echo $this->Form->input('id');
                echo $this->Form->input('title');
                echo $this->Form->input('native');
                echo $this->Form->input('alias');
                echo $this->Form->input('status');
            ?>
            </div>
            <?php echo $this->Layout->adminTabs(); ?>
        </div>
    </fieldset>

    <div class="buttons">
    <?php
        echo $this->Form->end(__('Save', true));
        echo $this->Html->link(__('Cancel', true), array(
            'action' => 'index',
        ), array(
            'class' => 'cancel',
        ));
    ?>
    </div>
</div>