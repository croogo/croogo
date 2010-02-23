<div class="languages">
    <h2><?php echo $title_for_layout; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('New Language', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <?php
        foreach ($languages AS $language) {
            $title = $language['Language']['title'] . ' (' . $language['Language']['native'] . ')';
            $link = $html->link($title, array(
                'plugin' => 'translate',
                'controller' => 'translate',
                'action' => 'edit',
                $id,
                $modelAlias,
                'locale' => $language['Language']['alias'],
            ));
            echo '<h3>' . $link . '</h3>';
        }
    ?>
</div>