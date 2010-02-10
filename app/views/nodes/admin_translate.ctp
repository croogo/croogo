<?php
    $html->script(array('nodes'), false);
    if (Configure::read('Writing.wysiwyg')) {
        $html->scriptBlock($tinymce->fileBrowserCallBack(), array('inline' => false));
        $html->scriptBlock($tinymce->init('NodeBody'), array('inline' => false));
    }
?>
<div class="nodes form">
    <h2><?php echo $title_for_layout; ?></h2>

    <?php
        echo $form->create('Node', array('url' => array(
            'action' => 'translate',
            'locale' => $this->params['named']['locale'],
        )));
    ?>
    <fieldset>
        <div class="tabs">
            <ul>
                <li><a href="#node-main"><span><?php __($type['Type']['title']); ?></span></a></li>
            </ul>

            <div id="node-main">
            <?php
                foreach ($fields AS $field) {
                    echo $form->input('Node.'.$field);
                }
             ?>
             </div>
        </div>
    </fieldset>
    <?php echo $form->end('Submit');?>
</div>