<?php
if(!defined("IN_DOCEBO")) die('You can\'t access directly');
if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');



function _encode(&$data) { return serialize($data); } //{ return urlencode(serialize($data)); }
function _decode(&$data) { return unserialize($data); } //{ return unserialize(urldecode($data)); }


$rep_cat = get_req('rep_cat', DOTY_ALPHANUM, false);

switch ($rep_cat) {

	case 'competences': {
		//include('ajax.report_competences.php');
	} break;
	
	default: {
	
$op = get_req('op', DOTY_ALPHANUM, '');
switch($op) {

	case 'save_filter_window': {
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$lang =& DoceboLanguage::createInstance('report', 'framework');
		
		$output = array();
		$output['title'] = $lang->def('_SAVE_REPORT_TITLE');
		
		$output['content'] = //'nome filtro:<input type="text" name="filter_name" value="" />';
			Form::getTextfield( 
				'Nome del filtro: ', //$label_name, 
				'filter_name', //$id, 
				'filter_name', //$name, 
				'200', '').Form::getHidden('filter_op','op','save_filter');
		
		$output['button_ok'] = $lang->def('_SAVE');
		$output['button_undo'] = $lang->def('_UNDO');
		
		//require_once('_lib.json.php');
		$json = new Services_JSON();
		aout($json->encode($output));
	} break;


	case 'show_recipients_window': {
		require_once($GLOBALS['where_lms'].'/lib/lib.report.php');
		$lang =& DoceboLanguage::createInstance('report', 'framework');
		$output = array();
		$output['title'] = $lang->def('_SHOW_RECIPIENTS_TITLE');
		$id_sched = get_req('idsched', DOTY_INT, false);
		if ($id_sched>0) {
			$recs = get_schedule_recipients($id_sched, true);
			$temp = '<div><table>';
			foreach ($recs as $key=>$value) {
				$temp .= '<tr><td><b>'.$value['userid'].'</b>:</td><td>'.$value['firstname'].'&nbsp;'.$value['lastname'].'</td></tr>';
			}
			$temp .= '</table></div>';
		}
		$output['content'] = $temp;
		$output['button_close'] = $lang->def('_CLOSE');
		$json = new Services_JSON();
		aout($json->encode($output));
	} break;


	case 'save_filter': {
		$output=array();
		$filter_data = get_req('filter_data', DOTY_ALPHANUM, ''); //warning: check urlencode-serialize etc.
		$data = urldecode($filter_data); //put serialized data in DB
		
		$name = get_req('filter_name', DOTY_ALPHANUM, '');
		$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_report_filter ".
			"(id_report, author, creation_date, filter_data, filter_name) VALUES ".
			"(".$_SESSION['report']['id_report'].", ".getLogUserId().", NOW(), '".serialize($_SESSION['report'])."', '$name')";
		
		if (!$output['success']=mysql_query($query)) {
			$output['error']=mysql_error();
			$output['debug']=$query;
			$output['ridebug']=print_r($data,true);
		} else {
			//if query is ok, I got the inserted ID and I put in session, telling the system I'm using it
			$row = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
			$_SESSION['report_saved'] = $row[0];
		}
		$json = new Services_JSON();
		aout($json->encode($output));
	} break;


	case 'delete_filter': {
		$output=array();
		$filter_id = get_req('filter_id', DOTY_ALPHANUM, '');
		if (mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_report_filter WHERE id_filter=$filter_id")) {
			$output['success']=true;
		} else {
			$output['success']=false;
		}
		$json = new Services_JSON();
		aout($json->encode($output));
	} break;

	case 'sched_enable' : {
		$output=array();
		$success=false;
		$message='';
		$id_sched=get_req('id', DOTY_INT, false);
		$value=get_req('val', DOTY_INT, -1);
		if ($value>=0 && $id_sched!==false) {
			$query="UPDATE ".$GLOBALS['prefix_lms']."_report_schedule SET enabled=$value ".
				"WHERE id_report_schedule=$id_sched";
			$success=mysql_query($query);
			$message=mysql_error();
		}
		$output['success']=$success;
		$output['message']=$message;
		$json = new Services_JSON();
		aout($json->encode($output));
	} break;

	case 'public_rep': {
		$output=array();
		$success=false;
		$message='';
		$id_rep=get_req('id', DOTY_INT, false);
		$value=get_req('val', DOTY_INT, -1);
		if ($value>=0 && $id_rep!==false) {
			$query="UPDATE ".$GLOBALS['prefix_lms']."_report_filter SET is_public=$value ".
				"WHERE id_filter=$id_rep";
			$success=mysql_query($query);
			$message=mysql_error();
		}
		$output['success']=$success;
		$output['message']=$message;
		$json = new Services_JSON();
		aout($json->encode($output));
	} break;

}

} break;

}

?>