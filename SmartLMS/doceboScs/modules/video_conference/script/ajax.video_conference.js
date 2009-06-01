/*======================================================================*/
/* DOCEBO - The E-Learning Suite										*/
/* ==================================================================== */
/* 																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com/												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/*======================================================================*/

var PING_SECOND = 1;

var _vc_serverurl = false;

var flash_path = '';
var templ_path = '';

var interval_ping;

var latency = new Array();

var resource_number = 0;

var second_counter = 0;

var logged_succ = false;

Date.prototype.getDateTime = function() {

	return this.getFullYear() + "-"
		+ ( this.getMonth() 	< 9	 ? "0" + (this.getMonth()+1) 	: (this.getMonth()+1) ) + "-"
		+ ( this.getDate() 		< 10 ? "0" + this.getDate() 		: this.getDate() ) + " "
		+ ( this.getHours() 	< 10 ? "0" + this.getHours() 		: this.getHours() ) + ":"
		+ ( this.getMinutes() 	< 10 ? "0" + this.getMinutes() 		: this.getMinutes() ) + ":"
		+ ( this.getSeconds() 	< 10 ? "0" + this.getSeconds() 		: this.getSeconds() );
}


function debug(str) {
	var new_div = document.createElement('div');
	new_div.innerHTML = str;
	$('debug').appendChild(new_div);
}

function VideoConferenceSetup( tpl_path, fsh_path ) {
	
	_vc_serverurl = './ajax.video_conference.php';
	
	templ_path = tpl_path;
	flash_path = fsh_path;

	Chat_VR.init_resource();
	User_VR.init_resource();
	Room_VR.init_resource();
	if(Chat_VR.latency != undefined) {
		
		latency[0] = new Object();
		latency[0].resource = 'chat';
		latency[0].value = Chat_VR.latency;
	}
	if(User_VR.latency != undefined) {
		
		latency[1] = new Object();
		latency[1].resource = 'user';
		latency[1].value = User_VR.latency;
	}
	resource_number = 2;
	interval_ping = setInterval("ping_for_news();", PING_SECOND * 1000);
	
	Event.observe(window, 'unload', VideoConferenceLogout, false);
}

function VideoConferenceLogout() {
	
	if(interval_ping != undefined && interval_ping != false) clearInterval(interval_ping);
	disconnectUser();
	disconnectDrawboard();
	User_VR.logoutUser();
}

function ping_for_news() {
	
	if(!logged_succ) return;
	
	var	request = new Array();
	for(var i = 0; i < resource_number;i++) {
		
		if((second_counter % latency[i].value) == 0 || second_counter == 0) {
			
			switch(latency[i].resource) {
				case "chat" : { request[0] = Chat_VR.get_param_for_action_ping(); };break;
				case "user" : { request[1] = User_VR.get_param_for_action_ping(); };break;
				case "room" : { request[2] = Room_VR.get_param_for_action_ping(); };break;
			}
		}
	}
	second_counter += PING_SECOND;
	if(request.length != undefined && request.length != null && request.length != 0) {
		perform_ajax_ping(Object.toJSON(request));
	}
}

function perform_ajax_ping(action_data) {
	
	var data = "requested_action=ping"
			+ "&action_data=" + encodeURIComponent(action_data);
	
	var objAjax = new Ajax.Request(
        	_vc_serverurl,
        	{method: 'post', parameters: data, onSuccess: perform_ajax_ping_success, onFailure: server_connection_failure }
    );   
}

function perform_ajax_ping_success(objReq) {
	//debug(objReq.responseText);
	var parsed = objReq.responseText;
	parsed = parsed.evalJSON(true);
	
	for(var i = 0; i < parsed.length;i++) {
		
		switch(parsed[i].resource) {
			case "chat" : { Chat_VR.perform_action_result(parsed[i].action_idref, parsed[i].result); };break;
			case "user" : { User_VR.perform_action_result(parsed[i].action_idref, parsed[i].result); };break;
			case "room" : { Room_VR.perform_action_result(parsed[i].action_idref, parsed[i].result); };break;
		}
	}
	
}

function perform_ajax_request(resource_type, action_idref, action_data) {
	
	var data = "requested_action=shot"
			+ "&resource=" + resource_type
			+ "&action_idref=" + action_idref
			+ "&action_data=" + encodeURIComponent(Object.toJSON(action_data));
	
	var objAjax = new Ajax.Request(
        	_vc_serverurl,
        	{method: 'post', parameters: data, onSuccess: perform_ajax_request_success, onFailure: server_connection_failure }
    );
}

function perform_ajax_request_success(objReq, parsed) {
	//debug(objReq.responseText);
	var parsed = objReq.responseText;
	parsed = parsed.evalJSON(true);
	
	if(parsed == false) server_connection_failure();
	
	switch(parsed.resource) {
		case "chat" : { Chat_VR.perform_action_result(parsed.action_idref, parsed.result); };break;
		case "user" : { User_VR.perform_action_result(parsed.action_idref, parsed.result); };break;
		case "room" : { Room_VR.perform_action_result(parsed.action_idref, parsed.result); };break;
	}
}

function server_connection_failure() {
	
	alert('Si è verificato un errore durante la connessione al servizio');
	clearInterval(interval_ping);
}
