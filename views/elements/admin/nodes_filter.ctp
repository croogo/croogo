<?php
    if (isset($this->params['named']['filter'])) {
        $this->Html->scriptBlock('var filter = 1;', array('inline' => false));
    }
?>
<div class="filter">
<?php
    echo $this->Form->create('Filter');
    $filterType = '';
    if (isset($filters['type'])) {
        $filterType = $filters['type'];
    }
    echo $this->Form->input('Filter.type', array(
        'options' => Set::combine($types, '{n}.Type.alias', '{n}.Type.title'),
        'empty' => true,
        'value' => $filterType,
    ));
    $filterStatus = '';
    if (isset($filters['status'])) {
        $filterStatus = $filters['status'];
    }
    echo $this->Form->input('Filter.status', array(
        'options' => array(
            '1' => __('Published', true),
            '0' => __('Unpublished', true),
        ),
        'empty' => true,
        'value' => $filterStatus,
    ));
    $filterPromote = '';
    if (isset($filters['promote'])) {
        $filterPromote = $filters['promote'];
    }
    echo $this->Form->input('Filter.promote', array(
        'label' => __('Promoted', true),
        'options' => array(
            '1' => __('Yes', true),
            '0' => __('No', true),
        ),
        'empty' => true,
        'value' => $filterPromote,
    ));

    $filterSearch = '';
    if (isset($this->params['named']['q'])) {
        $filterSearch = $this->params['named']['q'];
    }
    echo $form->input('Filter.q', array(
        'label' => __('Search', true),
        'value' => $filterSearch,
    ));
    echo $this->Form->end(__('Filter', true));
?>
    <div class="clear">&nbsp;</div>
</div>