<?php

use Cake\Core\Configure;

$this->extend('Croogo/Core./Common/admin_index');

$this->assign('title', __d('croogo', 'Themes'));

$this->Breadcrumbs->add(__d('croogo', 'Extensions'),
        ['plugin' => 'Croogo/Extensions', 'controller' => 'extensionsPlugins', 'action' => 'index'])
    ->add(__d('croogo', 'Themes'), $this->request->getUri()->getPath());

$this->start('action-buttons');
echo $this->Croogo->adminAction(__d('croogo', 'Upload'), ['action' => 'add'], ['class' => 'btn btn-success']);
$this->end(); ?>

<div class="<?php echo $this->Theme->getCssClass('row'); ?>">
    <div class="extensions-themes <?php echo $this->Theme->getCssClass('columnFull'); ?>">
        <div class="current-theme <?php echo $this->Theme->getCssClass('row'); ?>">
            <div class="screenshot <?php echo $this->Theme->getCssClass('columnRight'); ?>">
                <h3><?php echo __d('croogo', 'Current Theme'); ?></h3>
                <?php
                if (isset($currentTheme['screenshot'])):
                    if (!Configure::read('Site.theme')) :
                        $file = $currentTheme['screenshot'];
                    else:
                        $file = $this->Url->assetUrl(Configure::read('Site.theme') . '.' . $currentTheme['screenshot']);
                    endif;
                    $file = str_replace($this->request->base, '', $file);
                    $imgUrl = $this->Html->thumbnail($file);
                    $link = $this->Html->link($imgUrl, $file, [
                        'escape' => false,
                        'data-toggle' => 'lightbox',
                    ]);
                    echo $this->Html->tag('div', $link);
                endif;
                ?>
            </div>

            <div class="<?php echo $this->Theme->getCssClass('columnLeft'); ?>">
                <h3>
                    <?php
                    $author = isset($currentTheme['author']) ? $currentTheme['author'] : null;
                    if (isset($currentTheme['authorUrl']) && strlen($currentTheme['authorUrl']) > 0) {
                        $author = $this->Html->link($author, $currentTheme['authorUrl']);
                    }
                    echo $currentTheme['name'];
                    if (!empty($author)):
                        echo ' ' . __d('croogo', 'by') . ' ' . $author;
                    endif;
                    ?>
                </h3>
                <p class="description"><?php echo $currentTheme['description']; ?></p>
                <?php if (isset($currentTheme['regions'])): ?>
                    <p class="regions"><?php echo __d('croogo', 'Regions supported: ') .
                            implode(', ', $currentTheme['regions']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="available-themes">
            <h3><?php echo __d('croogo', 'Available Themes'); ?></h3>

            <?php
            $hasAvailable = false;
            foreach ($themesData as $themeAlias => $theme):
                $isAdminOnly = (!isset($theme['adminOnly']) || $theme['adminOnly'] != 'true');
                $isDefault = !($themeAlias == 'default' && !Configure::read('Site.theme'));
                $display = $themeAlias != Configure::read('Site.theme') && $isAdminOnly && $isDefault;
                if (!$display):
                    continue;
                endif;
                echo '<div class="content ' . $this->Theme->getCssClass('row') . '">';
                if (!empty($theme['screenshot'])):
                    $dataUri = $this->Croogo->dataUri($themeAlias, $theme['screenshot']);
                    $thumbnail = $this->Html->thumbnail($dataUri);
                    $image = sprintf('<a href="%s" %s>%s</a>',
                        $dataUri,
                        'data-toggle="lightbox"',
                        $thumbnail
                    );

                    echo $this->Html->tag('div', $image, [
                        'class' => 'screenshot ' . $this->Theme->getCssClass('columnRight'),
                    ]);
                else:
                    echo $this->Html->tag('div', '', [
                        'class' => $this->Theme->getCssClass('columnRight'),
                    ]);
                endif;
                $author = isset($theme['author']) ? $theme['author'] : null;
                if (isset($theme['authorUrl']) && strlen($theme['authorUrl']) > 0) {
                    $author = $this->Html->link($author, $theme['authorUrl']);
                }

                $out = $this->Html->tag('h3', $theme['name'] . ' ' . __d('croogo', 'by') . ' ' . $author, []);
                $out .= $this->Html->tag('p', $theme['description'], ['class' => 'description']);
                if (isset($theme['regions'])):
                    $out .= $this->Html->tag('p',
                        __d('croogo', 'Regions supported: ') . implode(', ', $theme['regions']),
                        ['class' => 'regions']);
                endif;
                $out .= $this->Html->tag('div', $this->Form->postLink(__d('croogo', 'Activate'), [
                        'action' => 'activate',
                        str_replace('/', '.', $themeAlias),
                    ], [
                        'button' => 'secondary',
                        'icon' => $this->Theme->getIcon('power-on'),
                    ]) . $this->Form->postLink(__d('croogo', 'Delete'), [
                        'action' => 'delete',
                        str_replace('/', '.', $themeAlias),
                    ], [
                        'button' => 'danger',
                        'escape' => true,
                        'escapeTitle' => false,
                        'icon' => $this->Theme->getIcon('delete'),
                    ], __d('croogo', 'Are you sure?')), ['class' => 'actions']);
                echo $this->Html->div($this->Theme->getCssClass('columnLeft'), $out);
                echo '</div>';
                $hasAvailable = true;
            endforeach;

            if (!$hasAvailable):
                echo $this->Html->tag('li', __d('croogo', 'No available theme'));
            endif;
            ?>
        </div>
    </div>
</div>
