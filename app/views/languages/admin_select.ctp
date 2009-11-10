<div class="languages">
    <h2><?php echo $this->pageTitle; ?></h2>

    <div class="actions">
        <ul>
            <li><?php echo $html->link(__('New Language', true), array('action'=>'add')); ?></li>
        </ul>
    </div>

    <?php
        foreach ($languages AS $language) {
            $title = $language['Language']['title'] . ' (' . $language['Language']['native'] . ')';
            $link = $html->link($title, array(
                'controller' => $controller,
                'action' => $action,
                'locale' => $language['Language']['alias'],
                $id,
            ));
            echo '<h3>' . $link . '</h3>';
        }
    ?>
</div>