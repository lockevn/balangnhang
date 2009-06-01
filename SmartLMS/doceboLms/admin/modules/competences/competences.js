//utils functions
function server_call(op,pars,callback) {
  var params='op='+op+'&'+pars;
  var oCallback={
    success: function (o) {
      var r=YAHOO.lang.JSON.parse(o.responseText);
      callback.success(r);
    },
    failure: function(o) {
      //do something to manage failure to calling server
      callback.failure(x);
    }
  };
  YAHOO.util.Connect.asyncRequest('POST', ajax_path, oCallback, params);
}


var comp_dialogBox, cat_dialogBox;

var del_competence=function(id) {
  var temp=comp_dialogBox;
  var oCallback={
    success:function(x) { temp.setHeader(x.head); temp.setBody(x.body); temp.show(); },
    failure:function(x) { alert(oLang._CONNECTION_ERROR); },
    scope: comp_dialogBox
  }
  server_call('del_competence', 'id_comp='+id, oCallback);
}


var del_category=function(id) {
  var temp=cat_dialogBox;
  var oCallback={
    success:function(x) { temp.setHeader(x.head); temp.setBody(x.body); temp.show(); },
    failure:function(x) { alert(oLang._CONNECTION_ERROR); },
    scope: comp_dialogBox
  }
  server_call('del_category', 'id_cat='+id, oCallback);
}

function initPopUp(e) {
  var container=document.createElement("DIV");
  container.id="comp_confirm_popup";
  document.body.appendChild(container);
  container=document.createElement("DIV");
  container.id="cat_confirm_popup";
  document.body.appendChild(container);
    
  var cancelFunction=function() { this.hide(); }
  
  var comp_okFunction=function(e) {
    var dialog=this;
    var oCallback={
      success:function(x) { if (x.success) { dialog.hide(); location.reload();  } else { alert(x.message); } },
      failure:function(x) { alert(oLang._CONNECTION_ERROR); dialog.hide(); }
    }
    server_call('confirm_del_competence','id_comp='+YAHOO.util.Dom.get('id_comp').value, oCallback);
  }
  
  var cat_okFunction=function(e) {
    var dialog=this;
    var oCallback={
      success:function(x) { if (x.success) { dialog.hide(); location.reload(); } else { alert(x.message); } },
      failure:function(x) { alert(oLang._CONNECTION_ERROR); dialog.hide(); }
    };
    server_call('confirm_del_category','id_cat='+YAHOO.util.Dom.get('id_cat').value+'&move_cat='+YAHOO.util.Dom.get('move_cat').value, oCallback);
  }
  
  comp_dialogBox=new YAHOO.widget.SimpleDialog("comp_confirm_popup", {
    width:"400px",
		fixedcenter: true,
		visible: false,
		draggable: true,
		modal: true,
		close: true,
		zindex: 10002,
		constraintoviewport: true,
		buttons: [ 
			{ text: oLang._CONFIRM, handler: comp_okFunction, isDefault: true },
			{ text: oLang._UNDO, handler: cancelFunction } 
		]
  } );
  comp_dialogBox.setHeader("&nbsp;");
  comp_dialogBox.render("comp_confirm_container");
  
  cat_dialogBox=new YAHOO.widget.SimpleDialog("cat_confirm_popup", {
    width:"400px",
		fixedcenter: true,
		visible: false,
		draggable: true,
		modal: true,
		close: true,
		zindex: 10002,
		constraintoviewport: true,
		buttons: [ 
			{ text: oLang._CONFIRM, handler: cat_okFunction, isDefault: true },
			{ text: oLang._UNDO, handler: cancelFunction } 
		]
  } );
  cat_dialogBox.setHeader("&nbsp;");
  cat_dialogBox.render("cat_confirm_container");
}


//******************************************************************************
function set_competence_type_parameter() {
  var t1=YAHOO.util.Dom.get('score'), t2=YAHOO.util.Dom.get('score_min');
  switch (YAHOO.util.Dom.get('type').value) {
    case 'score' : { 
      t1.readOnly=false; 
      t1.style.background='#FFFFFF';
      t2.readOnly=false; 
      t2.style.background='#FFFFFF'; //apply standard white color to textfield when active
    } break;
    case 'flag'  : {
      t1.readOnly=true;
      t1.style.background='#E7E7E7';
      t2.readOnly=true;
      t2.style.background='#E7E7E7'; //apply gray color to textfield when deactivated
    } break;
  }
}

function mod_competence_type_event(e) {
  set_competence_type_parameter();
}
//******************************************************************************




//******************************************************************************

var panel;

function cell_highlight(e) {
  YAHOO.util.Dom.addClass(this, "cell_highlight_modify");
}

function cell_backnormal(e) {
  YAHOO.util.Dom.removeClass(this, "cell_highlight_modify");
}

function cell_setvalue(e) {
  var t = this.firstChild; //the div
  
  switch (t.tagName) {
    case 'INPUT': {
      //alert('FLAG');
    } break;
    
    case 'DIV': {
      var p = new score_editor(this);
      p.showEditor();
    } break;
    
    default: { alert('ERROR'); }
  }
  
}

