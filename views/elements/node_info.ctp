<div class="node-info">
<?php
    $type = $types_for_layout[$layout->node('type')];

    if ($type['Type']['format_show_author'] || $type['Type']['format_show_date']) {
        __('Posted');
    }
    if ($type['Type']['format_show_author']) {
        echo ' ' . __('by', true) . ' ';
        if ($layout->node('User.website') != null) {
            $author = $html->link($layout->node('User.name'), $layout->node('User.website'));
        } else {
            $author = $layout->node('User.name');
        }
        echo $html->tag('span', $author, array(
            'class' => 'author',
        ));
    }
    if ($type['Type']['format_show_date']) {
        echo ' ' . __('on', true) . ' ';
        echo $html->tag('span', $time->format(Configure::read('Reading.date_time_format'), $layout->node('created'), null, Configure::read('Site.timezone')), array('class' => 'date'));
    }
?>
</div>