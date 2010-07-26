<div class="roles form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $form->create('Role');?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#role-main"><span><?php __('Role'); ?></span></a></li>
                    <?php echo $layout->adminTabs(); ?>
                </ul>

                <div id="role-main">
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