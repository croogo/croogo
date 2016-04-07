<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title><?php echo $this->fetch('title'); ?> - <?php echo __d('croogo', 'Croogo'); ?></title>
        <?php

        echo $this->Html->css([
            'Croogo/Core.croogo-admin',
            'https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/css/tether.min.css',
        ]);
        echo $this->Layout->js();
        echo $this->Html->script([
            'https://code.jquery.com/jquery-2.2.2.min.js',
            'Croogo/Core.jquery/jquery.slug',
            'Croogo/Core.croogo-bootstrap.js',
            'Croogo/Core.underscore-min',
            'Croogo/Core.admin',
            'https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js',
            'https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/js/bootstrap.js',
        ]);

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
