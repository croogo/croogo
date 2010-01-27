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
    $cacheDuration = '+1 hour';

    // models
    Cache::config('setting_write_configuration', array('duration' => $cacheDuration));

    // components
    Cache::config('croogo_blocks', array('duration' => $cacheDuration));
    Cache::config('croogo_menus', array('duration' => $cacheDuration));
    Cache::config('croogo_nodes', array('duration' => $cacheDuration));
    Cache::config('croogo_types', array('duration' => $cacheDuration));
    Cache::config('croogo_vocabularies', array('duration' => $cacheDuration));

    // themes (xml)
    Cache::config('theme_xml', array('duration' => $cacheDuration));

    // controllers
    Cache::config('nodes_view', array('duration' => $cacheDuration));
    Cache::config('nodes_promoted', array('duration' => $cacheDuration));
    Cache::config('nodes_term', array('duration' => $cacheDuration));
    Cache::config('nodes_index', array('duration' => $cacheDuration));
    Cache::config('contacts_view', array('duration' => $cacheDuration));
?>