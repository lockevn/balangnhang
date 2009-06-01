/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * This function set all the checkbox of a specified form
 *
 * @param 	string	form_name	the name of the form
 * @param 	string	check_name	the name of the checkbox i.e. check_name[34]
 * @param 	int		assign		the value to assign, if omitted the checbox value is inverted
 **/

function checkall( form_name, check_name, assign ) {
	var form = document.forms[form_name];
	for (var i = 0; i < form.elements.length; i++) {
		if( form.elements[i].name.indexOf( check_name + "[" ) >= 0 ) 
			if( arguments.length > 2 ) 
				form.elements[i].checked = assign;
			else
				form.elements[i].checked = !form.elements[i].checked;
	}
}

/**
 * This function set all the checkbox of a specified form
 *
 * @param 	string	form_name	the name of the form
 * @param 	string	check_name	the name of the checkbox from the end of the name i.e. [34]
 * @param 	int		assign		the value to assign, if omitted the checbox value is inverted
 **/
 
function checkall_fromback( form_name, check_name, assign ) {
	var form = document.forms[form_name];
	for (var i = 0; i < form.elements.length; i++) {
		if( form.elements[i].name.indexOf( "]" + check_name  ) >= 0 ) 
			if( arguments.length > 2 ) 
				form.elements[i].checked = assign;
			else
				form.elements[i].checked = !form.elements[i].checked;
	}
}


function checkall_meta( form_name, check_name, assign ) {
	var form = document.forms[form_name];
	for (var i = 0; i < form.elements.length; i++) {
		if( form.elements[i].name.indexOf( "_" + check_name + "_" ) >= 0 ) 
			if( arguments.length > 2 ) 
				form.elements[i].checked = assign;
			else
				form.elements[i].checked = !form.elements[i].checked;
	}
}

function checkall_fromback_meta( form_name, check_name, assign ) {
	var form = document.forms[form_name];
	for (var i = 0; i < form.elements.length; i++) {
		if( form.elements[i].name.indexOf( "_" + check_name + "_"  ) >= 0 ) 
			if( arguments.length > 2 ) 
				form.elements[i].checked = assign;
			else
				form.elements[i].checked = !form.elements[i].checked;
	}
}


function showHideForms(base_id, start_type) {
	
	var form 				= document.getElementById(base_id);
	var show_command 		= document.getElementById(base_id + '_command_show');
	var hide_command 		= document.getElementById(base_id + '_command_hide');
	var command_container 	= document.getElementById(base_id + '_command_container');
	
	if(start_type == 'hide') {
		command_container.style.display 	= 'block';
		hide_command.style.display 			= 'none';
		show_command.style.display 			= 'inline';
		form.style.display 			= 'none';
	} else {
		
		command_container.style.display 	= 'block';
		hide_command.style.display 			= 'inline';
		show_command.style.display 			= 'none';
	}
	
	show_command.onclick = function() {
		
		form.style.display 			= 'block';
		hide_command.style.display 	= 'inline';
		show_command.style.display 	= 'none';
		return false;
	}
	
	hide_command.onclick = function() {
		
		form.style.display 			= 'none';
		hide_command.style.display 	= 'none';
		show_command.style.display 	= 'inline';
		return false;
	}
}
