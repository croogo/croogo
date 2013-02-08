//
//  jQuery Slug Plugin by Perry Trinier (perrytrinier@gmail.com)
//  MIT License: http://www.opensource.org/licenses/mit-license.php

jQuery.fn.slug = function(options) {
	var settings = {
		slug: 'slug', // Class used for slug destination input and span. The span is created on $(document).ready()
		hide: true    // Boolean - By default the slug input field is hidden, set to false to show the input field and hide the span.
	};

	if(options) {
		jQuery.extend(settings, options);
	}

	var $this = $(this);

	$(document).ready( function() {
		if (settings.hide) {
			$('input.' + settings.slug).after("<span class="+settings.slug+"></span>");
			$('input.' + settings.slug).hide();
		}
	});

	var transliterate = function(str) {
		var rExps=[
			{re: /ä|æ|ǽ/g, ch: 'ae'},
			{re: /ö|œ/g, ch: 'oe'},
			{re: /ü/g, ch: 'ue'},
			{re: /Ä/g, ch: 'Ae'},
			{re: /Ü/g, ch: 'Ue'},
			{re: /Ö/g, ch: 'Oe'},
			{re: /À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ/g, ch: 'A'},
			{re: /à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/g, ch: 'a'},
			{re: /Ç|Ć|Ĉ|Ċ|Č/g, ch: 'C'},
			{re: /ç|ć|ĉ|ċ|č/g, ch: 'c'},
			{re: /Ð|Ď|Đ/g, ch: 'D'},
			{re: /ð|ď|đ/g, ch: 'd'},
			{re: /È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/g, ch: 'E'},
			{re: /è|é|ê|ë|ē|ĕ|ė|ę|ě/g, ch: 'e'},
			{re: /Ĝ|Ğ|Ġ|Ģ/g, ch: 'G'},
			{re: /ĝ|ğ|ġ|ģ/g, ch: 'g'},
			{re: /Ĥ|Ħ/g, ch: 'H'},
			{re: /ĥ|ħ/g, ch: 'h'},
			{re: /Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/g, ch: 'I'},
			{re: /ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/g, ch: 'i'},
			{re: /Ĵ/g, ch: 'J'},
			{re: /ĵ/g, ch: 'j'},
			{re: /Ķ/g, ch: 'K'},
			{re: /ķ/g, ch: 'k'},
			{re: /Ĺ|Ļ|Ľ|Ŀ|Ł/g, ch: 'L'},
			{re: /ĺ|ļ|ľ|ŀ|ł/g, ch: 'l'},
			{re: /Ñ|Ń|Ņ|Ň/g, ch: 'N'},
			{re: /ñ|ń|ņ|ň|ŉ/g, ch: 'n'},
			{re: /Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/g, ch: 'O'},
			{re: /ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/g, ch: 'o'},
			{re: /Ŕ|Ŗ|Ř/g, ch: 'R'},
			{re: /ŕ|ŗ|ř/g, ch: 'r'},
			{re: /Ś|Ŝ|Ş|Š/g, ch: 'S'},
			{re: /ś|ŝ|ş|š|ſ/g, ch: 's'},
			{re: /Ţ|Ť|Ŧ/g, ch: 'T'},
			{re: /ţ|ť|ŧ/g, ch: 't'},
			{re: /Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/g, ch: 'U'},
			{re: /ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/g, ch: 'u'},
			{re: /Ý|Ÿ|Ŷ/g, ch: 'Y'},
			{re: /ý|ÿ|ŷ/g, ch: 'y'},
			{re: /Ŵ/g, ch: 'W'},
			{re: /ŵ/g, ch: 'w'},
			{re: /Ź|Ż|Ž/g, ch: 'Z'},
			{re: /ź|ż|ž/g, ch: 'z'},
			{re: /Æ|Ǽ/g, ch: 'AE'},
			{re: /ß/g, ch: 'ss'},
			{re: /Ĳ/g, ch: 'IJ'},
			{re: /ĳ/g, ch: 'ij'},
			{re: /Œ/g, ch: 'OE'},
			{re: /ƒ/g, ch: 'f'}
		];
		for (var i=0, len=rExps.length; i<len; i++) {
			str = str.replace(rExps[i].re, rExps[i].ch);
		}
		return str;
	}

	makeSlug = function() {
		var slug = transliterate(jQuery.trim($this.val()))
			.replace(/\s+/g,'-').replace(/[^a-zA-Z0-9\-]/g,'').toLowerCase()
			.replace(/\-{2,}/g,'-')
			.replace(/\-$/, '')
			.replace(/^\-/, '')
			;
		$('input.' + settings.slug).val(slug);
		$('span.' + settings.slug).text(slug);
	}

	$(this).keyup(makeSlug);

	return $this;
};