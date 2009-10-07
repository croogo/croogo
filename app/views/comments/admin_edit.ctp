<div class="comments form">
    <h2><?php echo $this->pageTitle; ?></h2>

    <?php echo $form->create('Comment');?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#comment-main"><?php __('Comment'); ?></a></li>
                    <li><a href="#comment-contact"><?php __('Contact Info'); ?></a></li>
                </ul>

                <div id="comment-main">
                <?php
                    echo $form->input('id');
                    echo $form->input('title');
                    echo $form->input('body');
                    echo $form->input('status', array('label' => __('Published', true)));
                ?>
                </div>

                <div id="comment-contact">
                <?php
                    echo $form->input('name');
                    echo $form->input('email');
                    echo $form->input('website');
                    echo $form->input('ip', array('disabled' => 'disabled'));
                ?>
                </div>
            </div>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>