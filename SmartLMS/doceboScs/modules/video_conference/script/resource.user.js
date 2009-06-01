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

var self_id = 0;
var self_role = 0;
var self_display = 'unknown';

function setAvailableMedia(webcam, mic) {
	
	var action_data = new Object();
	action_data.mic 	= mic;
	action_data.webcam 	= webcam;
	perform_ajax_request('user', 'set_media', action_data);
}

function ring_bell() {
	
	var action_data = new Object();
	perform_ajax_request('user', 'ring_bell', action_data);
}

/* Webcam event listener =============================================== */


var LogUser = {

	users: [],
	
	newuser: function(user, list_of_user, append_after) {
	
		var new_div = document.createElement('div');
		
		if(append_after == undefined) {
			list_of_user.appendChild(new_div);
		} else {
			list_of_user.insertBefore(new_div, append_after);
		}
		
		new_div.id = 'user_' + user[User_VR.ID_USER];
		
		var line = '<p id="username_' + user[User_VR.ID_USER] + '" class="display_name">'
			+ ( user[User_VR.ROLE] == '1' 
					? '<img src="'+templ_path+'/images/master.png" alt="master d\'aula" /> ' 
					: '<img src="'+templ_path+'/images/auditor.png" alt="discente" /> '  )
			+ user[User_VR.DISPLAY_NAME] + '</p>'
			
			+ '<ul id="userpanel_' + user[User_VR.ID_USER] + '" class="action_list">';
		
		if(self_role == '1') {
			
			if(user[User_VR.WEBCAM] == '1') {
				
				line = line + '<li>'
					+ '<a href="javascript:;" '
						+ ' id="broadcast_command_' + user[User_VR.ID_USER] + '" '
						+ ' title="trasmetti il video di questo utente a tutti i partecipanti della videoconf">'
						+ '<img src="'+templ_path+'/images/webcam' + ( user[User_VR.BROADCAST] == '1' ? '_stop' : '' ) + '.png" alt="webcam" />'
					+ '</a>'
					+ '</li>';
			} else if(user[User_VR.MIC] == '1') {
				
				line = line + '<li>'
					+ '<a href="javascript:;" ' 
						+ ' id="broadcast_command_' + user[User_VR.ID_USER] + '" '
						+ ' title="trasmetti l\'audio di questo utente a tutti i partecipanti della videoconf">'
						+ '<img src="'+templ_path+'/images/audio' + ( user[User_VR.BROADCAST] == '1' ? '_stop' : '' ) + '.png" alt="audio" />'
					+ '</a>'
					+ '</li>';
			}
		}
		
		line = line + '</ul>';
		new_div.innerHTML = line;
		
		if(self_role == '1' && ((user[User_VR.WEBCAM] == '1') || (user[User_VR.MIC] == '1'))) {
			
			var is_broadcasting = false;
			var new_user = new Object();
			new_user.id_user = user[User_VR.ID_USER];
			if(user[User_VR.BROADCAST] == '1') is_broadcasting = true; 
			new_user.observer = new UserObserver('broadcast_command_' + user[User_VR.ID_USER], user[User_VR.ID_USER], is_broadcasting);
			this.users.push(new_user);
		}
	}, 
	
	removeuser: function(id_user) {
		
		for(var i=0; i < this.users.length; i++) {
		
			if(this.users[i].id_user == id_user) {
				this.users.splice(i, 1);
				return;
			}
		}
	},
	
	clean: function() {
	
		for(var i=0; i < this.users.length; i++) {
		
			this.users[i].observer.unregister();
		}
		this.users = new Array();
	},
	
	openWebcam: function(id_user, user_name) {
		
		if(id_user != self_id) {
			temp_new_webcam(id_user, user_name, 'new_webcam');
		} else {
			temp_my_new_webcam(id_user, user_name, 'web_self');
			set_webcam_onair(true);
		}
		
		if(self_role == '1') {
		
			var broad = $('broadcast_command_' + id_user);
			if(broad.firstChild != undefined && broad.firstChild != null) {
				
				if(broad.firstChild.src.match('webcam.png')) broad.firstChild.src = ''+templ_path+'/images/webcam_stop.png';
				else if(broad.firstChild.src.match('audio.png')) broad.firstChild.src = ''+templ_path+'/images/audio_stop.png';
			}
			
		}
		for(var i=0; i < this.users.length; i++) {
		
			if(this.users[i].id_user == id_user) {
				this.users[i].observer.change();
				return;
			}
		}
	},
	
	closeWebcam: function(id_user) {
		
		if(id_user != self_id) removeWebcam(id_user);
		else set_webcam_onair(false);
		
		if(self_role == '1') {
		
			var broad = $('broadcast_command_'+id_user);
			if(broad.firstChild != undefined && broad.firstChild != null) {
			
				if(broad.firstChild.src.match('webcam_stop.png')) broad.firstChild.src = ''+templ_path+'/images/webcam.png';
				else if(broad.firstChild.src.match('audio_stop.png')) broad.firstChild.src = ''+templ_path+'/images/audio.png';
			}
			
		}
		for(var i=0; i < this.users.length; i++) {
		
			if(this.users[i].id_user == id_user) {
				this.users[i].observer.change();
				return;
			}
		}
	}

}

