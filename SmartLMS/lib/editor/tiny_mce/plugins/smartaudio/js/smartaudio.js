tinyMCEPopup.requireLangPack();

var oldWidth, oldHeight, ed, url;

if (url = tinyMCEPopup.getParam("media_external_list_url"))
	document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');

function init() {
	var pl = "", f, val;
	var type = "flash", fe, i;

	ed = tinyMCEPopup.editor;

	tinyMCEPopup.resizeToInnerSize();
	f = document.forms[0]

	//selectByValue(f, 'media_type', type);
	//changedType(type);
	//updateColor('bgcolor_pick', 'bgcolor');

	TinyMCE_EditableSelects.init();
	//generatePreview();
}

function insertMedia() {
	var fe, f = document.forms[0], h;

	tinyMCEPopup.restoreSelection();

	if (!AutoValidator.validate(f)) {
		tinyMCEPopup.alert(ed.getLang('invalid_data'));
		return false;
	}

	fe = ed.selection.getNode();
	
	h = '<a href="';
	h += f.src.value;
	h += '">';
	h += '<img src="/lib/editor/tiny_mce/plugins/smartaudio/img/audio.gif" />';
	h += '</a>'
	
	/*h = '<img src="' + tinyMCEPopup.getWindowArg("plugin_url") + '/img/trans.gif"' ;
	h += '<div>';
	h += '<div id = "test"><img src = "/lib/editor/tiny_mce/plugins/smartaudio/img/audio.gif"/></div>';
	h += '<script type="text/javascript"> AudioPlayer.embed("test",;{soundFile: "'
	h += f.src.value;
	h += 	'"});</script>';
	h += '</div>'
	*/
	ed.execCommand('mceInsertContent', false, h);

	tinyMCEPopup.close();
}

function updatePreview() {
	var f = document.forms[0], type;

	f.width.value = f.width.value || '320';
	f.height.value = f.height.value || '240';

	type = getType(f.src.value);
	selectByValue(f, 'media_type', type);
	changedType(type);
	generatePreview();
}

function generatePreview(c) {
	var f = document.forms[0], p = document.getElementById('prev'), h = '', cls, pl, n, type, codebase, wp, hp, nw, nh;

	p.innerHTML = '<!-- x --->';

	nw = parseInt(f.width.value);
	nh = parseInt(f.height.value);

	if (f.width.value != "" && f.height.value != "") {
		if (f.constrain.checked) {
			if (c == 'width' && oldWidth != 0) {
				wp = nw / oldWidth;
				nh = Math.round(wp * nh);
				f.height.value = nh;
			} else if (c == 'height' && oldHeight != 0) {
				hp = nh / oldHeight;
				nw = Math.round(hp * nw);
				f.width.value = nw;
			}
		}
	}

	if (f.width.value != "")
		oldWidth = nw;

	if (f.height.value != "")
		oldHeight = nh;

	// After constrain
	pl = serializeParameters();

	switch (f.media_type.options[f.media_type.selectedIndex].value) {
		case "flash":
			cls = 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000';
			codebase = 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0';
			type = 'application/x-shockwave-flash';
			break;

		case "shockwave":
			cls = 'clsid:166B1BCA-3F9C-11CF-8075-444553540000';
			codebase = 'http://download.macromedia.com/pub/shockwave/cabs/director/sw.cab#version=8,5,1,0';
			type = 'application/x-director';
			break;

		case "qt":
			cls = 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B';
			codebase = 'http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0';
			type = 'video/quicktime';
			break;

		case "wmp":
			cls = ed.getParam('media_wmp6_compatible') ? 'clsid:05589FA1-C356-11CE-BF01-00AA0055595A' : 'clsid:6BF52A52-394A-11D3-B153-00C04F79FAA6';
			codebase = 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701';
			type = 'application/x-mplayer2';
			break;

		case "rmp":
			cls = 'clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA';
			codebase = 'http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701';
			type = 'audio/x-pn-realaudio-plugin';
			break;
	}

	if (pl == '') {
		p.innerHTML = '';
		return;
	}

	pl = tinyMCEPopup.editor.plugins.media._parse(pl);

	if (!pl.src) {
		p.innerHTML = '';
		return;
	}

	pl.src = tinyMCEPopup.editor.documentBaseURI.toAbsolute(pl.src);
	pl.width = !pl.width ? 100 : pl.width;
	pl.height = !pl.height ? 100 : pl.height;
	pl.id = !pl.id ? 'obj' : pl.id;
	pl.name = !pl.name ? 'eobj' : pl.name;
	pl.align = !pl.align ? '' : pl.align;

	// Avoid annoying warning about insecure items
	if (!tinymce.isIE || document.location.protocol != 'https:') {
		h += '<object classid="' + cls + '" codebase="' + codebase + '" width="' + pl.width + '" height="' + pl.height + '" id="' + pl.id + '" name="' + pl.name + '" align="' + pl.align + '">';

		for (n in pl) {
			h += '<param name="' + n + '" value="' + pl[n] + '">';

			// Add extra url parameter if it's an absolute URL
			if (n == 'src' && pl[n].indexOf('://') != -1)
				h += '<param name="url" value="' + pl[n] + '" />';
		}
	}

	h += '<embed type="' + type + '" ';

	for (n in pl)
		h += n + '="' + pl[n] + '" ';

	h += '></embed>';

	// Avoid annoying warning about insecure items
	if (!tinymce.isIE || document.location.protocol != 'https:')
		h += '</object>';

	p.innerHTML = "<!-- x --->" + h;
}

tinyMCEPopup.onInit.add(init);
