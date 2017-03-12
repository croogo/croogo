<?php
$showActions = isset($showActions) ? $showActions : true;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <title><?= $this->fetch('title'); ?> - <?= $_siteTitle ?></title>
        <?php

        echo $this->Html->css([
            'Croogo/Core.croogo-admin',
            'Croogo/Core.tether.min.css',
            'Croogo/Core.bootstrap-datetimepicker.min',
            'Croogo/Core.typeaheadjs',
        ]);
        echo $this->Layout->js();
        echo $this->Html->script([
            'Croogo/Core.jquery/jquery.min.js',
            'Croogo/Core.tether.min.js',
            'Croogo/Core.bootstrap.min.js',
            'Croogo/Core.jquery/jquery.slug',
            'Croogo/Core.jquery/jquery.cookie',
            'Croogo/Core.jquery/jquery.hoverIntent.minified',
            'Croogo/Core.jquery/superfish',
            'Croogo/Core.jquery/supersubs',
            'Croogo/Core.jquery/jquery.elastic-1.6.1.js',
            'Croogo/Core.underscore-min',
            'Croogo/Core.bootstrap3-typeahead.min',
            'Croogo/Core.admin',
            'Croogo/Core.sidebar',
            'Croogo/Core.choose',
            'Croogo/Core.moment-with-locales',
            'Croogo/Core.bootstrap-datetimepicker.min',
            'Croogo/Core.typeahead_autocomplete',
        ]);

        echo $this->fetch('script');
        echo $this->fetch('css');

        ?>
    </head>
    <body>
        <?php echo $this->element('Croogo/Core.admin/header'); ?>
        <div id="wrap">
            <div>
                <?php echo $this->element('Croogo/Core.admin/navigation'); ?>
            </div>
            <div id="content-container" class="content-container <?= $this->Theme->getCssClass('container') ?>">
                <div id="content" class="content <?= $this->Theme->getCssClass('row') ?>">
                    <div class="col-12 my-0 d-flex justify-content-between align-items-center">
                        <?= $this->element('Croogo/Core.admin/breadcrumb') ?>
                        <?php if ($showActions && $actionsBlock = $this->fetch('action-buttons')): ?>
                            <div class="actions">
                                <?php echo $actionsBlock; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div id="inner-content" class="<?= $this->Theme->getCssClass('columnFull') ?>">
                        <?php echo $this->Layout->sessionFlash(); ?>
                        <?php echo $this->fetch('content'); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $this->element('Croogo/Core.admin/footer'); ?>
        <?php
        echo $this->element('Croogo/Core.admin/initializers');
        echo $this->fetch('body-footer');

        echo $this->fetch('postLink');
        echo $this->fetch('scriptBottom');
        echo $this->Js->writeBuffer();
        ?>
    </body>
</html>
