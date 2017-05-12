<?php

if (!$this->request->is('ajax')):

    echo $this->Html->css([
        'Croogo/Core.croogo-admin',
        'Croogo/Core.tether.min.css',
        'Croogo/Core.bootstrap-datetimepicker.min',
        'Croogo/Core.typeaheadjs',
        'Croogo/Core.ekko-lightbox.min.css',
        'Croogo/Core.select2.min.css',
        'Croogo/Core.select2-bootstrap.min.css',
    ]);

endif;
