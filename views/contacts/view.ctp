<div id="contact-<?php echo $contact['Contact']['id']; ?>" class="">
    <h2><?php echo $contact['Contact']['title']; ?></h2>
    <div class="contact-body">
    <?php echo $contact['Contact']['body']; ?>
    </div>

    <?php if ($contact['Contact']['message_status']) { ?>
    <div class="contact-form">
    <?php
        echo $form->create('Message', array(
            'url' => array(
                'controller' => 'contacts',
                'action' => 'view',
                $contact['Contact']['alias'],
            ),
        ));
        echo $form->input('Message.name', array('label' => __('Your name', true)));
        echo $form->input('Message.email', array('label' => __('Your email', true)));
        echo $form->input('Message.title', array('label' => __('Subject', true)));
        echo $form->input('Message.body', array('label' => __('Message', true)));
        if ($contact['Contact']['message_captcha']) {
            echo $recaptcha->display_form();
        }
        echo $form->end(__('Send', true));
    ?>
    </div>
    <?php } ?>
</div>