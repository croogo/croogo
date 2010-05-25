<div id="vocabulary-<?php echo $vocabulary['Vocabulary']['id']; ?>" class="vocabulary">
    <ul>
    <?php
        foreach ($vocabulary['list'] AS $termSlug => $termTitle) {
            if ($options['link']) {
                echo '<li>';
                echo $html->link($termTitle, array(
                    'plugin' => $options['plugin'],
                    'controller' => $options['controller'],
                    'action' => $options['action'],
                    'type' => $options['type'],
                    'slug' => $termSlug,
                ));
                echo '</li>';
            } else {
                echo '<li>' . $termTitle . '</li>';
            }
        }
    ?>
    </ul>
</div>