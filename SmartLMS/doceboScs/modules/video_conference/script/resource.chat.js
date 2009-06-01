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

var Chat_VR = {
	
	latency: 2,
	
	beginscroll: true,
	
	_last_message_update: 0,
	
	_last_message_time_update: 0,
	
	CHAT_MAX_MESSAGE: 200,
	
	AUTHOR: 2,
	SENT_DATE: 4,
	TEXT_OF: 5,
	TYPE_OF: 6,
	
	init_resource: function () {},
	
	get_param_for_action_ping: function() {
		
		var ping_data = new Object();
		ping_data.resource 		= 'chat';
		ping_data.action_idref 	= 'get_message_list';
		
		ping_data.action_data = new Object();
		
		return ping_data;
	},
	
	// request function ---------------------------------------------------------
	post_system_message: function (type) {
	
		var action_data = new Object();
		action_data.type = type;
		perform_ajax_request('chat', 'post_system_message', action_data);
	},
	
	post_message: function () {
	
		var action_data = new Object();
		
		action_data.textof = $F('chat_msg_text');
		$('chat_msg_text').value = '';
		$('chat_msg_text').focus();
		if(action_data.textof != '') perform_ajax_request('chat', 'post_message', action_data);
	},
	
	get_message_list: function () {
		
		if(this._last_message_update == false) { this.init(); }
		
		var action_data = new Object();
		perform_ajax_request('chat', 'get_message_list', action_data);
	},
	
	// switch for parse action result -----------------------------------------------
	perform_action_result: function (action_idref, result) {
		
		switch(action_idref) {
			case "post_system_message" 	: {};break;
			case "post_message" 		: { $('chat_msg_list').scrollTop = 100000; };break;
			case "get_message_list" 	: { this.update_message_list(result); };break;
		}
	},
	
	manage_system_message: function(info, msg_list) {
		
		switch(info[0]) {
			case "login" : {
				
				var new_p = document.createElement('p');
				new_p.innerHTML = '<strong class="system_message">' + info[2] + '</strong>';
				msg_list.appendChild(new_p);
			};break;
			case "logout" : {
				
				var new_p = document.createElement('p');
				new_p.innerHTML = '<strong class="system_message">' + info[2] + '</strong>';
				msg_list.appendChild(new_p);
			};break;
			
			case "broadcast_webcam" : {
				
				LogUser.openWebcam(info[1], info[2]);
			};break;
			case "unbroadcast_webcam" : {
				
				LogUser.closeWebcam(info[1]);
			};break;
			
			case "rise_hand" : {
				
				User_VR.rise_user_hand(info[1]);
			};break;
			case "lower_hand" : {
				
				User_VR.lower_user_hand(info[1]);
			};break;
		}
	}, 
	
	// parse result action ----------------------------------------------------------
	update_message_list: function(result) {
		
		var msg_list = $('chat_msg_list');
		if(msg_list.scrollHeight == undefined || msg_list.clientHeight == undefined || msg_list.scrollTop == undefined) {
			var must_scroll = true;
		} else {
			if(msg_list.clientHeight < msg_list.scrollHeight) { this.beginscroll = false; }
			var must_scroll = (msg_list.scrollHeight == msg_list.clientHeight + msg_list.scrollTop);
		}
		// add messages -------------------------------------------------------------
		for(i in result) {
			if(result[i][this.TYPE_OF] == 'system') {
				
				/* parse system messagge ========================================== */
				var info = (result[i][this.TEXT_OF]).evalJSON(true);
				this.manage_system_message(info, msg_list);
				
				/* -end- parse system messagge ======================================== */
			} else if(result[i][this.AUTHOR] != undefined) {
			
				var new_p = document.createElement('p');
				if(result[i][this.AUTHOR] != '') {
				
					// chat message
					new_p.innerHTML = '(' + result[i][this.SENT_DATE].slice(11) 
						+ ') <strong>' + result[i][this.AUTHOR] + ':</strong> ' 
						+ result[i][this.TEXT_OF];
					msg_list.appendChild(new_p);
				}
			}
		}
		
		// if the number of message is more than the maximum message at time, delete the old
		if(msg_list.childNodes.length > this.CHAT_MAX_MESSAGE) {
		
			var to_remove = msg_list.childNodes.length - this.CHAT_MAX_MESSAGE;
			for(var i = 0;i < to_remove;i++) { 
				msg_list.removeChild(msg_list.firstChild); 
			}
		}
		// now scroll down to the bottom -------------------------------------------
		if(must_scroll || this.beginscroll) msg_list.scrollTop = 100000;
	}
	
}
