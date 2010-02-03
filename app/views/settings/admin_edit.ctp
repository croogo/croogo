<div class="settings form">
    <h2><? echo $title_for_layout; ?></h2>
    <?php echo $form->create('Setting');?>
	<fieldset>
        <div class="tabs">
            <ul>
                <li><a href="#setting-basic"><span><?php __('Settings'); ?></span></a></li>
                <li><a href="#setting-misc"><span><?php __('Misc.'); ?></span></a></li>
            </ul>

            <div id="setting-basic">
                <?php
                    echo $form->input('id');
                    echo $form->input('key', array('rel' => __("e.g., 'Site.title'", true)));
                    echo $form->input('value');
                ?>
            </div>

            <div id="setting-misc">
                <?php
                    echo $form->input('title');
                    echo $form->input('description');
                    echo $form->input('input_type', array('rel' => __("e.g., 'text' or 'textarea'", true)));
                    echo $form->input('editable');
                    //echo $form->input('weight');
                    echo $form->input('params');
                ?>
            </div>

        </div>
	</fieldset>
    <?php echo $form->end('Submit');?>
</div>
