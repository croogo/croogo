<div class="menus form">
    <h2><?php echo $this->pageTitle; ?></h2>
    <?php echo $form->create('Menu');?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#menu-basic"><span><?php __('Menu'); ?></span></a></li>
                    <li><a href="#menu-misc"><span><?php __('Misc.'); ?></span></a></li>
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

            </div>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>