Dropzone.autoDiscover = false;

Dropzone.createElement = function (string) {
  var div;
  if (string.substr(0, 3) == '<tr') {
    // Table elements can not be wrapped into a div
    div = document.createElement("tbody");
  } else {
    div = document.createElement("div");
  }
  div.innerHTML = string;
  return div.childNodes[0];
};

jQuery(function ($) {
  var $body = $('body');
  var baseUrl = $('#base-url').text();
  var $tokenFields = $('#tokens').find('input');
  var $dropzoneTarget = $('#dropzone-target');
  var tokens = {};
  $tokenFields.each(function () {
    tokens[this.name] = this.value;
  });

  var dropzone = new Dropzone(document.body, {
    url: $dropzoneTarget.data('url'),
    previewsContainer: ".table tbody",
    clickable: false,
    previewTemplate: $('#dropzone-preview').html(),
    addRemoveLinks: false,
    params: tokens,
    dragstart: function () {
      $body.addClass('dragging');
    },
    dragenter: function () {
      $body.addClass('dragging');
    },
    dragleave: function (e) {
      if (e.target.id === 'dropzone-target' || e.target.tagName.toLowerCase() === 'body') {
        $body.removeClass('dragging');
      }
    },
    dragend: function () {
      $body.removeClass('dragging');
    },
    drop: function () {
      $body.removeClass('dragging');
    },
    success: function (file, response) {
      var _ref, _i, _len;
      if (file.previewElement) {
        //Set the ids
        $(file.previewElement).find('[data-dz-id]').text(response.data.id);
        $(file.previewElement).find('[data-dz-path]')
          .attr('href', baseUrl + response.data.path)
          .text(baseUrl + response.data.path);

        //Remove the progress bar
        $(file.previewElement).find('[data-dz-uploadprogress]').remove();
      }
    },
    error: function (file, message) {
      var _results;
      if (file.previewElement) {
        var $previewElement = $(file.previewElement);
        $previewElement.add('dz-error');
        if (typeof message !== "String" && message.message) {
          message = message.message;
        }
        $(file.previewElement).find('[data-dz-errormessage]')
          .each(function () {
            _results.push($(this).text(message).get(0));
          });
        return _results;
      }
    },
  });
});
