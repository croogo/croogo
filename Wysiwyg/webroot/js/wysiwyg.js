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
    attachmentsPath: Croogo.basePath + 'admin/file-manager/attachments/browse'
  };
}

/**
 * This function is called when you select an image file to be inserted in your editor.
 */
Croogo.Wysiwyg.choose = function(url, title, description) {

};

/**
 * Returns boolean value to indicate an editor within the page has been modified
 */
Croogo.Wysiwyg.isDirty = function() {
};

/**
 * Reset dirty indicator for all editors in the page
 */
Croogo.Wysiwyg.resetDirty = function() {
};

/**
 * This function is responsible for integrating attachments/file browser in the editor.
 */
Croogo.Wysiwyg.browser = function() {
};

Croogo.Wysiwyg._$btnContainer = {};

Croogo.Wysiwyg._addButtonContainer = function($el) {
  var elementId = $el.attr('id');
  if (Croogo.Wysiwyg._$btnContainer[elementId] == null) {
    var $btnContainer = $el.siblings('.btn-group');
    if ($btnContainer.length == 0) {
        $btnContainer = $('<div class="btn-group float-right"/>');
    }
    $btnContainer.insertBefore($el);
    Croogo.Wysiwyg._$btnContainer[elementId] = $btnContainer;
  }
};

Croogo.Wysiwyg.addButton = function($el, title, onButtonClicked) {
  var elementId = $el.attr('id');
  Croogo.Wysiwyg._addButtonContainer($el);
  var $btn = $('<button type="button"/>')
    .attr('class', 'btn btn-sm btn-outline-secondary')
    .html(title)
    .on('click', onButtonClicked);
  Croogo.Wysiwyg._$btnContainer[elementId].append($btn);
  var buttons = $('.btn', Croogo.Wysiwyg._$btnContainer[elementId]);
  if (buttons.length == 1) {
    setTimeout(function() {
      $('.btn:first-child', Croogo.Wysiwyg._$btnContainer[elementId])
        .trigger('click');
    }, 300);
  }
};

if (typeof jQuery != 'undefined') {
  $(document).ready(function() {
    Croogo.Wysiwyg.browser();
  });
}
