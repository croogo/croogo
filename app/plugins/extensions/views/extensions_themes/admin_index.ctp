<div class="extensions-themes">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('Upload', true), array('action' => 'add')); ?></li>
            <!--<li><?php echo $html->link(__('Editor', true), array('action' => 'editor')); ?></li>-->
        </ul>
    </div>

    <div class="current-theme">
        <h3><?php __('Current Theme'); ?></h3>
        <div class="screenshot">
        <?php
            if ($currentTheme['Theme']['alias'] == 'default') {
                echo $html->image($currentTheme['Theme']['screenshot']);
            } else {
                echo $html->tag('div', $html->image('/themed/' . $currentTheme['Theme']['alias'] . '/img/' . $currentTheme['Theme']['screenshot']), array('class' => 'screenshot'));
            }
        ?>
        </div>
        <h3><?php echo $currentTheme['Theme']['name'] . ' ' . __('by', true) . ' ' . $currentTheme['Theme']['author'] ?></h3>
        <p class="description"><?php echo $currentTheme['Theme']['description']; ?></p>
        <p class="regions"><?php echo __('Regions supported: ', true) . implode(', ', Set::extract('/region', $currentTheme['Theme']['Regions'])); ?></p>
        <div class="clear"></div>
    </div>

    <div class="available-themes">
        <h3><?php __('Available Themes'); ?></h3>
        <ul>
        <?php
            foreach ($themesData AS $theme) {
                if ($theme['Theme']['alias'] != $currentTheme['Theme']['alias'] &&
                    (!isset($theme['Theme']['adminOnly']) || $theme['Theme']['adminOnly'] != 'true')) {
                    echo '<li>';
                        if ($theme['Theme']['alias'] == 'default') {
                            echo $html->tag('div', $html->image($theme['Theme']['screenshot']), array('class' => 'screenshot'));
                        } else {
                            echo $html->tag('div', $html->image('/themed/' . $theme['Theme']['alias'] . '/img/' . $theme['Theme']['screenshot']), array('class' => 'screenshot'));
                        }
                        echo $html->tag('h3', $theme['Theme']['name'] . ' ' . __('by', true) . ' ' . $theme['Theme']['author'], array());
                        echo $html->tag('p', $theme['Theme']['description'], array('class' => 'description'));
                        echo $html->tag('p', __('Regions supported: ', true) . implode(', ', Set::extract('/region', $theme['Theme']['Regions'])), array('class' => 'regions'));
                        echo $html->tag('div',
                            $html->link(__('Activate', true), array('action' => 'activate', $theme['Theme']['alias'])) .
                            $html->link(__('Delete', true), array('action' => 'delete', $theme['Theme']['alias']), null, __('Are you sure?', true)),
                            array('class' => 'actions'));
                    echo '</li>';
                }
            }
        ?>
        </ul>
        <div class="clear"></div>
    </div>
</div>
