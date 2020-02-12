var Assets = {};

Assets.reloadAssetsTab = function(e) {
  e && e.preventDefault();
  var $tab = $('a[data-toggle="tab"][href$="-assets"]');
  var url = $('.asset-list').data('url');
  var loadingMsg = '<span><i class="' + Admin.iconClass('spinner') + ' fa-spin"></i> Loading. Please wait...</span>';
  $tab.tab('show');
  $($tab.attr('href'))
    .html(loadingMsg)
    .load(url);
  return false;
};

Assets.popup = function(e) {
  e && e.preventDefault();
  var width = window.screen.width > 1024 ? 1024 : 800;
  var height = 600;
  var screenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
  var screenTop = window.screenTop != undefined ? window.screenTop : screen.top;
  var left = (screen.width / 2) - (width / 2) + screenLeft;
  var top = (screen.height / 4) - (height / 4) + screenTop;
  var url = e.currentTarget.attributes['href'].value;
  var options = 'menubar=no,resizable=yes,chrome=yes,centerScreen=yes,scrollbars=yes' +
    ',top=' + top + ',left=' + left +
    ',width=' + width + ',height=' + height;
  var $tab = $('a[data-toggle="tab"][href$="-assets"]').tab('show');
  window.open(url, 'Asset Browser', options).focus();
  return false;
};

Assets.changeUsageType = function(e) {
  var $target = $(e.currentTarget);
  var type = 'FeaturedImage';

  e && e.preventDefault();

  var curValue = $target.val();
  var postData = {
    pk: $target.data('pk'),
    value: curValue,
  };
  $.post({
    url: $target.data('url'),
    data: postData,
    headers: {
      'Accept': 'application/json',
      'X-CSRF-Token': Admin.getCookie('csrfToken'),
    },
    success: function(data, textStatus) {
      if ($target.hasClass('select2-hidden-accessible')) {
        $target.select2('destroy');
      }
      if (curValue) {
        $target.html('<option value="' + curValue + '">' + curValue + '</option>')
      } else {
        $target.html('');
      }
      $target.select2()
    },
  });
  return false;
};

Assets.setFeaturedImage = function(e) {
  var $target = $(e.currentTarget);
  var pk = $target.data('pk');
  var newValue = 'FeaturedImage';
  var $select = $('.change-usage-type[data-pk=' + pk + ']');

  var curValue = $select.val();
  if (curValue && curValue != newValue) {
    if (!confirm('Type already set. Overwrite?')) {
      return false;
    }
  }

  if ($select.hasClass('select2-hidden-accessible')) {
    $select.select2('destroy');
  }

  $select
    .html('<option value="' + newValue + '">' + newValue + '</option>')
    .val(newValue)
    .change()
    .select2();

  e && e.preventDefault();
  return false;
};

Assets.unregisterAssetUsage = function(e) {
  e && e.preventDefault();
  var $target = $(e.currentTarget);
  var postData = {
    id: $target.data('id')
  };
  $('.tooltip').tooltip('hide');
  $.post({
    url: $target.attr('href'),
    data: postData,
    headers: {
      'Accept': 'application/json',
      'X-CSRF-Token': Admin.getCookie('csrfToken'),
    },
    success: function(data, textStatus) {
      if (data == true) {
        $target.parents('tr').hide('medium', function() {
          $(this).remove();
        });
      }
    },
  });
  return false;
};

Assets.resizeAsset = function(e) {
  e && e.preventDefault();

  var inputWidth = prompt('Resize to width: ', 300)
  if (inputWidth === null) {
    return;
  }
  var width = parseInt(inputWidth);
  if (isNaN(width)) {
    return alert('Invalid number');
  }

  var $target = $(e.currentTarget);
  var postData = {
    width: width
  };
  $.ajax({
    method: 'post',
    url: $target.attr('href'),
    data: postData,
    accepts: {
      'json': 'application/json',
    },
    success: function(data, textStatus) {
      if (textStatus === 'success') {
        if (typeof data === 'string') {
          return alert(data);
        }
        return prompt("Asset id: "+ data.id + " created", data.path);
      }
    }
  })
  .fail(function(xhr, textStatus, errorThrown) {
    console.log(xhr, textStatus, errorThrown);
    if (typeof xhr.responseJSON.message !== 'undefined') {
      return alert(xhr.responseJSON.message);
    }
    return alert(errorThrown);
  });
  return false;
};

Assets.init = function() {
  var $body = $('body');
  $body.on('click', 'a[data-toggle=browse]', Assets.popup);
  $body.on('click', 'a[data-toggle=refresh]', Assets.reloadAssetsTab);
  $body.on('click', 'a[data-toggle=resize-asset]', Assets.resizeAsset);
  $body.on('change', '.change-usage-type', Assets.changeUsageType);
  $body.on('click', 'a.unregister-usage', Assets.unregisterAssetUsage);
  $body.on('click', 'a.set-featured-image', Assets.setFeaturedImage);
};
