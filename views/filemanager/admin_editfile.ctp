<div class="filemanager form">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="breadcrumb">
    <?php
        echo __('You are here:', true) . ' ';
        $breadcrumb = $filemanager->breadcrumb($path);
        foreach($breadcrumb AS $pathname => $p) {
            echo $filemanager->linkDirectory($pathname, $p);
            echo DS;
        }
    ?>
    </div>

    <?php
        echo $form->create('Filemanager', array(
            'url' => $html->url(array(
                'controller' => 'filemanager',
                'action' => 'editfile',
            ), true) . '?path=' . urlencode($absolutefilepath),
        ));
    ?>
    <fieldset>
    <?php echo $form->input('Filemanager.content', array('type' => 'textarea', 'value' => $content, 'class' => 'content')); ?>
    </fieldset>
    <?php echo $form->end("Submit"); ?>
</div>