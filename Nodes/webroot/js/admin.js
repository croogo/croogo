/**
 * Nodes
 *
 * for NodesController
 */
var Nodes = {};

/**
 * functions to execute when document is ready
 *
 * only for NodesController
 *
 * @return void
 */
Nodes.documentReady = function () {
  Admin.toggleRowSelection('#NodesCheckAll');
}

Nodes.confirmProcess = function (event) {
  var $el = $(event.currentTarget);
  var action = $($el.data('relatedelement') + ' :selected');
  var confirmMessage = app[$el.data('confirmmessage')];
  var noAction = 'Please select an action';
  if (action.val() == '') {
    confirmMessage = noAction;
  }
  if (confirmMessage == undefined) {
    confirmMessage = 'Are you sure?';
  } else {
    confirmMessage = confirmMessage.replace(/\%s/, action.text());
  }
  if (confirmMessage == noAction) {
    alert(confirmMessage);
  } else {
    if (confirm(confirmMessage)) {
      action.get(0).form.submit();
    }
  }
  return false;
}

/**
 * document ready
 *
 * @return void
 */
$(function () {
  if (Croogo.params.controller == 'Nodes') {
    Nodes.documentReady();
  }
});
