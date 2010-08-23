<?php
/**
 * Configuration
 */
<<<<<<< HEAD
    Configure::write('Tinymce.actions', array(
        'Nodes/admin_add' => array(
            'elements' => 'NodeBody',
        ),
        'Nodes/admin_edit' => array(
            'elements' => 'NodeBody',
        ),
        'Translate/admin_edit' => array(
            'elements' => 'NodeBody',
        ),
    ));
=======
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
>>>>>>> 937b76aadcbf59da40a8b613bba6483e8be2f413

/**
 * Hook helper
 */
    foreach (Configure::read('Tinymce.actions') AS $action => $settings) {
        $actionE = explode('/', $action);
        Croogo::hookHelper($actionE['0'], 'Tinymce.Tinymce');
    }
    Croogo::hookHelper('Attachments', 'Tinymce.Tinymce');

?>