<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <title><?= $this->fetch('title') ?> - <?= $_siteTitle ?></title>
    <?php

        echo $this->element('admin/stylesheets');
        echo $this->element('admin/javascripts');

        echo $this->fetch('script');
        echo $this->fetch('css');

    ?>
</head>

<body>

    <div id="wrap">
        <?php echo $this->element('admin/header'); ?>
        <div id="push"></div>
        <div id="content-container" class="content-container <?php echo $this->Theme->getCssClass('container'); ?>">
            <div class="<?php echo $this->Theme->getCssClass('row'); ?>">
                <div id="content" class="content">
                    <div id="inner-content" class="<?= $this->Theme->getCssClass('columnFull') ?>">
                        <?php echo $this->Layout->sessionFlash(); ?>
                        <?php echo $this->fetch('content'); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php echo $this->element('admin/footer'); ?>
    <?php
        echo $this->element('admin/initializers');
        echo $this->Blocks->get('scriptBottom');
        echo $this->Js->writeBuffer();
    ?>
    </body>
</html>