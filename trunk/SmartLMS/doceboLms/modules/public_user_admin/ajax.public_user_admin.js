/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

var fw_d_server_url = '';
var opened_profile = 0;

function setup_directory(passed_server_url) {
	fw_d_server_url = passed_server_url;
}

function getUserProfile(id_user) {

	if(opened_profile != id_user) {

		var data = "op=getuserprofile&id_user=" + id_user;
		var objAjax = new Ajax.Request(
	        	fw_d_server_url,
	        	{method: 'post', parameters: data, onComplete: getUserProfile_complete}
	    );
    }
}

function closeUserProfile(id_user) {
	
	if(id_user != 0) {
		
		var row  = $('user_row_' + id_user);
		row.parentNode.deleteRow( row.rowIndex );
		$('pw_less_usersel_' + id_user).style.display = 'none';
		$('pw_more_usersel_' + id_user).style.display = 'inline';
	}
	if(opened_profile == opened_profile) opened_profile = 0;
}

function getUserProfile_complete(objReq) {
	
	var parsed = objReq.responseText;
	parsed = parsed.evalJSON(true);
	
	var row  = $('user_row_' + parsed.id_user);
	if(opened_profile != 0) closeUserProfile(opened_profile);
	
	var new_row 		= row.parentNode.insertRow(row.rowIndex);
	opened_profile 		= parsed.id_user;
	$('pw_less_usersel_' + opened_profile).style.display = 'inline';
	$('pw_more_usersel_' + opened_profile).style.display = 'none';
	
	var new_cell 		= new_row.insertCell(0);
	new_cell.colSpan 	= row.cells.length;
	new_cell.innerHTML 	= parsed.content;
	new_cell.focus();
} 