function course_init(e) {
  var t = document.getElementById('course_table').firstChild.getElementsByTagName('tbody')[0].rows;
  for (var i=0; i<t.length; i++) {
    if (t[i].className!='line') {
      YAHOO.util.Event.addListener(t[i].cells[6],'mouseover',cell_highlight);
      YAHOO.util.Event.addListener(t[i].cells[6],'mouseout',cell_backnormal);
      YAHOO.util.Event.addListener(t[i].cells[6],'click',cell_setvalue);
    }
  }
  panel = new YAHOO.widget.Panel('edit_popup', {width:"320px", visible:false, draggable:false});
  panel.render();
}

function course_modify(id_course,id_comp) {
  //make textfield appear
  var t=document.getElementById('score_value_'+id_comp);
  var old  = t.innerHTML;
  var temp = (old=='-' ? '' : t.firstChild.innerHTML);
  t.innerHTML = '<input type="text" class="align_right" style="width:5em" name="competence_assign_score['+id_comp+']" '+
                'value="'+temp+'" />'+
                '<span id="old_value_'+id_comp+'" style="display:none">'+old+'</span>';
  //toggle mod icon
  var u=document.getElementById('a_mod_'+id_comp);
  u.href='javascript:course_modify_undo('+id_course+','+id_comp+');';
  //u.firstChild.src=img_path+'mod.gif';
}

function course_remove(id_course,id_comp) {
  //remove score from table
  var t=document.getElementById('score_value_'+id_comp);
  t.innerHTML='-'; //input hidden[] ??
  //...
}

function course_modify_undo(id_course,id_comp) {
  var t=document.getElementById('score_value_'+id_comp);
  var old=document.getElementById('old_value_'+id_comp).innerHTML;
  t.innerHTML=old;
  //restore mod icon
  var u=document.getElementById('a_mod_'+id_comp);
  u.href='javascript:course_modify('+id_course+','+id_comp+');';
  //u.firstChild.src=img_path+'goto.gif';
}

function check_float(e) {}
//**************
function score_editor(td) {
  var t;
  if (t=YAHOO.util.Dom.get('score_editor')) document.body.removeChild(t);//t.destroy();

  this.container = document.createElement('DIV');
  this.container.className='yui-dt-editor';
  this.container.style.display='none';
  this.container.id = 'score_editor';
  this.target = td;

  this.showEditor = function () {
  	var value = this.target.value;
    var temp=document.createElement("DIV");
	  var textvalue=this.target.firstChild.firstChild.firstChild.data;  
	          
	  var text = document.createElement('INPUT');
    text.type='text';
    text.className='align_right';
    text.style.width='100%';
    text.value=(textvalue=='-' ? '' : textvalue); //text inside the <span>
  	YAHOO.util.Event.addListener(text,'keydown',check_float);
  	
  	
    temp.appendChild(text);
    temp.style.paddingBottom='10px';
    this.container.appendChild(temp); 
    
        
    var buttons=document.createElement('DIV');
    buttons.style.textAlign='right';
    temp=document.createElement('BUTTON');
    temp.className='button';
    temp.innerHTML=_SAVE;//'Ok';
    YAHOO.util.Event.addListener(temp, "click", this.confirm, this, true);
    buttons.appendChild(temp);      
	
  	temp=document.createElement('BUTTON');
    temp.className='button';
    temp.innerHTML=_UNDO;//'Annulla';
    YAHOO.util.Event.addListener(temp, "click", this.cancel, this, true);
    buttons.appendChild(temp);  
  
  	this.container.appendChild(buttons);
  	
  	//positioning the container
  	this.container.zIndex='150';
  	this.container.style.display='block';
  	var pos = YAHOO.util.Dom.getXY(td);
  	
  	document.body.appendChild(this.container);
  	
  	//pos[0]-=(this.container.offsetWidth - td.offsetWidth)/2;
  	pos[0]-=(this.container.offsetWidth - td.offsetWidth);///2;
  	pos[1]-=(this.container.offsetHeight - td.offsetHeight)/2;
  	
  	YAHOO.util.Dom.setXY(this.container.id, pos);
  	text.focus();
  };

  this.confirm = function(e) {
    var self=this;
    var callback={
      success: function(x) {
        if (x['success']) {
          var t=x['new_value'];
          var span=self.target.firstChild.firstChild;
          span.firstChild.data=(t=='' ? '-' : t);
          span.style.fontWeight=(t=='' ? '' : 'bold');
        } else {
          //...
        }
        self.destroy();
      },
      failure: function(x) {}
    };
    var id_course = YAHOO.util.Dom.get('id_course').value;
    var id_comp   = this.target.firstChild.id.split('_')[2]; //get competence id from id of the div
    var value = this.container.firstChild.firstChild.value;
    server_call('update_course_comp','id_course='+id_course+'&id_comp='+id_comp+'&comp_value='+value,callback)
  };
  
  this.cancel = function(e) {
    this.destroy();
  }
  
  this.destroy = function() { document.body.removeChild(this.container); }

}

function course_flag_change(obj) {
  var callback={
    success: function(x) {
      if (x['success']) {
        //...
      } else {
        //...
      }
    },
    failure: function(x) {}
  };
    var id_course = YAHOO.util.Dom.get('id_course').value;
    var id_comp   = obj.value;
    var value = (obj.checked ? 1 : 0);
    server_call('update_course_comp','id_course='+id_course+'&id_comp='+id_comp+'&comp_value='+value,callback)
}