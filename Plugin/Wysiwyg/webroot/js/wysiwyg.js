/**
 * Every rich text editor plugin is expected to come with a wysiwyg.js file,
 * and should follow the same structure.
 *
 * This makes sure there is consistency among multiple RTE plugins.
 */
if (typeof Croogo.Wysiwyg == 'undefined') {
	// Croogo.uploadsPath and Croogo.attachmentsPath is set from Helper anyways
	Croogo.Wysiwyg = {
		uploadsPath: Croogo.basePath + 'uploads/',
		attachmentsPath: Croogo.basePath + 'file_manager/attachments/browse'
	};
}

/**
 * This function is called when you select an image file to be inserted in your editor.
 */
Croogo.Wysiwyg.choose = function(url, title, description) {

};

/**
 * This function is responsible for integrating attachments/file browser in the editor.
 */
Croogo.Wysiwyg.browser = function() {

};

if (typeof jQuery != 'undefined') {
	$(document).ready(function() {
		Croogo.Wysiwyg.browser();
	});
}
