<?php
$chooserType = isset($this->getRequest()->query['chooser_type']) ? $this->getRequest()->query['chooser_type'] : 'attachment';
?>
<div class="clearfix filter">
    <?php
    echo $this->Form->create(
        null,
        [
            'align' => 'inline',
        ]
    );
    $this->Form->templates(
        [
            'label' => false,
            'submitContainer' => '{{content}}',
        ]
    );
    echo $this->Form->input(
        'chooser_type',
        [
            'type' => 'hidden',
            'value' => $chooserType,
        ]
    );

    echo $this->Form->input(
        'chooser',
        [
            'type' => 'hidden',
            'value' => isset($this->getRequest()->query['chooser']),
        ]
    );

    echo $this->Form->input(
        'filter',
        [
            'label' => false,
            'title' => __d('croogo', 'Search'),
            'placeholder' => __d('croogo', 'Search...'),
            'tooltip' => false,
        ]
    );

    echo $this->Form->input(
        __d('croogo', 'Filter'),
        [
            'type' => 'submit',
        ]
    );
    echo $this->Form->end();
    ?>
</div>