var UserObserver = Class.create();
UserObserver.prototype = {
	
	observer: false,
	observing: false,
	
	element: false,
	id_user: false,

	initialize: function(element, id_user, is_broadcasting) {
		
		if(is_broadcasting == true) this.observing = 'set';
		
		this.element = $(element);
		this.id_user = id_user;
		this.change();
	},
	
	unregister: function() {
		
		if(this.observer != false) Event.stopObserving(this.element, 'click', this.observer);
	},
	
	change: function() {
		
		if(this.observer != false) {
		
			Event.stopObserving(this.element, 'click', this.observer);
		}
		if(this.observing == 'set') {
		
			this.observer = this.unset_broadcast.bindAsEventListener(this, this.id_user);
			Event.observe(this.element, 'click', this.observer);
			this.observing = 'unset';
		} else {
		
			this.observer = this.set_broadcast.bindAsEventListener(this, this.id_user);
			Event.observe(this.element, 'click', this.observer);
			this.observing = 'set';
		}
	},
	
	set_broadcast: function(event) {
		
		var action_data = new Object();
		action_data.to_broadcast = arguments[1];
		perform_ajax_request('user', 'broadcast_webcam', action_data);
	},
	
	unset_broadcast: function(event) {
		
		var action_data = new Object();
		action_data.to_broadcast = arguments[1];
		perform_ajax_request('user', 'unbroadcast_webcam', action_data);
	}
	
};

/* RiseHand event listener ============================================== */

var RiseHand = {
	
	rise: function(event) {
		
		var action_data = new Object();
		action_data.rise_user = self_id;
		perform_ajax_request('user', 'rise_hand', action_data);
	},
	
	lower: function(event) {
		
		var elem = Event.element(event);
		if(!elem) return;
		var action_data = new Object();
		action_data.rise_user = elem.parentNode.id;
		perform_ajax_request('user', 'lower_hand', action_data);
	}
};

