
var lang_to_load = new Array();
var actually_loading = 0;
var sUrl = './ajax.loadlang.php';
var loaded = 0;

var setup = function() {

	var table = $('table_list');
	var numof = 0;
	for(var j = 0;j < language.length;j++) {

		for(var i = 0;i < platform.length;i++,numof++) {

			var id = platform[i]+'_'+language[j];
			lang_to_load[numof] = id;

			var row = document.getElementById('table_list').insertRow(table.rows.length);
			row.id = id;
			row.className = (numof%2?'line-1':'line-2');
			var q = row.insertCell(0);
			var x = row.insertCell(1);
			var y = row.insertCell(2);
			var z = row.insertCell(3);
			z.id = id+'_status';
			q.innerHTML = '<img src="./templates/images/language/'+language[j]+'.png" />';
			x.innerHTML = language[j];
			y.innerHTML = platform[i];
			z.innerHTML = 'To load';
			/*
			<tr id="framework_italian" class="line-1">
			    <td>Framework</td>
			    <td>Italian</td>
			    <td id="framework_italian_status">To load</td>
			</tr>*/
		}

	}
}

function $(element) {
	if (arguments.length > 1) {
	for (var i = 0, elements = [], length = arguments.length; i < length; i++)
		elements.push($(arguments[i]));
		return elements;
	}
	if (typeof element == 'string')
		element = document.getElementById(element);
	return element;
}
function load_lang(start_from) {
	
	if(loaded == 0)
	{
		loaded = 1;
		document.getElementById('next_button').innerHTML = '<button type="submit" class="button" id="next" name="next" value="next">' + next_lang + '</button>';
		window.scrollTo(0,0);
	}
	
	if(start_from != undefined) actually_loading = start_from;

	var callback = {
		success: language_loaded,
		failure: handleFailure,
		argument: { loaded: lang_to_load[actually_loading] }
	};

	var transaction = YAHOO.util.Connect.asyncRequest(
			'GET',
			sUrl + '?to_load=' + lang_to_load[actually_loading],
			callback,
			null
		);
 			$(lang_to_load[actually_loading]+'_status').innerHTML = '<img class="waiting" src="./templates/images/indicator.white.gif"> Loading...';
}

var handleFailure = function(o) {
    var div = $('inspect');
    if(o.responseText !== undefined){
        div.innerHTML = "<li>Transaction id: " + o.tId + "</li>";
        div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
        div.innerHTML += "<li>Status code message: " + o.statusText + "</li>";
    }
}
var language_loaded = function(o) {

	try {
	    var prod = YAHOO.lang.JSON.parse(o.responseText);
	    if(prod.result == 'success') {
		   	++actually_loading;
	    	$(o.argument.loaded+'_status').innerHTML = '<img class="waiting" src="./templates/images/complete.png"> Complete ('+prod.exe_time+')';
			if(actually_loading < lang_to_load.length) load_lang();
		} else {
				$(o.argument.loaded+'_status').innerHTML = '<img class="waiting" src="./templates/images/failed.png"> Failed ( '
				+'<a href="#" onclick="load_lang('+actually_loading+'); return false;">'
					+'<img class="waiting" src="./templates/images/retry.png"> Retry</a> '
				+'| <a href="#" onclick="load_lang('+(actually_loading+1)+'); this.parentNode.innerHTML = \'Skipped\'; return false;">'
					+'<img class="waiting" src="./templates/images/skip.png"> Skip</a> )';
		}
	} catch (e) {
				$(o.argument.loaded+'_status').innerHTML = '<img class="waiting" src="./templates/images/failed.png"> Failed ( '
				+'<a href="#" onclick="load_lang('+actually_loading+'); return false;">'
					+'<img class="waiting" src="./templates/images/retry.png"> Retry</a> '
				+'| <a href="#" onclick="load_lang('+(actually_loading+1)+'); this.parentNode.innerHTML = \'Skipped\'; return false;">'
					+'<img class="waiting" src="./templates/images/skip.png"> Skip</a> )';
	}
	
}
