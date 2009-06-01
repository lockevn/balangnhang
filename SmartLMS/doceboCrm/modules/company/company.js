
var run_after='';
var mod_win_interval='';

function focusModWin() {
	try {
		if (mod_window.closed) {
			window.clearInterval(mod_win_interval);
			run_after();
			return;
		}
		mod_window.focus();
	}
	catch (everything) { }
}


function openModWin(url, d_width, d_height, run_after_func) {

	var args='width='+d_width+',height='+d_height+',left=100,top=50,toolbar=0,';
	args+='status=0,menubar=0,scrollbars=1,resizable=0';

	run_after=run_after_func;

	mod_window=window.open(url,'my_mod_win',args);
	mod_win_interval = window.setInterval("focusModWin()",5);
}


function refreshParent() {
	window.location=window.location;
}


function dontDoAnything() {
	return true;
}


function openFormPopup() {
	openModWin('', 1000, 700, refreshParent);
	return false;
}
