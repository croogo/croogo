<?php
$url = isset($url) ? $url : ['action' => 'index'];
?>
<?php
echo $this->CroogoForm->create(false, [
    'align' => 'inline',
    'url' => $url,
]);

$this->CroogoForm->templates([
    'label' => false,
    'submitContainer' => '{{content}}',
]);

echo $this->CroogoForm->input('filter', [
    'title' => __d('croogo', 'Search'),
    'placeholder' => __d('croogo', 'Search...'),
    'tooltip' => false,
]);

if (!isset($this->request->query['chooser'])):

    echo $this->CroogoForm->input('type', [
        'options' => $nodeTypes,
        'empty' => __d('croogo', 'Type'),
        'class' => 'c-select',
    ]);

    echo $this->CroogoForm->input('status', [
        'options' => [
            '1' => __d('croogo', 'Published'),
            '0' => __d('croogo', 'Unpublished'),
        ],
        'empty' => __d('croogo', 'Status'),
        'class' => 'c-select',
    ]);

    echo $this->CroogoForm->input('promote', [
        'options' => [
            '1' => __d('croogo', 'Yes'),
            '0' => __d('croogo', 'No'),
        ],
        'empty' => __d('croogo', 'Promoted'),
        'class' => 'c-select',
    ]);

endif;

echo $this->CroogoForm->submit(__d('croogo', 'Filter'), [
    'class' => 'btn-success-outline',
]);
echo $this->CroogoForm->end();
?>
