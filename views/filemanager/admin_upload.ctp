<div class="filemanager form">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="breadcrumb">
    <?php
        __('You are here:');

        $breadcrumb = $filemanager->breadcrumb($path);
        foreach($breadcrumb AS $pathname => $p) {
            echo $filemanager->linkDirectory($pathname, $p);
            echo DS;
        }
    ?>
    </div>

    <?php
        echo $form->create('Filemanager', array(
            'type' => 'file',
            'url' => $html->url(array(
                'controller' => 'filemanager',
                'action' => 'upload',
            ), true) . '?path=' . urlencode($path),
        ));
    ?>
    <fieldset>
    <?php echo $form->input('Filemanager.file', array('type' => 'file')); ?>
    </fieldset>
    <?php echo $form->end("Submit"); ?>
</div>