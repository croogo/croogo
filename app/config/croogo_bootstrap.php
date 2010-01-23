<?php
/**
 * Locale
 */
    Configure::write('Config.language', 'eng');
/**
 * Admin theme
 */
    //Configure::write('Site.admin_theme', 'sample');
/**
 * Cache configuration
 */
    // models
    Cache::config('setting_write_configuration', array('duration' => '+60 seconds'));

    // components
    Cache::config('croogo_blocks', array('duration' => '+60 seconds'));
    Cache::config('croogo_menus', array('duration' => '+60 seconds'));
    Cache::config('croogo_nodes', array('duration' => '+60 seconds'));
    Cache::config('croogo_types', array('duration' => '+60 seconds'));
    Cache::config('croogo_vocabularies', array('duration' => '+60 seconds'));

    // themes (xml)
    Cache::config('theme_xml', array('duration' => '+60 seconds'));

    // controllers
    Cache::config('nodes_view', array('duration' => '+60 seconds'));
    Cache::config('nodes_promoted', array('duration' => '+60 seconds'));
    Cache::config('nodes_term', array('duration' => '+60 seconds'));
    Cache::config('nodes_index', array('duration' => '+60 seconds'));
    Cache::config('contacts_view', array('duration' => '+60 seconds'));
?>