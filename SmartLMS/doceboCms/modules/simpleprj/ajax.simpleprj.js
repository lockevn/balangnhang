/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

var simpleprj_server_url = false;
var simpleprj_active_comments = 0;
var _LANG = new Array();


function setup_simpleprj(passed_server_url) {
	simpleprj_server_url = passed_server_url;

	var data = "op=getLang";
	var objAjax = new Ajax.Request(
        	simpleprj_server_url,
        	{method: 'post', parameters: data, onComplete: setup_simpleprj_complete}
    );
}

function setup_simpleprj_complete(ObjReq) {
	var langText = ObjReq.responseText;
	_LANG = langText.evalJSON(true);
}


function openComment(doc_id, project_id) {

	if (simpleprj_active_comments > 0) {
		$('comment_div_'+simpleprj_active_comments).innerHTML = '';
	}

	if (doc_id != simpleprj_active_comments) {

		simpleprj_active_comments = doc_id;

		var data = "op=comment_it&project_id="+project_id;
		var objAjax = new Ajax.Request(
						simpleprj_server_url,
						{method: 'post', parameters: data, onComplete: openComment_callback}
			);
	}
	else {
		simpleprj_active_comments = 0;
	}
}


function openComment_callback(objReq) {
	//alert(objReq.responseText);
	var parsed = objReq.responseText;
	parsed = parsed.evalJSON(true);
	if(parsed == false) {
		report_error(objReq.responseText);
		return;
	}
	$('comment_div_'+parsed.project_id).innerHTML = parsed.content;
	$('ajaxcomment_textof').value = '';

}


function addajaxcomment() {

	var data = "op=addnewcomment"
//		+ "&doc_id=" + $F('ajaxcomment_ext_key')
		+ "&project_id=" + $F('ajaxcomment_ext_key')
		+ "&reply_to=" + $F('ajaxcomment_reply_to')
		+ "&text_of=" + $F('ajaxcomment_textof');
	var objAjax = new Ajax.Request(
        	simpleprj_server_url,
        	{method: 'post', parameters: data, onComplete: openComment_callback}
    );
}


function delTaskWindow(task_id, project_id, task_title) {


	var name = "window_del_task";
	var title = _LANG._DEL_TITLE_RULE;

	var str="";
	str += _LANG._DEL_CONFIRM_RULE + '<br /><b>' + task_title + '</b>';
	var butt="";
	butt += '<form method="post" action="index.php" onsubmit="delTask(\''+task_id+'\', \''+project_id+'\'); return false;">';
	butt += '<input type="hidden" id="id_rule" name="id_rule" value="' + task_id +'" />';
	butt += '<input type="submit" value="'+_LANG._YES+'" /> ';
	butt += '<input type="button" value="'+_LANG._NO+'" onclick="destroyWindow(\'window_del_task\'); return false;" />';
	butt += '</form>';

	var w = new Window();
	w.id = name;
	w.width = 400;
	w.height = 125;
	w.title = title;
	w.content = str;
	w.buttons = butt;
	w.show();
}

function delTask(task_id, project_id) {

	var data = "op=delTask&task_id="+task_id+"&project_id="+project_id;
	var objAjax = new Ajax.Request(
        	simpleprj_server_url,
        	{method: 'post', parameters: data, onComplete: delTask_callback}
    );

	destroyWindow('window_del_task');
}


function delTask_callback(objReq) {

	var parsed = objReq.responseText;
	parsed = parsed.evalJSON(true);
	if(parsed == false) {
		report_error(objReq.responseText);
		return;
	}
	if (parsed.result) {
		$('task_container').removeChild( $('task_line_'+parsed.task_id) );
	}

}


function showAddTask(project_id) {

	var form = document.createElement('form');
	form.method = 'post';
	form.action = 'index.php';
	form.id = 'task_add_form';
	form.onsubmit = function() { addTask(project_id); return false; };

	form_code ='<textarea id="description" name="description" rows="2" /></textarea><br />';
	form_code+='<input type="submit" value="'+_LANG._YES+'" />';
	form_code+='<input type="button" value="'+_LANG._NO+'" onclick="hideAddForm(); return false;" />';

	form.innerHTML =form_code;

	$('task_container').appendChild(form);

}


function hideAddForm() {

	$('task_container').removeChild( $('task_add_form') );
	Element.toggle($('add_task_link'));

}


function addTask(project_id) {

	var description=$F('description');

	var data = "op=addTask&project_id="+project_id+"&description="+description;
	var objAjax = new Ajax.Request(
        	simpleprj_server_url,
        	{method: 'post', parameters: data, onComplete: addTask_callback}
    );

	$('description').disabled = true;
}


function addTask_callback(objReq) {

	var parsed = objReq.responseText;
	parsed = parsed.evalJSON(true);
	if(parsed == false) {
		report_error(objReq.responseText);
		return;
	}
	if (parsed.result) {
		var new_div = document.createElement('div');
		new_div.innerHTML = parsed.new_item_code;
		new_div.id = 'task_line_'+parsed.task_id;
		$('task_container').insertBefore( new_div, $('task_container').firstChild );
	}

	hideAddForm();
}


function switchTaskStatus(status, task_id, project_id) {

	var data = "op=switchtaskstatus&project_id="+project_id+"&task_id="+task_id+"&status="+status;
	var objAjax = new Ajax.Request(
        	simpleprj_server_url,
        	{method: 'post', parameters: data, onComplete: switchTaskStatus_callback}
    );

}


function switchTaskStatus_callback(objReq) {

	var parsed = objReq.responseText;
	parsed = parsed.evalJSON(true);
	if(parsed == false) {
		report_error(objReq.responseText);
		return;
	}
	if (parsed.result) {

		$('task_status_img_'+parsed.task_id).src =parsed.new_image;
		$('task_status_img_'+parsed.task_id).alt =parsed.new_title;
		$('task_status_img_'+parsed.task_id).title =parsed.new_title;
		$('task_status_link_'+parsed.task_id).onclick =function() { switchTaskStatus(parsed.status, parsed.task_id, parsed.project_id); return false; };
	}
}


function toggleBox(name, img_name, default_class, img_path) {

	current_class =$(name).className;

	if($(name).style.display == 'none') {
		toggle_img ='triangle_down';
		toggle_title =_LANG._HIDE_BOX;
		$(name).className =default_class;
	}
	else {
		toggle_img ='triangle_left';
		toggle_title =_LANG._SHOW_BOX;
		//$(name).className ='hidden_box';
	}

	Effect.toggle(name, 'blind');

	$(img_name).src =img_path+'simpleprj/'+toggle_img+'.gif';
	$(img_name).alt =toggle_title;
	$(img_name).title =toggle_title;
}
