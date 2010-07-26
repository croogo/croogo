<div class="languages form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $form->create('Language'); ?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#language-basic"><?php __('Language'); ?></a></li>
                    <?php echo $layout->adminTabs(); ?>
                </ul>

                <div id="language-basic">
                <?php
                    echo $form->input('id');
                    echo $form->input('title');
                    echo $form->input('native');
                    echo $form->input('alias');
                    echo $form->input('status');
                ?>
                </div>
                <?php echo $layout->adminTabs(); ?>
            </div>
        </fieldset>
    <?php echo $form->end('Submit'); ?>
</div>