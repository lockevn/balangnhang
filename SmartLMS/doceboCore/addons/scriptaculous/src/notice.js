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

var NoticeMsg = {
	
	waiting_displayed: 0,
	
	appended: new Array(),
	
	/**
	 * the last 3 params of this function are optional, if to_element is not specfied the body is used
	 * disappera after id evaluated as microsec
	 **/
	display: function (message_text, to_element, message_type, disappear_after) {
		
		if(to_element === null) {
			var body_elem = document.getElementsByTagName('body');
			var container = body_elem[0];
		} else {
			var container =	$(to_element);
		}
		if(!container) return false;
		
		var new_div = document.createElement('div');
		var new_p = document.createElement('p');
		var close_link = document.createElement('a');
		
		new_div.appendChild(new_p);
		new_p.innerHTML = message_text + ' ';
		new_p.appendChild(close_link);
		container.insertBefore(new_div, container.firstChild);
		
		
		close_link.className = 'close_link';
		close_link.href 		= 'javascript:void(0)';
		close_link.innerHTML = 'close';
		close_link.onclick 	= function () { this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode); }
		
		switch(message_type) {
			case "notice" 	: { new_div.className = 'notice_display notice_display_notice'; };break;
			case "success" 	: { new_div.className = 'notice_display notice_display_success'; };break;
			case "failure" 	: { new_div.className = 'notice_display notice_display_failure'; };break;
			case "error" 	: { new_div.className = 'notice_display notice_display_error'; };break;
			default 		: { new_div.className = 'notice_display notice_display_default'; };break;
		}
		new_div.style.zIndex 	= '80000';
		new_div.id = 'auto_notice_' + this.appended.length;
		this.appended[this.appended.length] = new_div.id;
		
		if(disappear_after != null) { 
			setTimeout("Effect.Fade('" + new_div.id + "', {duration:0.5});", disappear_after); 
		}
		
		if($(new_p.focus).focus !== undefined) setTimeout('$('+new_p.focus+').focus();', 100);
		return true;
	},
	
	_removeDisplay: function (appended_closed) {
		(this.appended).splice(appended_closed, 1);
	},
	
	loading: function () {
		
		if(this.waiting_displayed == 0) {
			
			var wait_msg = document.createElement('div');
			var body_elem = document.getElementsByTagName('body');
			body_elem[0].appendChild(wait_msg);
			
			wait_msg.id 			= 'notice_ajax_loading';
			wait_msg.className 		= "notice_ajax_loading";
			wait_msg.innerHTML 		= 'Loading ...';
			wait_msg.style.zIndex 	= '90000';
		}
		this.waiting_displayed++;
	},
	
	endLoading: function () {
		
		if(this.waiting_displayed <= 1) {
			
			var elem = $('notice_ajax_loading');
			elem.parentNode.removeChild(elem);
			this.waiting_displayed = 0;
		} else {
			this.waiting_displayed--;
		}
	},
	
	forceEndLoading: function () {
		
		this.waiting_displayed = 0;
		var elem = $('notice_ajax_loading');
		if(elem) elem.parentNode.removeChild(elem);
	}
	
}
