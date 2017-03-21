<?php

if (!$this->request->is('ajax')):

    echo $this->Layout->js();
    echo $this->Html->script([
        'Croogo/Core.jquery/jquery.min.js',
        'Croogo/Core.tether.min.js',
        'Croogo/Core.bootstrap.min.js',
        'Croogo/Core.jquery/jquery.slug',
        'Croogo/Core.jquery/jquery.cookie',
        'Croogo/Core.jquery/jquery.hoverIntent.minified',
        'Croogo/Core.jquery/superfish',
        'Croogo/Core.jquery/supersubs',
        'Croogo/Core.jquery/jquery.elastic-1.6.1.js',
        'Croogo/Core.underscore-min',
        'Croogo/Core.bootstrap3-typeahead.min',
        'Croogo/Core.admin',
        'Croogo/Core.sidebar',
        'Croogo/Core.choose',
        'Croogo/Core.moment-with-locales',
        'Croogo/Core.bootstrap-datetimepicker.min',
        'Croogo/Core.typeahead_autocomplete',
    ]);

endif;
