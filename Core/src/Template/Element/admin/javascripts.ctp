<?php

if (!$this->getRequest()->is('ajax')) :
    echo $this->Html->scriptBlock('window.isCollapsed = true;');
    echo $this->Layout->js();
    echo $this->Html->script([
        'Croogo/Core.jquery/jquery.min.js',
        'Croogo/Core.core/moment-with-locales',
    ]);
    echo $this->Html->script([
        '//unpkg.com/sleek-dashboard/dist/assets/js/sleek.bundle.js',
        '//pagecdn.io/lib/nprogress/v0.2.0/nprogress.js',
        '//cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js',
        'Croogo/Core.jquery/jquery-ui.min.js',
        //'Croogo/Core.core/popper.min.js',
        //'Croogo/Core.core/bootstrap.min.js',
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
        'Croogo/Core.core/underscore-min',
        'Croogo/Core.core/admin',
    ], [
        'defer' => true,
    ]);
endif;
