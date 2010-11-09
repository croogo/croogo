<div class="regions form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $this->Form->create('Region');?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#region-main"><span><?php __('Region'); ?></span></a></li>
                    <?php echo $this->Layout->adminTabs(); ?>
                </ul>

                <div id="region-main">
                <?php
                    echo $this->Form->input('title');
                    echo $this->Form->input('alias');
                ?>
                </div>
                <?php echo $this->Layout->adminTabs(); ?>
            </div>
        </fieldset>
    <?php echo $this->Form->end('Submit');?>
</div>