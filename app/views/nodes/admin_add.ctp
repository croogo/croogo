<?php
    $javascript->link(array('nodes'), false);
    if (Configure::read('Writing.wysiwyg')) {
        $javascript->codeBlock($tinymce->fileBrowserCallBack(), array('inline' => false));
        $javascript->codeBlock($tinymce->init('NodeBody'), array('inline' => false));
    }
?>
<div class="nodes form">
    <h2><?php echo $this->pageTitle; ?></h2>
    <?php echo $form->create('Node', array('url' => array('action' => 'add', $typeAlias)));?>
        <fieldset>
            <div class="tabs">
                <ul>
                    <li><a href="#node-main"><span><?php __($type['Type']['title']); ?></span></a></li>
                    <?php if (count($terms) > 0) { ?><li><a href="#node-terms"><span><?php __('Terms'); ?></span></a></li><?php } ?>
                    <?php if ($type['Type']['comment_status'] != 0) { ?><li><a href="#node-comments"><span><?php __('Comments'); ?></span></a></li><?php } ?>
                    <li><a href="#node-meta"><span><?php __('Custom fields'); ?></span></a></li>
                    <li><a href="#node-access"><span><?php __('Access'); ?></span></a></li>
                    <li><a href="#node-publishing"><span><?php __('Publishing'); ?></span></a></li>
                </ul>

                <div id="node-main">
                <?php
                    echo $form->input('parent_id', array('type' => 'select', 'options' => $nodes, 'empty' => true));
                    echo $form->input('title');
                    echo $form->input('user_id');
                    echo $form->input('slug', array('class' => 'slug'));
                    echo $form->input('excerpt');
                    echo $form->input('body');
                ?>
                </div>

                <?php if (count($terms) > 0) { ?>
                <div id="node-terms">
                    <?php echo $form->input('Term.Term'); ?>
                </div>
                <?php } ?>

                <?php if ($type['Type']['comment_status'] != 0) { ?>
                <div id="node-comments">
                <?php
                    echo $form->input('comment_status', array(
                        'type' => 'radio',
                        'div' => array('class' => 'radio'),
                        'options' => array(
                            '0' => __('Disabled', true),
                            '1' => __('Read only', true),
                            '2' => __('Read/Write', true),
                        ),
                        'value' => $type['Type']['comment_status'],
                    ));
                ?>
                </div>
                <?php } ?>

                <div id="node-meta">
                    <div id="meta-fields">
                        <?php
                            $fields = array();
                            if (count($fields) > 0) {
                                foreach ($fields AS $fieldKey => $fieldValue) {
                                    echo $meta->field($fieldKey, $fieldValue);
                                }
                            } else {
                                echo $meta->field();
                            }
                        ?>
                        <div class="clear">&nbsp;</div>
                    </div>
                    <a href="#" class="add-meta"><?php __('Add another field'); ?></a>
                </div>

                <div id="node-access">
                    <?php
                        echo $form->input('Role.Role');
                    ?>
                </div>

                <div id="node-publishing">
                <?php
                    echo $form->input('status', array('label' => __('Published', true)));
                    echo $form->input('promote', array('label' => __('Promoted to front page', true)));
                    echo $form->input('created');
                ?>
                </div>
                <div class="clear">&nbsp;</div>
            </div>
        </fieldset>
    <?php echo $form->end('Submit');?>
</div>