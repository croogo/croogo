<?php

if (!$this->getRequest()->is('ajax')) :
    echo $this->Layout->js();
    echo $this->Html->script([
        'Croogo/Core.jquery/jquery.min.js',
        'Croogo/Core.core/moment-with-locales',
        'Croogo/Core.core/underscore-min',
    ]);
    echo $this->Html->script([
        'Croogo/Core.jquery/jquery-ui.min.js',
        'Croogo/Core.core/popper.min.js',
        'Croogo/Core.core/bootstrap.min.js',
        '//cdn.jsdelivr.net/npm/transliteration@2.1.8/dist/browser/bundle.umd.min.js',
        'Croogo/Core.jquery/jquery.slug',
        'Croogo/Core.jquery/jquery.hoverIntent.minified',
        'Croogo/Core.core/bootstrap3-typeahead.min',
        'Croogo/Core.core/moment-timezone-with-data',
        'Croogo/Core.core/tempusdominus-bootstrap-4.min',
        'Croogo/Core.core/typeahead_autocomplete',
        'Croogo/Core.core/ekko-lightbox.min.js',
        'Croogo/Core.core/select2.full.min.js',
        'Croogo/Core.core/sidebar',
        'Croogo/Core.core/choose',
        'Croogo/Core.core/modal',
    ], [
        'async' => true,
    ]);
    echo $this->Html->script([
        'Croogo/Core.core/admin',
    ], [
        'defer' => true,
    ]);
endif;
