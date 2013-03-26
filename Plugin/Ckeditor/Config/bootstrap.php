<?php

/**
 * Hook helper
 */
foreach (Configure::read('Wysiwyg.actions') as $action => $settings) {
	$actionE = explode('/', $action);
	Croogo::hookHelper($actionE['0'], 'Ckeditor.Ckeditor');
}
Croogo::hookHelper('Attachments', 'Ckeditor.Ckeditor');
