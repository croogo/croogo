<?php

/**
 * Hook helper
 */
foreach ((array)Configure::read('Wysiwyg.actions') as $action => $settings) {
	$actionE = explode('/', $action);
	Croogo::hookHelper($actionE['0'], 'Tinymce.Tinymce');
}
Croogo::hookHelper('Attachments', 'Tinymce.Tinymce');
