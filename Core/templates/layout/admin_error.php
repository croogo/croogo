<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title><?= $this->fetch('title') ?> - <?= $_siteTitle ?></title>
        <?php

        echo $this->element('admin/stylesheets');
        echo $this->element('admin/javascripts');

        echo $this->fetch('script');
        echo $this->fetch('css');

        ?>
        <style>
            #content {
                border-radius: 0px;
            }
        </style>
    </head>
    <body>
        <?= $this->element('Croogo/Core.admin/header') ?>
        <div id="wrap">
            <div id="content-container" class="content-container">
                <div id="content" class="content">
                    <div id="inner-content" class="<?= $this->Theme->getCssClass('columnFull') ?>">
                        <?= $this->Layout->sessionFlash() ?>
                        <?= $this->fetch('content') ?>
                    </div>
                </div>
            </div>
        </div>
        <?= $this->element('Croogo/Core.admin/footer') ?>
        <?php
        echo $this->element('Croogo/Core.admin/initializers');
        echo $this->fetch('body-footer');

        echo $this->fetch('postLink');
        echo $this->fetch('scriptBottom');
        echo $this->Js->writeBuffer();
        ?>
    </body>
</html>
