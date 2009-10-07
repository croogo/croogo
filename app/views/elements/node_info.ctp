<div class="node-info">
<?php
    $type = $types_for_layout[$node['Node']['type']];

    if ($type['Type']['format_show_author'] || $type['Type']['format_show_date']) {
        __('Posted');
    }
    if ($type['Type']['format_show_author']) {
        echo ' ' . __('by', true) . ' ';
        if ($node['User']['website'] != null) {
            $author = $html->link($node['User']['name'], $node['User']['website']);
        } else {
            $author = $node['User']['name'];
        }
        echo $html->tag('span', $author, array(
            'class' => 'author',
        ));
    }
    if ($type['Type']['format_show_date']) {
        echo ' ' . __('on', true) . ' ';
        echo $html->tag('span', $time->nice($node['Node']['created']), array('class' => 'date'));
    }
?>
</div>