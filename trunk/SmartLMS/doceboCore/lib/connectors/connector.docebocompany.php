<?php
/*-----------------------------------------------------------------------|
 | DOCEBO CORE - Framework							                     |
 | ======================================================================|
 | Docebo is the new name of SpaghettiLearning Project                   |
 |                                                                       |
 | Copyright (c) 2006 by Docebo s.rl.           						 |
 |                                                                       |
 | This program is free software. You can redistribute it and/or modify  |
 | it under the terms of the GNU General Public License as published by  |
 | the Free Software Foundation; either version 2 of the License.        |
 |-----------------------------------------------------------------------*/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');


require_once( dirname(__FILE__) . '/lib.connector.php' );

/**
 * class for define docebo courses connection to data source.
 *
 * @package admin-core
 * @subpackage io-operation
 * @version 	1.0
 * @author		Fabio Pirovano <fabio (@) docebo (.) com>
 * @access public
 */

class DoceboConnectorDoceboCompany extends DoceboConnector {

  	var $last_error = "";

 	// name, type
 	var $all_cols = array(
		array( 'code', 'text' ),
		array( 'name', 'text' ),
		array( 'description', 'text' ),
		array( 'lang_code', 'text' ),
		array( 'status', 'text' ),
		array( 'subscribe_method', 'int' ),
		array( 'permCloseLO', 'int' ),
		array( 'difficult', 'dropdown' ),
		array( 'show_progress', 'int' ),
		array( 'show_time', 'int' ),
		array( 'show_extra_info', 'int' ),
		array( 'show_rules', 'int' ),
		array( 'date_begin', 'date' ),
		array( 'date_end', 'date' ),
		array( 'valid_time', 'int' ),
		array( 'max_num_subscribe', 'int' ),
		array( 'selling', 'int' ),
		array( 'prize', 'int' )
	);

	var $mandatory_cols = array('code', 'name');

	var $default_cols = array(	'description' 		=> '',
								'lang_code' 		=> '',
								'status' 			=> '0',
								'subscribe_method' 	=> '',
								'permCloseLO' 		=> '',
								'difficult' 		=> 'medium',
								'show_progress' 	=> '1',
								'show_time' 		=> '1',
								'show_extra_info' 	=> '0',
								'show_rules' 		=> '0',
								'date_begin' 		=> '0000-00-00',
								'date_end' 			=> '0000-00-00',
								'valid_time' 		=> '0',
								'max_num_subscribe' => '0',
								'selling' 			=> '0',
								'prize' 			=> '');

	var $valid_filed_type 		= array( 'text','date','dropdown','yesno');

	var $dbconn 				= NULL;

	var $readwrite 				= 1; // read = 1, write = 2, readwrite = 3
	var $sendnotify = 1; // send notify = 1, don't send notify = 2

	var $name 					= "";
	var $description 			= "";

	var $on_delete = 1;  // unactivate = 1, delete = 2

	var $std_menu_to_assign 	= false;


	var $arr_id_inserted 		= array();

	function DoceboConnectorDoceboCompany($params) {

		$this->default_cols['lang_code'] = getDefaultLanguage();
		if( $params === NULL )
	  		return;
	  	else
			$this->set_config( $params );	// connection


	}

	function get_config() {

		return array(	'name' => $this->name,
						'description' => $this->description,
						'readwrite' => $this->readwrite,
						'sendnotify' => $this->sendnotify,
						'on_delete' => $this->on_delete);
	}

	function set_config( $params ) {

		if( isset($params['name']) )				$this->name = $params['name'];
		if( isset($params['description']) )			$this->description = $params['description'];
		if( isset($params['readwrite']) )			$this->readwrite = $params['readwrite'];
		if( isset($params['sendnotify']) )			$this->sendnotify = $params['sendnotify'];
		if( isset($params['on_delete']) )			$this->on_delete = $params['on_delete'];
	}

