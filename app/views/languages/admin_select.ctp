<div class="languages">
    <h2><?php echo $this->pageTitle; ?></h2>

    <?php
        foreach ($languages AS $language) {
            $title = $language['Language']['title'] . ' (' . $language['Language']['native'] . ')';
            $link = $html->link($title, array(
                'controller' => $controller,
                'action' => $action,
                $language['Language']['alias'],
                $id,
            ));
            echo '<h3>' . $link . '</h3>';
        }
    ?>
</div>