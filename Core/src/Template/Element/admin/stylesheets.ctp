<?php

if (!$this->getRequest()->is('ajax')) :
    echo $this->Html->css([
        '//unpkg.com/sleek-dashboard/dist/assets/css/sleek.min.css',
        'Croogo/Core.core/tempusdominus-bootstrap-4.min',
        'Croogo/Core.core/typeaheadjs',
        'Croogo/Core.core/ekko-lightbox.min.css',
        'Croogo/Core.core/select2.min.css',
        'Croogo/Core.core/select2-bootstrap.min.css',
        'Croogo/Core.core/custom.css',
        '//fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500',
        '//cdn.materialdesignicons.com/3.0.39/css/materialdesignicons.min.css',
    ]);
endif;
