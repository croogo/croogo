<?php

if (!$this->request->is('ajax')):

    echo $this->Layout->js();
    echo $this->Html->script([
        'Croogo/Core.jquery/jquery.min.js',
        'Croogo/Core.jquery/jquery-ui.min.js',
        'Croogo/Core.popper.min.js',
        'Croogo/Core.bootstrap.min.js',
        'Croogo/Core.jquery/jquery.slug',
        'Croogo/Core.jquery/jquery.hoverIntent.minified',
        'Croogo/Core.underscore-min',
        'Croogo/Core.bootstrap3-typeahead.min',
        'Croogo/Core.admin',
        'Croogo/Core.sidebar',
        'Croogo/Core.choose',
        'Croogo/Core.moment-with-locales',
        'Croogo/Core.moment-timezone-with-data',
        'Croogo/Core.bootstrap-datetimepicker.min',
        'Croogo/Core.typeahead_autocomplete',
        'Croogo/Core.ekko-lightbox.min.js',
        'Croogo/Core.select2.full.min.js',
    ]);

endif;
