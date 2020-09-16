<?php

use Cake\Core\Configure;

$this->loadHelper('Croogo/FileManager.FileManager');

if (isset($node)) :
    $mastheadTitle = $node->title;
    $mastheadSubheading = strip_tags($node->excerpt);
    $mastheadWrapperClass = "post-heading";
    if (isset($node->linked_assets['FeaturedImage'][0])) :
        $media = $node->linked_assets['FeaturedImage'][0];
        $bgImagePath = $media->path;
        if (isset($media->poster_path)):
            $posterPath = $media->poster_path;
        endif;
    endif;
elseif (isset($contact)) :
    $mastheadTitle = $contact->title;
    $mastheadSubheading = null;
    $mastheadWrapperClass = "post-heading";
    if (isset($contact->linked_assets['FeaturedImage'][0])) :
        $bgImagePath = $contact->linked_assets['FeaturedImage'][0]->path;
    endif;
elseif ($this->getRequest()->getParam('action') === 'index' && isset($type)) :
    $mastheadTitle = $type->title;
    $mastheadSubheading = null;
    $mastheadWrapperClass = "page-heading";
else :
    $mastheadTitle = Configure::read('Site.title');
    $mastheadSubheading = Configure::read('Site.tagline');
    $mastheadWrapperClass = "";
endif;

if (!isset($bgImagePath)) :
    $bgImagePath = Configure::read('Theme.bgImagePath') ?: 'img/header-bg.jpg';
endif;

$bgImageUrl = $this->Url->webroot($bgImagePath);
list($mimeType, $mimeSubtype) = explode('/', $this->FileManager->filename2mime($bgImageUrl));
$mastheadAttrs = [];
if ($mimeType == 'image'):
    $mastheadAttrs = [
        "background-image: url($bgImageUrl)",
    ];
endif;

if (isset($contact)) :
    $mastheadAttrs[] = 'background-color: #222';
endif;

$mastheadStyle = $mastheadAttrs ? implode(';', $mastheadAttrs) : null;

if (isset($node->meta)):
    $ogVideo = collection($node->meta)->firstMatch(['key' => 'og:video']);
    if ($ogVideo && substr($ogVideo->value, -3) == 'mp4'):
        $bgImageUrl = $ogVideo->value;
        $mimeType = 'video';
    endif;
endif;

?>
<header class="masthead" style="<?= $mastheadStyle ?>">
<?php if ($mimeType === 'video'): ?>
    <?= $this->Html->media([$bgImageUrl], [
            'tag' => 'video',
            'fullBase' => true,
            'controls', 'playsinline',
            'poster' => isset($posterPath) ? $posterPath : null,
    ]) ?>
<?php endif; ?>

<?php if (isset($ogVideo) && strstr($ogVideo->value, 'youtu.be') !== false): ?>
    <?php $parsed = parse_url($ogVideo->value); ?>
    <iframe src="https://youtube.com/embed<?= $parsed['path'] ?>?controls=0"
        class="youtube-embed"
        allowfullscreen
        frameborder="0"
        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
    >
    </iframe>
<?php endif; ?>

    <div class="container">

        <?php if (empty($mastheadWrapperClass)) : ?>
        <div class="intro-text">
            <div class="intro-heading">
                <?= h($mastheadTitle) ?>
            </div>

            <div class="intro-lead-in">
                <?= h($mastheadSubheading) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mastheadWrapperClass) : ?>
        <div class="<?= $mastheadWrapperClass ?>">
            <h1><?= h($mastheadTitle) ?></h1>

            <?php if (isset($contact)) : ?>
            <hr class="small">
            <?php endif; ?>

            <h2 class="subheading"><?= h($mastheadSubheading) ?></h2>
            <?php
            if (isset($node)) :
                $this->Nodes->set($node);
                echo $this->Nodes->info();
            endif;
            ?>
        </div>
        <?php endif; ?>

    </div>
</header>