	function get_configUI() {
		return new DoceboConnectorDoceboCompanyUI($this);
	}

	function connect() {}

	function close() {}

	function get_type_name() { return "docebo-company"; }

	function get_type_description() { return "connector to docebo company"; }

	function get_name() { return $this->name; }

	function get_description() { return $this->description; }

	function is_readonly() { return (bool)($this->readwrite & 1); }

	function is_writeonly() { return (bool)($this->readwrite & 2); }

	function get_tot_cols(){
		return count( $this->all_cols );
	}

	/**
	 * @return array the array of columns descriptor
	 *				- DOCEBOIMPORT_COLNAME => string the name of the column
	 *				- DOCEBOIMPORT_COLID => string the id of the column (optional,
	 *										 same as COLNAME if not given)
	 *				- DOCEBOIMPORT_COLMANDATORY => bool TRUE if col is mandatory
	 *				- DOCEBOIMPORT_DATATYPE => the data type of the column
	 *				- DOCEBOIMPORT_DEFAULT => the default value for the column (Optional)
	 * For readonly connectos only 	DOCEBOIMPORT_COLNAME and DOCEBOIMPORT_DATATYPE
	 * are required
	**/
	function get_cols_descripor() {

		require_once($GLOBALS["where_framework"]."/class/class.fieldmap_company.php");

		$fmc=new FieldMapCompany();

		$col_descriptor = array();
		$predefined_fields=$fmc->getPredefinedFields(FALSE);


		foreach ($predefined_fields as $field_id=>$label) {

			$col_descriptor[] = array(
				DOCEBOIMPORT_COLNAME 		=> $label,
				DOCEBOIMPORT_COLID			=> $field_id,
				DOCEBOIMPORT_COLMANDATORY 	=> (in_array($field_id, $this->mandatory_cols) ? TRUE : FALSE),
				DOCEBOIMPORT_DATATYPE 		=> "text",
				DOCEBOIMPORT_DEFAULT 		=> ""
			);

		}


		$custom_fields=$fmc->getCustomFields(FALSE);

		foreach ($custom_fields as $field_id=>$label) {

			$col_descriptor[] = array(
				DOCEBOIMPORT_COLNAME 		=> $label,
				DOCEBOIMPORT_COLID			=> $field_id,
				DOCEBOIMPORT_COLMANDATORY 	=> FALSE,
				DOCEBOIMPORT_DATATYPE 		=> "text",
				DOCEBOIMPORT_DEFAULT 		=> ""
			);

		}

/*
		$lang =& DoceboLanguage::createInstance('company', 'crm');

		$col_descriptor = array();
		foreach($this->all_cols as $k => $col) {

			$col_descriptor[] = array(
				DOCEBOIMPORT_COLNAME 		=> $lang->def('_'.strtoupper($col[0])),
				DOCEBOIMPORT_COLID			=> $col[0],
				DOCEBOIMPORT_COLMANDATORY 	=> ( array_search($col[0], $this->mandatory_cols) === FALSE
													? false
													: true ),
				DOCEBOIMPORT_DATATYPE 		=> $col[1],
				DOCEBOIMPORT_DEFAULT 		=> ( $in = array_search($col[0], $this->default_cols) === FALSE
													? ''
													: $this->default_cols[$in] )
			);

		}
	*/
		return $col_descriptor;
	}

	function get_first_row() {

	}

	function get_next_row() {

	}

	function is_eof() {

	}

	function get_row_index() {}

	function get_tot_mandatory_cols() {

		return count($this->mandatory_cols);
	}

