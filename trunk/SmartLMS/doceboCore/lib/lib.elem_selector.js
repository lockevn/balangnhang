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

function ElemSelector(argument) {
	
	this.current_selection = new Object();
	this.num_selected = 0;
	this.base = argument;
	this.counter = false;
}

ElemSelector.prototype.refreshCounter = function() {

	if(!this.counter) return;
	YAHOO.util.Dom.get(this.counter).innerHTML = this.num_selected;
}

ElemSelector.prototype.toString = function() {
	str = '';
	for(ind in this.current_selection) {
		
		if(ind.indexOf( this.base ) >= 0 && this[ind] != 0) str = str + ',' + parseInt(ind.substr(this.base.length));
	}
	return str;
}

ElemSelector.prototype.initSelection = function(arr_sel) {
	
	for(ind in arr_sel) {
		this.addsel(ind);
	}
	return true;
}

ElemSelector.prototype.addsel = function(id_sel) {
	
	if(this.current_selection[this.base+id_sel] != id_sel) {
		this.num_selected++;
	}
	this.current_selection[this.base+id_sel] = id_sel;
}

ElemSelector.prototype.remsel = function(id_sel) {
	
	if(this.current_selection[this.base+id_sel] == id_sel) {
		this.num_selected--;
		this.current_selection[this.base+id_sel] = 0;
	}
}

ElemSelector.prototype.isset = function(id_sel) {

	if(this.current_selection[this.base+id_sel] == id_sel) return true;
	return false;
}

ElemSelector.prototype.reset = function() {
	this.current_selection = new Object();
	this.num_selected = 0;
}
