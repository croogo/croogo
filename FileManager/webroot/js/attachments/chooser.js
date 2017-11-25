$(function() {
  $('.popovers').popover().on('click', function (e) {
    e.preventDefault();
  });

  var $body = $('body');
  var $dropzoneTarget = $('#dropzone-target');
  var $tokenFields = $('#tokens').find('input');
  var baseUrl = $dropzoneTarget.data('baseUrl');
  var tokens = {};
  $tokenFields.each(function () {
    tokens[this.name] = this.value;
  });

  var dropzone = new Dropzone(document.body, {
    url: $dropzoneTarget.data('url'),
    previewsContainer: "#dropzone-previews",
    clickable: '.add-image',
    previewTemplate: $('#dropzone-preview').html(),
    addRemoveLinks: false,
    params: tokens,
    thumbnailWidth: 250,
    thumbnailHeight: null,
    init: function () {
      this.on("addedfile", function (file) {
        file.previewElement.remove();
        this.previewsContainer.prepend(file.previewElement);
      });
    },
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
    sending: function (file, xhr, formData) {
      xhr.setRequestHeader('X-CSRF-Token', $dropzoneTarget.data('csrfToken'))
    },
    success: function (file, response) {
      var _ref, _i, _len;
      if (file.previewElement) {
        //Set the ids
        var $preview = $(file.previewElement);
        $preview
          .data({
            chooserType: 'node',
            chooserId: response.data.id,
            chooserTitle: response.data.title,
          })
          .attr('rel', baseUrl + response.data.path);

        $preview.find('progress').remove();
        $preview.find('.file-size').remove();
        $preview.find('.file-slug').remove()
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

  $('#dropzone-previews').closest('.modal').on('hide.bs.modal', function () {
    dropzone.destroy();
  });
});
