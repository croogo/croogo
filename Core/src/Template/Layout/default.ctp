<?php
/**
 * Default Theme for Croogo CMS
 *
 * @author Fahad Ibnay Heylaal <contact@fahad19.com>
 * @link http://www.croogo.org
 */

use Cake\Core\Configure;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?= $this->fetch('title'); ?> &raquo; <?= Configure::read('Site.title'); ?></title>
    <?php
//    echo $this->Meta->meta();
    echo $this->Layout->feed();
    echo $this->Html->css(array(
        'Croogo/Core.reset',
        'Croogo/Core.960',
        'Croogo/Core.theme',
    ));
    echo $this->Layout->js();
    echo $this->Html->script(array(
        'Croogo/Core.jquery/jquery.min',
        'Croogo/Core.jquery/jquery.hoverIntent.minified',
        'Croogo/Core.theme',
    ));
    echo $this->Blocks->get('css');
    echo $this->Blocks->get('script');
    ?>
</head>
<body>
    <div id="wrapper">
        <div id="header" class="container_16">
            <div class="grid_16">
                <h1 class="site-title"><?= $this->Html->link(Configure::read('Site.title'), '/'); ?></h1>
                <span class="site-tagline"><?= Configure::read('Site.tagline'); ?></span>
            </div>
            <div class="clear"></div>
        </div>

        <div id="nav">
            <div class="container_16">
                <?= $this->Menus->menu('main', array('dropdown' => true)); ?>
            </div>
        </div>

        <div id="main" class="container_16">
            <div id="content" class="grid_11">
                <?php
                echo $this->Layout->sessionFlash();
                echo $this->fetch('content');
                ?>
            </div>

            <div id="sidebar" class="grid_5">
                <?= $this->Regions->blocks('right'); ?>
            </div>

            <div class="clear"></div>
        </div>

        <div id="footer">
            <div class="container_16">
                <div class="grid_8 left">
                    Powered by <a href="http://www.croogo.org">Croogo</a>.
                </div>
                <div class="grid_8 right">
                    <a href="http://www.cakephp.org"><?= $this->Html->image('/img/cake.power.gif'); ?></a>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <?= $this->Blocks->get('scriptBottom');?>
</body>
</html>
