<?php

use Cake\Core\Configure;
use Cake\Utility\Hash;

if (isset($node)):
    $mastheadTitle = $node->title;
    $mastheadSubheading = $node->excerpt;
    $mastheadWrapperClass = "post-heading";
elseif (isset($contact)):
    $mastheadTitle = $contact->title;
    $mastheadSubheading = null;
    $mastheadWrapperClass = "post-heading";
elseif ($this->request->param('action') === 'index' && isset($type)):
    $mastheadTitle = $type->title;
    $mastheadSubheading = $type->description;
    $mastheadWrapperClass = "page-heading";
else:
    $mastheadTitle = Configure::read('Site.title');
    $mastheadSubheading = Configure::read('Site.tagline');
    $mastheadWrapperClass = "";
endif;

if (!isset($bgImagePath)):
    $bgImagePath = Configure::read('Theme.bgImagePath') ?: 'img/header-bg.jpg';
endif;

$bgImageUrl = $this->Url->webroot($bgImagePath);
$mastheadAttrs = [
    "background-image: url($bgImageUrl)",
];

if (isset($contact)):
    $mastheadAttrs[] = 'background-color: #222';
endif;

$mastheadStyle = $mastheadAttrs ? implode(';', $mastheadAttrs) : null;

?>
<header class="masthead" style="<?= $mastheadStyle ?>">
    <div class="container">

        <?php if (empty($mastheadWrapperClass)): ?>
        <div class="intro-text">
            <div class="intro-heading">
                <?= h($mastheadTitle) ?>
            </div>

            <div class="intro-lead-in">
                <?= h($mastheadSubheading) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mastheadWrapperClass): ?>
        <div class="<?= $mastheadWrapperClass ?>">
            <h1><?= h($mastheadTitle) ?></h1>

            <?php if (isset($contact)): ?>
            <hr class="small">
            <?php endif; ?>

            <h2 class="subheading"><?= h($mastheadSubheading) ?></h2>
            <?php
            if (isset($node)):
                $this->Nodes->set($node);
                echo $this->Nodes->info();
            endif;
            ?>
        </div>
        <?php endif; ?>

    </div>
</header>
