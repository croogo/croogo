<div class="menus form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $form->create('Menu');?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#menu-basic"><span><?php __('Menu'); ?></span></a></li>
                    <li><a href="#menu-misc"><span><?php __('Misc.'); ?></span></a></li>
                    <?php echo $layout->adminTabs(); ?>
                </ul>

                <div id="menu-basic">
                    <?php
                        echo $form->input('id');
                        echo $form->input('title');
                        echo $form->input('alias');
                        echo $form->input('description');
                        //echo $form->input('status');
                    ?>
                </div>

                <div id="menu-misc">
                    <?php
                        echo $form->input('params');
                    ?>
                </div>
                <?php echo $layout->adminTabs(); ?>
            </div>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>