<div class="node-info">
<?php
    $type = $typesForLayout[$this->Nodes->field('type')];

    if ($type->format_show_author || $type->format_show_date) {
        echo __d('croogo', 'Posted');
    }
    if ($type->format_show_author) {
        echo ' ' . __d('croogo', 'by') . ' ';
        if ($this->Nodes->field('user.website') != null) {
            $author = $this->Html->link($this->Nodes->field('user.name'), $this->Nodes->field('user.website'));
        } else {
            $author = $this->Nodes->field('user.name');
        }

        echo $this->Html->tag('span', $author, array(
            'class' => 'author',
        ));
    }
    if ($type->format_show_date) {
        echo ' ' . __d('croogo', 'on') . ' ';
        echo $this->Html->tag('span', $this->Nodes->date($this->Nodes->field('publish_start')), array('class' => 'date'));
    }
?>
</div>
