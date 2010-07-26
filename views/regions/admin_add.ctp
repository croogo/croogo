<div class="regions form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $form->create('Region');?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#region-main"><span><?php __('Region'); ?></span></a></li>
                    <?php echo $layout->adminTabs(); ?>
                </ul>

                <div id="region-main">
                <?php
                    echo $form->input('title');
                    echo $form->input('alias');
                ?>
                </div>
                <?php echo $layout->adminTabs(); ?>
            </div>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>