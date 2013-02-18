Croogo.Wysiwyg.choose = function(url, title, description) {
	if (url == '') return false;

	url = Croogo.Wysiwyg.uploadsPath + url;

	desc_field = window.top.opener.browserWin.document.forms[0].elements[2];
	if (typeof description !== 'undefined') {
		desc_field.value = description;
	}

	title_field = window.top.opener.browserWin.document.forms[0].elements[3];
	if (typeof title !== 'undefined') {
		title_field.value = title;
	}

	field = window.top.opener.browserWin.document.forms[0].elements[window.top.opener.browserField];
	field.value = url;
	if (field.onchange != null) {
		field.onchange();
	}
	window.top.close();
	window.top.opener.browserWin.focus();
};

Croogo.Wysiwyg.browser = function() {
	window.fileBrowserCallBack = function(field_name, url, type, win) {
		browserField = field_name;
		browserWin = win;
		window.open(Croogo.Wysiwyg.attachmentsPath, 'browserWindow', 'modal,width=960,height=700,scrollbars=yes');
	}
};