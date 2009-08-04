function nocontextmenu()  // this function only applies to IE4, ignored otherwise.
{
	Event.cancelBubble = true
	Event.returnValue = false;
	return false;
}

function norightclick(e) // This function is used by all others
{
	if (window.Event) { // Netscape
		if (e.which == 2 || e.which == 3) {
			lookupSelected();
			return false;
		}
	} else if (event.button == 2 || event.button == 3) {
		lookupSelected();
		event.cancelBubble = true
		event.returnValue = false;
		return false;
	}
}
function cns_lookup(word_to_lookup)
{
	Modalbox.show("http://e4kid.net/portal/dict/search_frame.php?word=" + word_to_lookup, {title: "Từ điển E4KID", width: 600}); 
}
function getSelectedText() {
	if (window.getSelection) return window.getSelection();
	else if (document.getSelection) return document.getSelection();
	else if (document.selection) {
		var sel = document.selection.createRange().text;
		return sel.replace(new RegExp('([\\f\\n\\r\\t\\v ])+', 'g')," ");
	}
	else return "";
}

function lookupSelected() {
	var txt = getSelectedText();
	if (txt == "") return;
	//alert(txt);
	cns_lookup(txt);
}

if (window.Event) {
	document.captureEvents(Event.MOUSEUP);
}
document.oncontextmenu = nocontextmenu;  // for IE5+
document.onmousedown = norightclick;  // for all others