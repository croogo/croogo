/**
 * Users
 *
 * for UsersController
 */
var Users = {};

/**
 * functions to execute when document is ready
 *
 * only for NodesController
 *
 * @return void
 */
Users.documentReady = function () {
};

Users.statusControl = function() {
  $('#status').on('change', function (e) {
    var $passwords = $('#passwords');
    var $elements = $passwords.find(':input');
    $elements.prop('disabled', !this.checked);
    if (this.checked) {
      $passwords.slideDown('fast');
    } else {
      $passwords.slideUp('fast');
    }
  });
  $('#notification').on('change', function (e) {
    var $status = $('#status');
    $status.attr('checked', false);
    $status.trigger('change').closest('.checkbox').slideToggle('fast');
  });
};

Users.generateToken = function() {
  function toHexString(byteArray) {
    return Array.prototype.map.call(byteArray, function(byte) {
      return ('0' + (byte & 0xFF).toString(16)).slice(-2);
    }).join('');
  }
  var array = new Uint32Array(20);
  window.crypto.getRandomValues(array);
  var hexString = toHexString(array);
  return hexString;
};

/**
 * document ready
 *
 * @return void
 */
$(document).ready(function () {
  if (Croogo.params.controller == 'Users') {
    Users.documentReady();

    if (Croogo.params.action == 'add' && Croogo.params.prefix == 'Admin') {
      Users.statusControl();
    }
  }

  Admin.toggleRowSelection('#UsersCheckAll');
});