	function get_row_by_pk($pk) {

		require_once($GLOBALS["where_framework"]."/class/class.fieldmap_company.php");

		$ccm=new CoreCompanyManager();
		$res=$ccm->get_row_by_pk($pk, $this->get_name());

		return $res;

		$search_query = "
		SELECT company_id, imported_from_connection
		FROM ".$GLOBALS['prefix_lms']."_course
		WHERE 1";
		foreach($pk as $fieldname => $fieldvalue) {

			$search_query .= " AND $fieldname = '".addslashes($fieldvalue)."'";
		}

		$re_course = mysql_query($search_query);
		if(mysql_num_rows($re_course) == 0) return 0;
		if(!$re_course) return false;
		list($company_id, $imported_from) = mysql_fetch_row($re_course);

		if($this->get_name() != $imported_from)
			return 'jump';

		return $company_id;
	}

	function add_row( $row, $pk ) {
		//require_once( $GLOBALS['where_lms'] . '/setting.php' );
		require_once($GLOBALS["where_framework"]."/class/class.fieldmap_company.php");

		$fmc=new FieldMapCompany();

		$company_id = false;
/*
		if($row['code'] == '') 				$row['code'] = $this->default_cols['code'];
		if($row['name'] == '') 				$row['name'] = $this->default_cols['name'];
		if($row['description'] == '') 		$row['description'] = $this->default_cols['description'];
		if($row['lang_code'] == '') 		$row['lang_code'] = $this->default_cols['lang_code'];
		if($row['status'] == '') 			$row['status'] = $this->default_cols['status'];
		if($row['subscribe_method'] == '') 	$row['subscribe_method'] = $this->default_cols['subscribe_method'];
		if($row['permCloseLO'] == '') 		$row['permCloseLO'] = $this->default_cols['permCloseLO'];
		if($row['difficult'] == '') 		$row['difficult'] = $this->default_cols['difficult'];
		if($row['show_progress'] == '') 	$row['show_progress'] = $this->default_cols['show_progress'];
		if($row['show_time'] == '') 		$row['show_time'] = $this->default_cols['show_time'];
		if($row['show_extra_info'] == '') 	$row['show_extra_info'] = $this->default_cols['show_extra_info'];
		if($row['show_rules'] == '') 		$row['show_rules'] = $this->default_cols['show_rules'];
		if($row['date_begin'] == '') 		$row['date_begin'] = $this->default_cols['date_begin'];
		if($row['date_end'] == '') 			$row['date_end'] = $this->default_cols['date_end'];
		if($row['valid_time'] == '') 		$row['valid_time'] = $this->default_cols['valid_time'];
		if($row['max_num_subscribe'] == '') $row['max_num_subscribe'] = $this->default_cols['max_num_subscribe'];
		if($row['prize'] == '') 			$row['prize'] = $this->default_cols['prize'];
		if($row['selling'] == '') 			$row['selling'] = $this->default_cols['selling'];
*/

		$value=(!empty($row["company"]) ? $row["company"] : def("_NO_NAME", "company", "crm"));
		$predefined_data["name"]=$value;
		$value=$row["ctype"];
		$predefined_data["ctype_id"]=$value;
		$value=$row["cstatus"];
		$predefined_data["cstatus_id"]=$value;
		$value=$row["address"];
		$predefined_data["address"]=$value;
		$value=$row["tel"];
		$predefined_data["tel"]=$value;
		$value=$row["email"];
		$predefined_data["email"]=$value;
		$value=$row["vat_number"];
		$predefined_data["vat_number"]=$value;
		$predefined_data["imported_from_connection"]=$this->get_name();

		$custom_fields_arr=$fmc->getCustomFields(FALSE);
		$custom_data=array();

		foreach($custom_fields_arr as $field_id=>$label) {
			if (isset($row[$field_id])) {
				$custom_data[$field_id]=$row[$field_id];
			}
		}

		// check if the course identified by the pk alredy exits
		$company_id = $this->get_row_by_pk($pk);
		if($company_id === false) {
			$this->last_error = 'Error in search query : ( '.mysql_error().' )';
			return false;
		}
		if($company_id === 'jump') return true;


		$company_id=$fmc->saveFields($predefined_data, $custom_data, $company_id, FALSE, FALSE);

		if ($company_id > 0)
			$this->arr_id_inserted[] = $company_id;

		return ($company_id > 0 ? TRUE : FALSE);

		/*
		if($company_id != false) {

			if($this->cache_inserted)
				$this->arr_id_inserted[] = $id_course;

			if($this->sendnotify == 1) {
				// send notify
				if($is_add) {

					require_once($GLOBALS['where_framework'] . '/lib/lib.eventmanager.php');

					$msg_composer = new EventMessageComposer('admin_course_management', 'lms');

					$msg_composer->setSubjectLangText('email', '_ALERT_SUBJECT', false);
					$msg_composer->setBodyLangText('email', '_ALERT_TEXT', array(	'[url]' => $GLOBALS['lms']['url'],
																					'[course_code]' => $row['code'],
																					'[course]' => $row['name'] ) );

					$msg_composer->setSubjectLangText('sms', '_ALERT_SUBJECT_SMS', false);
					$msg_composer->setBodyLangText('sms', '_ALERT_TEXT_SMS', array(	'[url]' => $GLOBALS['lms']['url'],
																					'[course_code]' => $row['code'],
																					'[course]' => $row['name'] ) );

					require_once($GLOBALS['where_lms'] . '/lib/lib.course.php');
					$course_man = new Man_Course();
					$recipients = $course_man->getIdUserOfLevel($id_course);
					createNewAlert(	'CoursePropModified',
									'course',
									'add',
									'1',
									'Inserted course '.$_POST['course_name'],
									$recipients,
									$msg_composer );
				}
			}
			return true;
		}
		$this->last_error = 'Unknow error';
		return false; */
	}

