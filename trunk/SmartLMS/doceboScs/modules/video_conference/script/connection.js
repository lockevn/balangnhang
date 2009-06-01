
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

/** 
 * Pubblica l'output della webcam e microfono sul server di streaming Red5. 
 * Tale funzione deve essere richiamata quando un utente decide 
 * di partecipare alla videoconferenza. 
 * Riceve come parametri l'id dell'utente e il suo nick 
 **/
function publishStream(user_id, nick, web) {
	
	var filmato = $(web);
	if(filmato == undefined) return;
	
	filmato.SetVariable("_root.user_id", user_id);
	filmato.SetVariable("_root.nick", nick);
	filmato.TCallFrame("_root", 1);
}



function createMyWebcamAndConnect(id_user, nick_user, div_id) {
	
	var new_web = $('web_self');
	
	if(new_web === null || new_web === undefined) {
		
		new_web = document.createElement('div');
		new_web.innerHTML = ""
			+ '<object id="web_self" type="application/x-shockwave-flash" data="'+flash_path+'/flash/webcam.swf" width="250" height="200">'
			
			+ "	<param name=\"movie\" value=\""+flash_path+"/flash/webcam.swf\" />"
			+ "	<param name=\"bgcolor\" value=\"#ffffff\" />"
			+ "	<param name=\"quality\" value=\"high\" />"
			
			+ "</object>";
		
		setTimeout("publishStream('" + id_user + "', '" + nick_user + "', 'web_self');", 1000);
		return new_web;
	} else {
		
		new_web.SetVariable("_root.user_id", id_user);
		new_web.SetVariable("_root.nick", nick);
		new_web.TCallFrame("_root", 1);
		return null;
	}
}

function publishDrawboard(user_id, toolbar) {

	var filmato = $('drawboard');
	if(filmato == undefined) return;
		
	filmato.SetVariable("_root.toolbar", toolbar);
	filmato.SetVariable("_root.user_id", user_id);
	filmato.TCallFrame("_root", 1);
}

function createWebcamAndConnect(id_user, nick_user, div_id) {
	
	var new_web = $('web_another_' + id_user);
	
	if(new_web === null || new_web === undefined) {
		
		new_web = document.createElement('div');
		new_web.innerHTML = ""
			+ '<object id="web_another_' + id_user + '" type="application/x-shockwave-flash" data="'+flash_path+'/flash/webcam_repeat.swf" width="250" height="200">'
			
			+ "	<param name=\"movie\" value=\""+flash_path+"/flash/webcam.swf\" />"
			+ "	<param name=\"bgcolor\" value=\"#ffffff\" />"
			+ "	<param name=\"quality\" value=\"high\" />"
			
			+ "</object>";
		
		setTimeout("viewStream('" + id_user + "', '" + nick_user + "', 'web_another_" + id_user + "');", 1000);
		return new_web;
	} else {
		return null;
	}
}

function temp_my_new_webcam(id_user, nick_user, div_id) {

	var webcams = $('effective_webcam_list');
	if(webcams != undefined) {
	
		new_web = createMyWebcamAndConnect(id_user, nick_user, div_id);
		if(new_web !== null) webcams.appendChild(new_web);
	}
}

function temp_new_webcam(id_user, nick_user, div_id) {

	var webcams = $('effective_webcam_list');
	if(webcams != undefined) {
	
		new_web = createWebcamAndConnect(id_user, nick_user, div_id);
		if(new_web !== null) webcams.appendChild(new_web);
	}
}

function set_webcam_onair(status) {
	
	var webcam = $('web_self');	
	if(webcam != undefined) {
		webcam.SetVariable("_root.on_air",status);
		webcam.TCallFrame("_root",3);
	}
}

function removeWebcam(id_user) {

	var new_web = $('web_another_' + id_user);
	if(new_web != undefined) {
		
		// remove the div that contain the webcam
		new_web.parentNode.parentNode.removeChild(new_web.parentNode);
	}
}

/**
 * Riceve lo stream audio/video pubblicato sul server di streaming da un utente.
 * Tale funzione deve essere richiamata ognivolta che un utente
 * desidera visionare la webCam di un altro utente.
 * Riceve come parametri l'id dell'utente e il suo nick 
 **/
function viewStream(user_id, nick, web) {
	
	var filmato = $(web);
	if(filmato == undefined) return;
	filmato.SetVariable("_root.view_id", user_id);
	filmato.SetVariable("_root.view_nick", nick);
	filmato.TCallFrame("_root", 2);
}

/**
 * Tale funzione deve essere richiamata quando l'utente
 * corrente si disconnette dalla videoconferenza.
 * Riceve come parametri l'id dell'utente e il suo nick. 
 **/
function disconnectUser() {
	
	var filmato = $('web_self');
	if(filmato != undefined) filmato.TCallFrame("_root", 4);
}

function disconnectDrawboard() {
	
	var filmato = $('drawboard');
	if(filmato != undefined) filmato.TCallFrame("_root", 2);
}
