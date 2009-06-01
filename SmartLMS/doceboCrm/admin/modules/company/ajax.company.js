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

var company_server_url;
var company_lang;

function setup_company(passed_server_url) {
	company_server_url = passed_server_url;
}

function save_new_conf() {

	var config = $F('idref_config_drop');

	var data = "op=save_new_conf&new_conf="+config;
	
	var objAjax = new Ajax.Request(
        	company_server_url,
        	{method: 'post', parameters: data, onComplete: save_new_conf_complete}
    );
}
function save_new_conf_complete(objReq) {
		
	var re = (objReq.responseText).evalJSON(true);
	
	if(re.result) {
		
		$('mod_conf_link').innerHTML = re.new_conf_name;
		Effect.toggle('mod_config_container', 'blind');	
	}
	appendAlert('company_content', re.result_phrase);
}
