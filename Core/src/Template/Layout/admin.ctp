<?php
$showActions = isset($showActions) ? $showActions : true;
?>
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
    </head>
    <body id="body" class="header-fixed sidebar-fixed-offcanvas sidebar-dark header-dark sidebar-collapse">
        <div class="wrapper">

            <aside class="left-sidebar bg-sidebar">

                <div id="sidebar" class="sidebar sidebar-with-footer">

                <div class="app-brand">
                    <a href="javascript:void(0)">
                        <svg
                        class="brand-icon"
                        xmlns="http://www.w3.org/2000/svg"
                        preserveAspectRatio="xMidYMid"
                        width="30"
                        height="33"
                        viewBox="0 0 30 33"
                        >
                        <g fill="none" fill-rule="evenodd">
                            <path
                            class="logo-fill-blue"
                            fill="#7DBCFF"
                            d="M0 4v25l8 4V0zM22 4v25l8 4V0z"
                            />
                            <path class="logo-fill-white" fill="#FFF" d="M11 4v25l8 4V0z" />
                        </g>
                        </svg>
                        <span class="brand-name">Croogo</span>
                    </a>
                </div>
                    <?= $this->element('Croogo/Core.admin/navigation') ?>
                </div>
            </aside>

            <div class="page-wrapper">
                <?= $this->element('Croogo/Core.admin/header') ?>
                <div class="content-wrapper">

                    <div id="breadcrumb-container" class="p-0 d-flex justify-content-between align-items-center">
                        <?= $this->element('Croogo/Core.admin/breadcrumb') ?>
                        <?php if ($showActions && $actionsBlock = $this->fetch('action-buttons')) : ?>
                            <div class="actions m-2 ml-auto">
                                <?= $actionsBlock ?>
                            </div>
                        <?php endif ?>
                    </div>

                    <div id="inner-content" class="content">
                        <?= $this->Layout->sessionFlash() ?>
                        <?= $this->fetch('content') ?>
                    </div>

                </div>

                <?= $this->element('Croogo/Core.admin/footer') ?>
            </div>
        </div>
        <?php
        echo $this->element('Croogo/Core.admin/initializers');
        echo $this->fetch('body-footer');

        echo $this->fetch('postLink');
        echo $this->fetch('scriptBottom');
        echo $this->Js->writeBuffer();
        ?>
    </body>
</html>
