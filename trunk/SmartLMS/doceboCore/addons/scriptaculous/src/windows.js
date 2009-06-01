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

var wObjList= new Array();

Window = function() {
	this.title=null;
	this.content=null;
	this.id=null;
	this.width=null;
	this.height=null;
	this.onClose=null;
	this.close_text = 'close';
	this.css_class_name = 'window_object';
	this.buttons = null;
	this.form = null;
};


Window.prototype.show = function() {
	
	if(wObjList[this.id] != undefined) {
		wObjList[this.id].show();
	} else {
		
		wObjList[this.id] = new YAHOO.widget.SimpleDialog("simpledialog_"+this.id, 
			{	fixedcenter: true,
				visible: false,
				draggable: true,
				close: true,
				modal: true,
				constraintoviewport: true
			} );
		new_div = document.createElement('div');
		if(this.form != null) new_div.appendChild(this.form);
		
		if(this.buttons != null) {
	
			var buttonArea = document.createElement('div');
		 	new_div.appendChild(buttonArea);
		  	if(this.form != null) this.form.appendChild(buttonArea);
			else new_div.appendChild(buttonArea);
			buttonArea.innerHTML = this.buttons;
			
			
			var undo_button = document.createElement('input');
			undo_button.type = 'button';
			undo_button.value = 'Annulla';
			undo_button.id = 'undo_' + this.id;
			buttonArea.appendChild(undo_button);

			var handleUndo = function(e) {
				this.hide();
			};
			YAHOO.util.Event.addListener('undo_'+this.id, "click", handleUndo, wObjList[this.id], true);
		}
		
		wObjList[this.id].setHeader(this.title);
		wObjList[this.id].setBody(this.content);
		wObjList[this.id].setFooter(new_div.innerHTML);

		wObjList[this.id].render(document.body); 
		wObjList[this.id].show();
	}





/*
	var do_appear = true;

	if(wObjList[this.id] != null) {
		do_appear = false;
		destroyWindowNoEffect(this.id);
	}
	wObjList[this.id] = this;

	var new_div = document.createElement('div');
	new_div.id = this.id;
	new_div.style.display = 'block';
	new_div.style.width = this.width+'px';
	new_div.style.zIndex = new_div.style.zIndex + "9000";
	new_div.style.left 	= Math.round((browserWidth()/2 - (this.width/2) ))+'px';
	if(this.height == null)
		new_div.style.top = 200+getTop()+'px';
	else
		new_div.style.top = getTop()+Math.round((browserHeight()/2 - this.height/2 ))+'px';
	new_div.className 	= this.css_class_name;

	var titlebar = document.createElement('div');
  	titlebar.className = 'title_bar';
	titlebar.id = 'wTitleBar_'+this.id;
	titlebar.innerHTML = '<h1 id="' + this.id + '_titleBar">' + this.title + '</h1>'
		+ '<a class="close_button" href="" id="' + this.id + '_close" onclick="callCloseHandler('+"'"+this.id+"'"+');destroyWindow('+"'"+this.id+"'"+');return false;">'
		+ '<span>' + this.close_text +'</span></a>';
   	new_div.appendChild(titlebar);

	if(this.form != null)
		new_div.appendChild(this.form);

	var clientArea = document.createElement('div');
	clientArea.className = "w_content";
	if(this.height != null)	clientArea.style.height = this.height+'px';
	clientArea.innerHTML += this.content;
	if(this.form != null) this.form.appendChild(clientArea);
	else new_div.appendChild(clientArea);

	if(this.buttons != null) {

		var buttonArea = document.createElement('div');
	 	 	new_div.appendChild(buttonArea);
	  	if(this.form != null) this.form.appendChild(buttonArea);
		else new_div.appendChild(buttonArea);
	   	buttonArea.className = "line_for_button";
		buttonArea.innerHTML += this.buttons;
	}

	//if(do_appear) new_div.style.display='none';

	document.body.insertBefore(new_div, document.body.nextSibling);

	new Draggable(this.id,{handle:'title_bar'});
	if (do_appear) {
		//Effect.Appear(new_div, { duration: 0.4 });
	}

	//if($(this.id).focus !== undefined) setTimeout('$('+this.id+').focus();', 500);
	*/
	
}


function callCloseHandler(name) {
	w=wObjList[name];
	if (w && w.onClose) {
		w.onClose();
	}
}

function destroyWindow(name) {
	var toKill = $(name);
  	//Effect.DropOut(toKill);
 	toKill.parentNode.removeChild(toKill);
	wObjList[name]=null;
}

function destroyWindowNoEffect(name) {
	var toKill = $(name);
 	toKill.parentNode.removeChild(toKill);
	wObjList[name]=null;
}

function browserWidth() {
   if (self.innerWidth) {
	return self.innerWidth;
   } else if (document.documentElement && document.documentElement.clientWidth) {
	return document.documentElement.clientWidth;
   } else if (document.body) {
	return document.body.clientWidth;
   }
   return 630;
}

function browserHeight() {
   if (self.innerWidth) {
	return self.innerHeight;
   } else if (document.documentElement && document.documentElement.clientWidth) {
	return document.documentElement.clientHeight;
   } else if (document.body) {
	return document.body.clientHeight;
   }
   return 470;
}

function getTop() {
	if (window.innerHeight)
	{
		  pos = window.pageYOffset
	}
	else if (document.documentElement && document.documentElement.scrollTop)
	{
		pos = document.documentElement.scrollTop
	}
	else if (document.body)
	{
		  pos = document.body.scrollTop
	}


	return pos;

}

function showMsg(str) {	
	
	if (wObjList['wMsg']!=null) {
		var w0=$('wMsg');
		var el=document.getElementsByClassName("w_content",w0.parentNode);
		el[0].innerHTML=str;
	} else {
		
			var name="wMsg";
			var title="";
		
   			var w=new Window();
			w.top=getTop();
			w.id=name;
			w.width=450;
			w.height=200;
			w.title=title;
			w.content=str;
			w.show();
		
	}

}