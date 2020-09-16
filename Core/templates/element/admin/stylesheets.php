<?php

if (!$this->getRequest()->is('ajax')) :
    echo $this->Html->css([
        'Croogo/Core.core/croogo-admin',
        'Croogo/Core.core/tempusdominus-bootstrap-4.min',
        'Croogo/Core.core/typeaheadjs',
        'Croogo/Core.core/ekko-lightbox.min.css',
        'Croogo/Core.core/select2.min.css',
        'Croogo/Core.core/select2-bootstrap.min.css',
        'Croogo/Core.core/custom.css',
    ]);
endif;
