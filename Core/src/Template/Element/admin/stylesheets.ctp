<?php

if (!$this->request->is('ajax')):

    echo $this->Html->css([
        'Croogo/Core.core/croogo-admin',
        'Croogo/Core.core/bootstrap-datetimepicker.min',
        'Croogo/Core.core/typeaheadjs',
        'Croogo/Core.core/ekko-lightbox.min.css',
        'Croogo/Core.core/select2.min.css',
        'Croogo/Core.core/select2-bootstrap.min.css',
    ]);

endif;
