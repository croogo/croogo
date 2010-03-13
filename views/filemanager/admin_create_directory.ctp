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
                'action' => 'create_directory',
            ), true) . '?path=' . urlencode($path),
        ));
    ?>
    <fieldset>
    <?php echo $form->input('Filemanager.name', array('type' => 'text')); ?>
    </fieldset>
    <?php echo $form->end("Submit"); ?>
</div>