	function _delete_by_id($company_id) {

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");

		$ccm=new CoreCompanyManager();
		$res=$ccm->deleteCompany($company_id);

		return $res;
	}

	function delete_bypk( $pk ) {
		return false;
		// check if the course identified by the pk alredy exits
		$company_id = $this->get_row_by_pk($pk);
		if($company_id === 'jump') return true;
		if($company_id === false) return false;
		if($company_id === 0) return true;

		return $this->_delete_by_id($company_id);
	}

	function delete_all_filtered( $arr_pk ) {

		$re  = true;
		foreach($arr_pk as $k => $pk) {

			$re &= $this->delete_bypk( $pk );
		}
		return $re;
	}

	function delete_all_notinserted() {
		$res=0;

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");

		$ccm=new CoreCompanyManager();

		$not_ins_arr=$ccm->find_all_notinserted($this->get_name(), $this->arr_id_inserted);

		foreach($not_ins_arr as $company_id) {

			$deleted=$this->_delete_by_id($company_id);

			if($deleted) {
				$res++;
			}
		}

		return $res;
	}

	function get_error() { return $this->last_error; }

}

/**
 * class for define docebo courses UI connection
 * @package		Docebo
 * @subpackage	ImportExport
 * @version 	1.0
 * @author		Fabio Pirovano <fabio (@) docebo (.) com>
 * @access public
 **/
class DoceboConnectorDoceboCompanyUI extends DoceboConnectorUI {

	var $connector 		= NULL;
	var $post_params 	= NULL;
	var $sh_next 		= TRUE;
	var $sh_prev 		= FALSE;
	var $sh_finish 		= FALSE;
	var $step_next 		= '';
	var $step_prev 		= '';
	var $available_menu = array();

	function DoceboConnectorDoceboCompanyUI( &$connector ) {

		require_once($GLOBALS['where_lms'].'/lib/lib.manmenu_course.php');

		$this->available_menu = getAllCustom();

		$this->connector =& $connector;
	}

	function _get_base_name() { return 'docebocompanyuiconfig'; }

	function get_old_name() { return $this->post_params['old_name']; }

