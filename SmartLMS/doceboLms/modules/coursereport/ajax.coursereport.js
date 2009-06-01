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

function setup_coursereport(passed_server_url) {
	fw_d_server_url = passed_server_url;
}

function getQuestDetail(id_quest, id_test, type) {

	if(opened_profile != id_quest) {

		if(type == 'extended_text')
			var data = "op=extendedquestdetail&id_quest=" + id_quest + "&id_test=" + id_test;
		else
			var data = "op=fileuploaddetail&id_quest=" + id_quest + "&id_test=" + id_test;
		
		YAHOO.util.Dom.get('less_quest_' + id_quest).style.display = 'inline';
		YAHOO.util.Dom.get('more_quest_' + id_quest).style.display = 'none';
		
		var objAjax = YAHOO.util.Connect.asyncRequest('POST', fw_d_server_url + '?' + data, {success : getQuestDetail_complete, failure : ajax_failure});
    }
}

function closeQuestDetail(id_quest, id_test, type) {
	
	if(id_quest != 0) {
		
		var row  = YAHOO.util.Dom.get('quest_' + id_quest);
		
		YAHOO.util.Dom.get('less_quest_' + id_quest).style.display = 'none';
		YAHOO.util.Dom.get('more_quest_' + id_quest).style.display = 'inline';
		YAHOO.util.Dom.get('quest_' + id_quest).innerHTML = '';
		
		YAHOO.Animation.BlindOut('quest_' + id_quest);
	}
}

function getQuestDetail_complete(objReq)
{
	var parsed = YAHOO.lang.JSON.parse(objReq.responseText);
	var row  = YAHOO.util.Dom.get('quest_' + parsed.id_quest); 
	var resize = parsed.records.length * 30;
	var attributes = { 
		height: { to : resize  }
	}; 
	
	YAHOO.Animation.BlindIn('quest_' + parsed.id_quest);
	
	for(i=0;i < parsed.records.length;i++)
	{
		var answer = document.createElement('p');
		answer.innerHTML = parsed.records[i];
		row.appendChild(answer);
	}
}

function ajax_failure(o)
{
}