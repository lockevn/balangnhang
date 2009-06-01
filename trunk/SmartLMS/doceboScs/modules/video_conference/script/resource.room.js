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

var Room_VR = {

	latency: 10,
	
	init_resource: function () {}, 
	
	get_room_list: function () {},
	
	get_param_for_action_ping: function() {
		
		var ping_data = new Object();
		ping_data.resource 		= 'room';
		ping_data.action_idref 	= 'get_room_list';
		
		ping_data.action_data = new Object();
		ping_data.action_data = false;
		
		return ping_data;
	},
	
	perform_action_result: function (action_idref, result) {
		
		switch(action_idref) {
			case "get_room_list" : {};break;
		}
	}
	
}