	function parse_input( $get, $post ) {

		if( !isset($post[$this->_get_base_name()]) ) {

			// first call - first step, initialize variables
			$this->post_params = $this->connector->get_config();
			$this->post_params['step'] = '0';
			$this->post_params['old_name'] = $this->post_params['name'];
			if( $this->post_params['name'] == '' )
				$this->post_params['name'] = $this->lang->def('_CONN_NAME_EXAMPLE');

		} else {
			// get previous values
			$this->post_params = unserialize(urldecode($post[$this->_get_base_name()]['memory']));
			$arr_new_params = $post[$this->_get_base_name()];
			// overwrite with the new posted values
			foreach($arr_new_params as $key => $val) {
				if( $key != 'memory' && $key != 'reset' ) {
					$this->post_params[$key] = stripslashes($val);
				}
			}
		}
		$this->_load_step_info();
	}

	function _set_step_info( $next, $prev, $sh_next, $sh_prev, $sh_finish ) {
		$this->step_next = $next;
		$this->step_prev = $prev;
		$this->sh_next = $sh_next;
		$this->sh_prev = $sh_prev;
		$this->sh_finish = $sh_finish;
	}

	function _load_step_info() {

		$this->_set_step_info( '1', '0', FALSE, FALSE, TRUE );
	}

	function go_next() {
		$this->post_params['step'] = $this->step_next;
		$this->_load_step_info();
	}

	function go_prev() {
		$this->post_params['step'] = $this->step_prev;
		$this->_load_step_info();
	}

	function go_finish() {
		$this->connector->set_config( $this->post_params );
	}

	function show_next() { return $this->sh_next; }
	function show_prev() { return $this->sh_prev; }
	function show_finish() { return $this->sh_finish; }

	function get_htmlheader() {
		return '';
	}

	function get_html() {
	  	$out = '';
		switch( $this->post_params['step'] ) {
			case '0':
				$out .= $this->_step0();
			break;
		}
		// save parameters
		$out .=  $this->form->getHidden($this->_get_base_name().'_memory',
										$this->_get_base_name().'[memory]',
										urlencode(serialize($this->post_params)) );

		return $out;
	}

	function _step0() {

	  	// ---- name -----
	  	$out = $this->form->getTextfield(	$this->lang->def('_NAME'),
											$this->_get_base_name().'_name',
											$this->_get_base_name().'[name]',
											255,
											$this->post_params['name']);
		// ---- description -----
		$out .= $this->form->getSimpleTextarea( $this->lang->def('_DESCRIPTION'),
											$this->_get_base_name().'_description',
											$this->_get_base_name().'[description]',
											$this->post_params['description'] );
	  	// ---- access type read/write -----
	  	$out .= $this->form->getRadioSet( 	$this->lang->def('_ACCESSTYPE'),
		  									$this->_get_base_name().'_readwrite',
											$this->_get_base_name().'[readwrite]',
											array( 	$this->lang->def('_READ')  => '1',
													$this->lang->def('_WRITE') => '2',
													$this->lang->def('_READWRITE') => '3'),
											$this->post_params['readwrite']);
		// ---- on delete -> delete or unactivate -----
		$out .= $this->form->getRadioSet( 	$this->lang->def('_ON_COMPANY_DELETION'),
		  									$this->_get_base_name().'_on_delete',
											$this->_get_base_name().'[on_delete]',
											array( 	$this->lang->def('_DO_NOT_CHANGE')  => '1',
													$this->lang->def('_DEL') => '2'),
											$this->post_params['on_delete']);
	  	// ---- access type read/write -----
	  	$out .= $this->form->getRadioSet( 	$this->lang->def('_SENDNOTIFY'),
		  									$this->_get_base_name().'_sendnotify',
											$this->_get_base_name().'[sendnotify]',
											array( 	$this->lang->def('_SEND')  => '1',
													$this->lang->def('_DONTSEND') => '2'),
											$this->post_params['sendnotify']);

		return $out;
	}
}

function docebocompany_factory() {
	return new DoceboConnectorDoceboCompany(array());
}


?>
