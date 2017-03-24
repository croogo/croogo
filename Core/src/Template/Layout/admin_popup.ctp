<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title><?= $this->fetch('title'); ?> - <?= $_siteTitle ?></title>
        <?php

        echo $this->element('admin/stylesheets');
        echo $this->element('admin/javascripts');

        echo $this->fetch('script');
        echo $this->fetch('css');
        ?>
    </head>
    <body class="popup">
        <div class="<?php echo $this->Theme->getCssClass('container'); ?>">
            <div class="<?php echo $this->Theme->getCssClass('row'); ?>">
                <div id="content" class="<?php echo $this->Theme->getCssClass('columnFull'); ?>">
                    <?php echo $this->Layout->sessionFlash(); ?>
                    <?php echo $this->fetch('content'); ?>
                </div>
            </div>
        </div>
        <?php
        echo $this->element('Croogo/Core.admin/initializers');
        echo $this->Blocks->get('scriptBottom');
        ?>
    </body>
</html>
