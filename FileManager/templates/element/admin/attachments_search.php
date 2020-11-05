<?php
/**
 * @var \App\View\AppView $this
 */
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
    echo $this->Form->control(
        'chooser_type',
        [
            'type' => 'hidden',
            'value' => $chooserType,
        ]
    );

    echo $this->Form->control(
        'chooser',
        [
            'type' => 'hidden',
            'value' => isset($this->getRequest()->query['chooser']),
        ]
    );

    echo $this->Form->control(
        'filter',
        [
            'label' => false,
            'title' => __d('croogo', 'Search'),
            'placeholder' => __d('croogo', 'Search...'),
            'tooltip' => false,
        ]
    );

    echo $this->Form->control(
        __d('croogo', 'Filter'),
        [
            'type' => 'submit',
        ]
    );
    echo $this->Form->end();
    ?>
</div>
