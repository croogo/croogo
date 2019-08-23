<?php

$activeThemes = [$currentTheme['name'], $currentBackendTheme['name']];

?>
<div class="card">

    <?php
        if (!empty($theme['screenshot'])):
            $dataUri = $this->Croogo->dataUri($theme['name'], $theme['screenshot']);
            $thumbnail = '<img class="card-img-top" src="' . $dataUri . '">';
            $image = sprintf('<a href="%s" %s>%s</a>',
                $dataUri,
                'data-toggle="lightbox"',
                $thumbnail
            );
            echo $image;
        endif;
    ?>

    <div class="card-body">

        <h5 class="card-title">
            <?php
            $author = isset($theme['author']) ? $theme['author'] : null;
            if (isset($theme['authorUrl']) && strlen($theme['authorUrl']) > 0) {
                $author = $this->Html->link($author, $theme['authorUrl']);
            }
            echo $theme['name'];
            if (!empty($author)):
                echo ' ' . __d('croogo', 'by') . ' ' . $author;
            endif;
            ?>
        </h5>

        <?php
            $badge = '';
            if ($theme['name'] == $currentTheme['name']):
                $badge .= $this->Html->tag('p', 'Current Frontend Theme', ['class' => 'badge badge-success']);
            endif;
            if ($theme['name'] == $currentBackendTheme['name']):
                $badge .= $this->Html->tag('p', 'Current Backend Theme', ['class' => 'badge badge-success']);
            endif;
            if ($badge):
                echo $badge;
            endif;
        ?>

        <p class="card-text"><?= $theme['description'] ?></p>
        <?php if (isset($theme['regions'])): ?>
            <p class="regions"><?= __d('croogo', 'Regions supported: ') .
                    implode(', ', $theme['regions']) ?></p>
        <?php endif ?>

   </div>

<?php


    $out = '';
    if ($theme['isFrontendTheme'] && $currentTheme['name'] != $theme['name']):
        $out .= $this->Form->postLink(__d('croogo', 'Activate Frontend'), [
                'action' => 'activate',
                urlencode(base64_encode($theme['name'])),
            ], [
                'button' => 'outline-secondary btn-sm',
                'icon' => $this->Theme->getIcon('power-on'),
                'escape' => false,
            ]);
    endif;

    if ($theme['isBackendTheme'] && $currentBackendTheme['name'] != $theme['name']):
        $out .= $this->Form->postLink(__d('croogo', 'Activate Backend'), [
                'action' => 'activate',
                urlencode(base64_encode($theme['name'])),
                'admin_theme',
            ], [
                'button' => 'outline-secondary btn-sm',
                'icon' => $this->Theme->getIcon('power-on'),
                'escape' => false,
            ]);
    endif;

    if (!in_array($theme['name'], $activeThemes)):
        $out .= $this->Form->postLink(__d('croogo', 'Delete'), [
                'action' => 'delete',
                urlencode($theme['name']),
            ], [
                'button' => 'outline-danger btn-sm',
                'escape' => true,
                'escapeTitle' => false,
                'icon' => $this->Theme->getIcon('delete'),
            ], __d('croogo', 'Are you sure?'));
    endif;

    if (!empty($out)):
        echo $this->Html->div('actions text-right card-footer' , $out);
    endif;
?>
</div>
