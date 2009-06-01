
var taskman_server_url = '';

/****************************/
function expandList(id, img_path) {
  //Effect.SlideDown('details_'+id+'');
  $('expander_'+id+'').innerHTML='<a href="javascript:unexpandList(\''+id+'\', \''+img_path+'\');">'+
                                 '<img src="'+img_path+'less.gif" alt="less" /></a>';
	$('details_'+id+'').style.visibility ='visible';
}
function unexpandList(id, img_path) {
  //Effect.SlideUp('details_'+id+'');
	$('details_'+id+'').style.visibility ='collapse';
  $('expander_'+id+'').innerHTML='<a href="javascript:expandList(\''+id+'\', \''+img_path+'\');">'+
                                 '<img src="'+img_path+'more.gif" alt="more" /></a>';
}
/****************************/


function prjDropdownChange(dd_val) {

	if (dd_val == 'add') {
		$('project').disabled=false;
	}
	else {
		$('project').disabled=true;
	}

}

function setup_taskman(passed_server_url) {
	taskman_server_url = passed_server_url;
}


function refresh_dropdown(company_id) {

	var data = "op=getCompanyInstallations&company_id="+company_id;
	var objAjax = new Ajax.Request(
        	taskman_server_url,
        	{method: 'post', parameters: data, onComplete: refresh_dropdown_complete}
    );
}

function refresh_dropdown_complete(obj) {

	var sel = document.getElementById("customerinstall_id");
	var num_to_delete = sel.length;
	for(var i=0;i<num_to_delete;i++) {
		sel.remove(0);
	}

	var result = obj.responseText;
	new_option = result.evalJSON(true);

	for(var i=0;i<new_option.length;i++) {

		var y = document.createElement('option');
		y.value = new_option[i].value;
		y.text = new_option[i].text;
		try {
			sel.add(y,null); // standards compliant
		} catch(ex) {
			sel.add(y); // IE only
		}
	}
}
