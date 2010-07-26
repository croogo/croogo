<div class="blocks form">
    <h2><?php __('Edit Block'); ?></h2>
    <?php echo $form->create('Block');?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#block-basic"><span><?php __('Block'); ?></span></a></li>
                    <li><a href="#block-access"><span><?php __('Access'); ?></span></a></li>
                    <li><a href="#block-visibilities"><span><?php __('Visibilities'); ?></span></a></li>
                    <?php echo $layout->adminTabs(); ?>
                </ul>

                <div id="block-basic">
                    <?php
                        echo $form->input('id');
                        echo $form->input('title');
                        echo $form->input('show_title');
                        echo $form->input('alias', array('rel' => __('unique name for your block', true)));
                        echo $form->input('region_id', array('rel' => __('if you are not sure, choose \'none\'', true)));
                        echo $form->input('body');
                        echo $form->input('class');
                        echo $form->input('element');
                        echo $form->input('status');
                    ?>
                </div>

                <div id="block-access">
                    <?php
                        echo $form->input('Role.Role');
                    ?>
                </div>

                <div id="block-visibilities">
                    <?php
                        echo $form->input('Block.visibility_paths', array(
                            'class' => 'wide',
                            'rel' => __('Enter one URL per line. Leave blank if you want this Block to appear in all pages.', true)
                        ));
                        /*echo $form->input('Block.visibility_php', array(
                            'class' => 'wide',
                            'rel' => __('Block will be shown if the PHP code returns true. If unsure, leave blank.', true)
                        ));*/
                    ?>
                </div>
                <?php echo $layout->adminTabs(); ?>
            </div>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>