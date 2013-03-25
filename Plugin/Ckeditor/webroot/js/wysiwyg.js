Croogo.Wysiwyg.choose = function(url, title, description) {
	var params = window.location.href.split('?')[1].split('&');
	var paramsObj = {};
	for (var i in params) {
		var param = params[i];
		var paramE = param.split('=');
		var k = paramE[0];
		var v = paramE[1];
		paramsObj[k] = v;
	}

	if (typeof paramsObj['CKEditorFuncNum'] != 'undefined') {
		window.top.opener.CKEDITOR.tools.callFunction(paramsObj['CKEditorFuncNum'], Croogo.Wysiwyg.uploadsPath + url);
		window.top.close();
	}
}