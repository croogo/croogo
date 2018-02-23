<ul class="pagination justify-content-center my-5">
<?php
    $requestAttributes = $this->request->getAttributes();
    $options = [
        'url' => array_intersect_key($requestAttributes['params'], [
            'slug' => null,
            'pass' => null,
            'plugin' => null,
            'controller' => null,
            'action' => null,
            'type' => null,
        ]),
    ];
	echo $this->Paginator->numbers($options);
?>
</ul>
