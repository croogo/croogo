<div class="contacts form">
    <h2><?php echo $title_for_layout; ?></h2>
    <?php echo $form->create('Contact');?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#contact-basic"><?php __('Contact'); ?></a></li>
                    <li><a href="#contact-details"><?php __('Details'); ?></a></li>
                    <li><a href="#contact-message"><?php __('Message'); ?></a></li>
                    <?php echo $layout->adminTabs(); ?>
                </ul>

                <div id="contact-basic">
                <?php
                    echo $form->input('title');
                    echo $form->input('alias');
                    echo $form->input('email');
                    echo $form->input('body');
                    echo $form->input('status');
                ?>
                </div>

                <div id="contact-details">
                <?php
                    echo $form->input('name');
                    echo $form->input('position');
                    echo $form->input('address');
                    echo $form->input('address2');
                    echo $form->input('state');
                    echo $form->input('country');
                    echo $form->input('postcode');
                    echo $form->input('phone');
                    echo $form->input('fax');
                ?>
                </div>

                <div id="contact-message">
                <?php
                    echo $form->input('message_status', array(
                        'label' => __('Let users leave a message', true),
                    ));
                    echo $form->input('message_archive', array(
                        'label' => __('Save messages in database', true),
                    ));
                    echo $form->input('message_notify', array(
                        'label' => __('Notify by email instantly', true),
                    ));
                    echo $form->input('message_spam_protection', array(
                        'label' => __('Spam protection (requires Akismet API key)', true),
                    ));
                    echo $form->input('message_captcha', array(
                        'label' => __('Use captcha? (requires Recaptcha API key)', true),
                    ));
                ?>
                    <p>
                    <?php
                        echo $html->link(__('You can manage your API keys here.', true), array(
                            'controller' => 'settings',
                            'action' => 'prefix',
                            'Service',
                        ));
                    ?>
                    </p>
                </div>
                <?php echo $layout->adminTabs(); ?>
            </div>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>