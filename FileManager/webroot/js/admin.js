var Attachments = {};

Attachments.confirmProcess = function (event) {
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
};

Attachments.init = function() {
  Admin.toggleRowSelection('#AttachmentsCheckAll');
};
