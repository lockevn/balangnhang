
/*======================================================================*/
/* DOCEBO - The E-Learning Suite										*/
/* ==================================================================== */
/* 																		*/
/* Copyright (c) 2007													*/
/* http://www.docebo.com/												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/*======================================================================*/

Effect.roundBorderOfElem = function(element_id) {
	
	var designed_elem = $(element_id);
	if(designed_elem == null || designed_elem == false) return;
		
	// draw the border with div =======================
	var up = document.createElement('div');
	var bottom = document.createElement('div');
	var left = document.createElement('div');
	var right = document.createElement('div');
	
	up.className = 'bb_up';
	bottom.className = 'bb_bottom';
	left.className = 'bb_left';
	right.className = 'bb_right';
	
	up.zIndex = designed_elem.zIndex + 1;
	bottom.zIndex = designed_elem.zIndex + 2;
	left.zIndex = designed_elem.zIndex + 3;
	right.zIndex = designed_elem.zIndex + 4;
	
	designed_elem.appendChild(up);
	designed_elem.appendChild(bottom);
	designed_elem.appendChild(left);
	designed_elem.appendChild(right);
	
	// corners ==========================================================
	var tl = document.createElement('div');
	var tr = document.createElement('div'); 
	var bl = document.createElement('div');
	var br = document.createElement('div');
	
	tl.className = 'bb_tl';
	tr.className = 'bb_tr';
	bl.className = 'bb_bl';
	br.className = 'bb_br';
	tl.zIndex = designed_elem.zIndex + 5;
	tr.zIndex = designed_elem.zIndex + 6;
	bl.zIndex = designed_elem.zIndex + 7;
	br.zIndex = designed_elem.zIndex + 8;
	
	designed_elem.appendChild(tl);
	designed_elem.appendChild(tr);
	designed_elem.appendChild(bl);
	designed_elem.appendChild(br);
}

Effect.roundByClass = function(class_name) {
	
	var match_elements = $$('div.'+class_name, 'p.'+class_name);
	if(match_elements == null || match_elements == false || match_elements.lenght == 0) return;
	
	for(var i = 0;i < match_elements.length; i++) {
		
		Effect.roundBorderOfElem(match_elements[i]);
	}
}
