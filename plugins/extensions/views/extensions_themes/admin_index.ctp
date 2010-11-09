<div class="extensions-themes">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $this->Html->link(__('Upload', true), array('action' => 'add')); ?></li>
            <!--<li><?php echo $this->Html->link(__('Editor', true), array('action' => 'editor')); ?></li>-->
        </ul>
    </div>

    <div class="current-theme">
        <h3><?php __('Current Theme'); ?></h3>
        <div class="screenshot">
        <?php
            if (!Configure::read('Site.theme')) {
                echo $this->Html->image($currentTheme['screenshot']);
            } else {
                echo $this->Html->tag('div', $this->Html->image('/theme/' . Configure::read('Site.theme') . '/img/' . $currentTheme['screenshot']), array('class' => 'screenshot'));
            }
        ?>
        </div>
        <h3>
        <?php
            $author = $currentTheme['author'];
            if (isset($currentTheme['authorUrl']) && strlen($currentTheme['authorUrl']) > 0) {
                $author = $this->Html->link($author, $currentTheme['authorUrl']);
            }
            echo $currentTheme['name'] . ' ' . __('by', true) . ' ' . $author;
        ?>
        </h3>
        <p class="description"><?php echo $currentTheme['description']; ?></p>
        <p class="regions"><?php echo __('Regions supported: ', true) . implode(', ', $currentTheme['regions']); ?></p>
        <div class="clear"></div>
    </div>

    <div class="available-themes">
        <h3><?php __('Available Themes'); ?></h3>
        <ul>
        <?php
            foreach ($themesData AS $themeAlias => $theme) {
                if ($themeAlias != Configure::read('Site.theme') &&
                    (!isset($theme['adminOnly']) || $theme['adminOnly'] != 'true') &&
                    !($themeAlias == 'default' && !Configure::read('Site.theme'))) {
                    echo '<li>';
                        if ($themeAlias == 'default') {
                            echo $this->Html->tag('div', $this->Html->image($theme['screenshot']), array('class' => 'screenshot'));
                        } else {
                            echo $this->Html->tag('div', $this->Html->image('/theme/' . $themeAlias . '/img/' . $theme['screenshot']), array('class' => 'screenshot'));
                        }
                        $author = $theme['author'];
                        if (isset($theme['authorUrl']) && strlen($theme['authorUrl']) > 0) {
                            $author = $this->Html->link($author, $theme['authorUrl']);
                        }
                        echo $this->Html->tag('h3', $theme['name'] . ' ' . __('by', true) . ' ' . $author, array());
                        echo $this->Html->tag('p', $theme['description'], array('class' => 'description'));
                        echo $this->Html->tag('p', __('Regions supported: ', true) . implode(', ', $theme['regions']), array('class' => 'regions'));
                        echo $this->Html->tag('div',
                            $this->Html->link(__('Activate', true), array(
                                'action' => 'activate',
                                $themeAlias,
                                'token' => $this->params['_Token']['key'],
                            )) .
                            $this->Html->link(__('Delete', true), array(
                                'action' => 'delete',
                                $themeAlias,
                                'token' => $this->params['_Token']['key'],
                            ), null, __('Are you sure?', true)),
                            array('class' => 'actions'));
                    echo '</li>';
                }
            }
        ?>
        </ul>
        <div class="clear"></div>
    </div>
</div>