var User_VR = {

	latency: 9,
	
	ID_USER: 0,
	WEBCAM: 6,
	MIC: 7,
	ROLE: 4,
	BROADCAST: 11,
	DISPLAY_NAME: 13,
	
	refresh_list: false, 
	
	rised_hand_list: new Array(), 
	
	init_resource: function () {
	
		var action_data = new Object();
		action_data.auth_code = '';
		perform_ajax_request('user', 'login_user', action_data);
	}, 
	
	get_user_list: function () {
		
		var action_data = new Object();
		perform_ajax_request('user', 'get_user_list', action_data);
	},
	
	get_param_for_action_ping: function() {
		
		var ping_data = new Object();
		ping_data.resource 		= 'user';
		ping_data.action_idref 	= ( this.refresh_list == true ? 'get_user_list' : 'get_userlist_change' );
		ping_data.action_data 	= new Object();
		return ping_data;
	},
	
	perform_action_result: function (action_idref, result) {
		
		switch(action_idref) {
			case "login_user"				: { 
				
				self_id 		= result.user_self.id;
				self_role		= result.user_self.role;
				self_display 	= result.user_self.display_name;
				
				var line = '';
				
				var command = $('mycommand');
				
				if(self_role == '1' && command !== null) {
					
				} else if(command !== null) {
					
					var hand_big = document.createElement('a');
					hand_big.href 		= 'javascript:;';
					hand_big.title 		= 'alza la mano';
					hand_big.innerHTML 	= '<img src="'+templ_path+'/images/hand_big.png" alt="rise hand" />';
					command.appendChild(hand_big);
					
					Event.observe(hand_big, 'click', RiseHand.rise.bindAsEventListener(RiseHand));
				}
				
				logged_succ = true;
				publishDrawboard(self_id, 1);
				// this operation will call the setAvailableMedia
			};break;
			case "set_media" 			: {
				
				this.update_user_list(result);
				logged_succ = true;
			};break;
			case "get_user_list" 		: { this.update_user_list(result); };break;
			case "get_userlist_change" 	: { this.update_user_list_with_change(result); };break;
		}
	},
						
	rise_user_hand: function (of_user) {
	
		var rised_hands = this.rised_hand_list.length;
		for(var i=0; i < rised_hands; i++) {
		
			if(this.rised_hand_list[i] == of_user) return;
		}
		
		var command = $('userpanel_' + of_user);
		if(!command) return;
		
		var risehand = document.createElement('li');
		command.appendChild(risehand);
		
		var hand_lower = document.createElement('a');
		risehand.appendChild(hand_lower);
		
		risehand.id = 'rise_index_' + of_user;
		risehand.className = 'rised_hand';
		
		hand_lower.href 		= 'javascript:;';
		hand_lower.title 		= 'alza la mano';
		hand_lower.innerHTML 	= rised_hands + 1;
		
		this.rised_hand_list[rised_hands + 1] = of_user;
		
		Event.observe(risehand, 'click', RiseHand.lower.bindAsEventListener(RiseHand, of_user));
	},
	
	lower_user_hand: function (of_user) {
	
		var rised_hands = this.rised_hand_list.length;
		var sub_other = false;
		var index_to_unset = -1;
		
		for(var i=0; i < rised_hands; i++) {
		
			if(sub_other) {
				var risehand = $('rise_index_' + this.rised_hand_list[i]);
				if(risehand) risehand.innerHTML = i - 1;
			} else if(this.rised_hand_list[i] == of_user) {
				sub_other = true;
				index_to_unset = i;
			}
		}
		if(index_to_unset != -1) {
			var hand = $('rise_index_' + this.rised_hand_list[index_to_unset]);
			if(!hand) return;
			hand.parentNode.removeChild(hand);
			
			this.rised_hand_list.splice(index_to_unset, 1);
		}
	},
	
	get_user_line: function (id_user, display_name, role, webcam, mic) {
		
		var line = '<p id="username_'+id_user+'" class="display_name">'
				 + ( role == '1' 
				 	? '<img src="'+templ_path+'/images/master.png" alt="master d\'aula" /> ' 
				 	: '<img src="'+templ_path+'/images/auditor.png" alt="discente" /> '  )
				 + display_name + '</p>';
		
		if(self_role == '1') {
			
			line = line + '<ul id="userpanel_'+id_user+'" class="action_list">';
			if(webcam == '1') {
				
				line = line + '<li>'
					+ '<a href="javascript:;" '
						+ ' id="broadcast_command_'+id_user+'" '
						+ ' title="trasmetti il video di questo utente a tutti i partecipanti della videoconf">'
						+ '<img src="'+templ_path+'/images/webcam.png" alt="webcam" />'
					+ '</a>'
					+ '</li>';
			} else if(mic == '1') {
				
				line = line + '<li>'
					+ '<a href="javascript:;" ' 
						+ ' id="broadcast_command_'+id_user+'" '
						+ ' title="trasmetti l\'audio di questo utente a tutti i partecipanti della videoconf">'
						+ '<img src="'+templ_path+'/images/audio.png" alt="audio" />'
					+ '</a>'
					+ '</li>';
			}
			line = line + '</ul>';
		} else {
			
			line = line + '<ul id="userpanel_'+id_user+'" class="action_list">' + '</ul>';
		}
		return line;
	},
	
	update_user_list: function(result) {
		
		var ulist = $('effective_user_list');
		var num_listed = ulist.getElementsByTagName('p');
		ulist.innerHTML = '';
		LogUser.clean();
		
		for(var i = 0;i < result.length; i++) {
			
			LogUser.newuser(result[i], ulist);
		}
		this.refresh_list = false;
	},
	
	update_user_list_with_change: function(result) {
		
		var ulist = $('effective_user_list');
		// parse user logged in in the meantime
		if(result.logged_in.length != 0 && result.logged_in.length != undefined) {
			
			for(var i = 0;i < result.logged_in.length; i++) {
				
				if($('user_' + result.logged_in[i][this.ID_USER]) == undefined) {
					
					var new_name = (result.logged_in[i][this.DISPLAY_NAME]).toLowerCase();
					
					// find position in the user list
					var founded = false;
					for(var j = 0;j < ulist.childNodes.length && founded == false; j++) {
						
						if(ulist.childNodes[j].nodeType == 1) {
							
							if((ulist.childNodes[j].innerHTML).toLowerCase() > new_name) founded = ulist.childNodes[j];
						}
					}
					if(founded != false) LogUser.newuser(result.logged_in[i], ulist, founded);
					else LogUser.newuser(result.logged_in[i], ulist);
					
				}
			}
		}
		// parse user logged out in the meantime
		if(result.logged_out.length != 0 && result.logged_out.length != undefined) {
			
			for(var i = 0;i < result.logged_out.length;i++) { 
			
				if($('user_' + result.logged_out[i][0]) != undefined) {
					
					ulist.removeChild( $('user_' + result.logged_out[i][0]) );
					if(self_role == '1') LogUser.removeuser(result.logged_out[i][0]);
				}
				removeWebcam(result.logged_out[i][this.ID_USER]);
			} // end for
		}
		var num_listed = ulist.getElementsByTagName('p');
		if(result.now_online != num_listed.length) { this.refresh_list = true; }
	},
	
	logoutUser: function () {
		
		var action_data = new Object();
		
    	var data = "requested_action=shot"
			+ "&resource=" + 'user'
			+ "&action_idref=" + 'logout_user'
			+ "&action_data=" + encodeURIComponent(Object.toJSON(action_data));
	
		var objAjax = new Ajax.Request(
	        	_vc_serverurl,
	        	{method: 'post', parameters: data }
	    );
	    
	}
	
}
