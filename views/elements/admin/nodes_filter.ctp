<?php
    if (isset($this->params['named']['filter'])) {
        $html->scriptBlock('var filter = 1;', array('inline' => false));
    }
?>
<div class="filter">
<?php
    echo $form->create('Filter');
    $filterType = '';
    if (isset($filters['type'])) {
        $filterType = $filters['type'];
    }
    echo $form->input('Filter.type', array(
        'options' => Set::combine($types, '{n}.Type.alias', '{n}.Type.title'),
        'empty' => true,
        'value' => $filterType,
    ));
    $filterStatus = '';
    if (isset($filters['status'])) {
        $filterStatus = $filters['status'];
    }
    echo $form->input('Filter.status', array(
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
    echo $form->input('Filter.promote', array(
        'label' => __('Promoted', true),
        'options' => array(
            '1' => __('Yes', true),
            '0' => __('No', true),
        ),
        'empty' => true,
        'value' => $filterPromote,
    ));
    echo $form->end(__('Filter', true));
?>
</div>