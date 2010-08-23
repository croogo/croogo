<?php
/**
 * Configuration
 */
    Configure::write('Tinymce.actions', array(
        'Nodes/admin_add' => array(
            array(
                'elements' => 'NodeBody',
            ),
        ),
        'Nodes/admin_edit' => array(
            array(
                'elements' => 'NodeBody',
            ),
        ),
        'Translate/admin_edit' => array(
            array(
                'elements' => 'NodeBody',
            ),
        ),
    ));

/**
 * Hook helper
 */
    foreach (Configure::read('Tinymce.actions') AS $action => $settings) {
        $actionE = explode('/', $action);
        Croogo::hookHelper($actionE['0'], 'Tinymce.Tinymce');
    }
    Croogo::hookHelper('Attachments', 'Tinymce.Tinymce');

?>