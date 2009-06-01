<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system								*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

define("CERT_ID", 			0);
define("CERT_NAME", 		1);
define("CERT_DESCR", 		2);
define("CERT_LANG", 		3);
define("CERT_STRUCTURE", 	4);
define("CERT_CODE", 		5);

define("CERT_ID_COURSE", 	4);
define("CERT_AV_STATUS", 	5);

define("CERTIFICATE_PATH", '/doceboLms/certificate/');

define("AVS_NOT_ASSIGNED", 					0);
define("AVS_ASSIGN_FOR_ALL_STATUS", 		1);
define("AVS_ASSIGN_FOR_STATUS_INCOURSE", 	2);
define("AVS_ASSIGN_FOR_STATUS_COMPLETED", 	3);

define("ASSIGN_CERT_ID", 		0);
define("ASSIGN_COURSE_ID", 		1);
define("ASSIGN_USER_ID", 		2);
define("ASSIGN_OD_DATE", 		3);
define("ASSIGN_CERT_FILE", 		4);

class Certificate {
	
	function getCertificateList($name_filter = false, $code_filter = false) {
		
		$cert = array(); 
		$query_certificate = "
		SELECT id_certificate, name, description, base_language, cert_structure, code
		FROM ".$GLOBALS['prefix_lms']."_certificate"
		." WHERE meta = 0";
		
		if ($name_filter && $code_filter)
			$query_certificate .= " AND name LIKE '%".$name_filter."%'" .
									" AND code LIKE '%".$code_filter."%'";
		elseif ($name_filter)
			$query_certificate .= " AND name LIKE '%".$name_filter."%'";
		elseif ($code_filter)
			$query_certificate .= " AND code LIKE '%".$code_filter."%'";
		
		$query_certificate .= " ORDER BY name";
		
		$re_certificate = mysql_query($query_certificate);
		
		while($row = mysql_fetch_row($re_certificate))
		{
			$cert[$row[CERT_ID]] = $row;
		}
		
		return $cert;
	}
	
	function getCourseCertificate($id_course) {
		
		$cert = array(); 
		$query_certificate = "
		SELECT id_certificate, available_for_status
		FROM ".$GLOBALS['prefix_lms']."_certificate_course
		WHERE id_course = '".$id_course."' ";
		$re_certificate = mysql_query($query_certificate);
		while(list($id, $available_for_status) = mysql_fetch_row($re_certificate)) {
			
			$cert[$id] = $available_for_status;
		}
		return $cert;
	}
	
	/**
	 * @return array 	idcourse => array( idcert => array( CERT_ID, CERT_NAME, CERT_DESCR, CERT_LANG, CERT_STRUCTURE, CERT_ID_COURSE, CERT_AV_STATUS ) )
	 */
	function certificateForCourses($arr_course = false, $base_language = false) {
		
		$query_certificate = ""
		." SELECT c.id_certificate, c.name, c.description, c.base_language, course.id_course, course.available_for_status "
		." FROM ".$GLOBALS['prefix_lms']."_certificate AS c "
		." 		JOIN ".$GLOBALS['prefix_lms']."_certificate_course AS course"
		." WHERE c.id_certificate = course.id_certificate "
		." 		AND course.available_for_status <> '".AVS_NOT_ASSIGNED."' ";
		if($arr_course !== false && !empty($arr_course)) {
			$query_certificate .= " AND course.id_course IN ( ".implode(',', $arr_course)." )";
		}
		if($base_language !== false) {
			$query_certificate .= " AND c.base_language = '".$base_language."' ";
		}
		$query_certificate .= " ORDER BY course.available_for_status, c.name";
		
		$re = mysql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while($row = mysql_fetch_row($re)) {
			
			$list_of[$row[CERT_ID_COURSE]][$row[CERT_ID]] = $row;
		}
		return $list_of;
	}
	
	function numberOfCertificateReleased($id_certificate = false) {
		
		$query_certificate = "
		SELECT id_certificate, COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE 1 ";
		if($id_certificate !== false) $query_certificate .= " AND id_certificate = '".$id_certificate."' ";
		
		$re = mysql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while(list($id_c, $number) = mysql_fetch_row($re)) {
			$list_of[$id_c] = $number;
		}
		reset($list_of);
		if($id_certificate !== false) return current($list_of);
		return $list_of;
	}
	
	function certificateReleased($id_user, $arr_course = false) {
		
		$query_certificate = "
		SELECT id_course, id_certificate, on_date
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE id_user = '".$id_user."' ";
		if($arr_course) {
			$query_certificate .= " AND id_course IN ( ".implode(',', $arr_course)."";
		}
		$re = mysql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while(list($id_course, $id_cert, $on_date) = mysql_fetch_row($re)) {
			
			$list_of[$id_course][$id_cert] = $on_date;
		}
		return $list_of;
	}
	
	function certificateReleasedMultiUser($arr_user = false, $arr_course = false) {
		
		$query_certificate = "
		SELECT id_user, id_certificate, id_course, on_date
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE 1 ";
		if(is_array($arr_user) && !empty($arr_user)) {
			$query_certificate .= " AND id_user IN ( ".implode(',', $arr_user)."";
		}
		if(is_array($arr_course) && !empty($arr_course)) {
			$query_certificate .= " AND id_course IN ( ".implode(',', $arr_course)."";
		}
		$re = mysql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while(list($id_user, $id_course, $id_cert, $on_date) = mysql_fetch_row($re)) {
			
			$list_of[$id_user][$id_cert]['on_date'] = $on_date;
			$list_of[$id_user][$id_cert]['id_course'] = $id_course;
		}
		return $list_of;
	}
	
	function numOfCertificateReleasedForCourse($id_course) {
		
		$query_certificate = "
		SELECT id_certificate, COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE id_course = '".$id_course."' 
		GROUP BY id_certificate ";
		$re = mysql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while(list($id_cert, $num_of) = mysql_fetch_row($re)) {
			$list_of[$id_cert] = $num_of;
		}
		return $list_of;
	}
	
	function certificateReleasedForCourse($id_course) {
		
		$query_certificate = "
		SELECT id_user, id_certificate, on_date
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE id_course = '".$id_course."' ";
		$re = mysql_query($query_certificate);
		if(!$re) return array();
		
		$list_of = array();
		while(list($id_user, $id_cert, $on_date) = mysql_fetch_row($re)) {
			$list_of[$id_user][$id_cert] = $on_date;
		}
		return $list_of;
	}
	
	function isReleased($id_certificate, $id_course, $id_user) {
		
		$query_certificate = "
		SELECT cert_file
		FROM ".$GLOBALS['prefix_lms']."_certificate_assign
		WHERE id_certificate = '".$id_certificate."'
			 AND id_course = '".$id_course."' 
			 AND id_user = '".$id_user."' ";
		
		$re = mysql_query($query_certificate);
		if(!$re) return false;
		return (mysql_num_rows($re) > 0);
	}
	
	function canRelease($av_for_status, $user_status) {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		
		switch($av_for_status) {
			case AVS_NOT_ASSIGNED 				: { return false; };break;
			case AVS_ASSIGN_FOR_ALL_STATUS 		: { return true; };break;
			case AVS_ASSIGN_FOR_STATUS_INCOURSE : { return ($user_status == _CUS_BEGIN); };break;
			case AVS_ASSIGN_FOR_STATUS_COMPLETED : { return ($user_status == _CUS_END); };break;
		}
		return false;
	}
	
	function updateCertificateCourseAssign($id_course, $list_of_assign) {
		
		$cert = array(); 
		$query_certificate = "SELECT id_certificate, available_for_status FROM ".$GLOBALS['prefix_lms']."_certificate_course WHERE id_course = '".$id_course."' ";
		$re_certificate = mysql_query($query_certificate);
		while(list($id, $available_for_status) = mysql_fetch_row($re_certificate)) { $actual_assign[$id] = $available_for_status; }
		
		$re = true;
		foreach($list_of_assign as $id_cert => $new_status) {
			
			if(($new_status == '0') && isset($actual_assign[$id_cert])) {
				// delete
				$query_certificate = "
				DELETE FROM ".$GLOBALS['prefix_lms']."_certificate_course
				WHERE id_certificate = '".$id_cert."' AND id_course = '".$id_course."' ";
				
			} elseif(isset($actual_assign[$id_cert])) {
				// update
				$query_certificate = "
				UPDATE ".$GLOBALS['prefix_lms']."_certificate_course
				SET available_for_status = '".$new_status."'
				WHERE id_certificate = ".(int)$id_cert." AND id_course = ".(int)$id_course." ";
			} else {
				// insert
				$query_certificate = "
				INSERT INTO ".$GLOBALS['prefix_lms']."_certificate_course
				( id_certificate, id_course, available_for_status ) VALUES 
				( ".(int)$id_cert.", ".(int)$id_course.", ".(int)$new_status." )";
			}
			$re &= mysql_query($query_certificate);	
		}
		return $re;
	}
	
	function getSubstitutionArray($id_user, $id_course) {
		
		$query_certificate = "
		SELECT file_name, class_name
		FROM ".$GLOBALS['prefix_lms']."_certificate_tags ";
		$re = mysql_query($query_certificate);
		
		$subst = array();
		while(list($file_name, $class_name) = mysql_fetch_row($re)) {
			
			if(file_exists($GLOBALS['where_lms'].'/lib/certificate/'.$file_name)) {
				
				require_once($GLOBALS['where_lms'].'/lib/certificate/'.$file_name);
				$instance = new $class_name($id_user, $id_course);
				$this_subs = $instance->getSubstitution();
				$subst = $subst + $this_subs;
			}
		}
		return $subst;	
	}
	
	function send_preview_certificate($id_certificate, $array_substituton = false) {
		
		$query_certificate = "
		SELECT name, cert_structure, base_language, orientation, bgimage
		FROM ".$GLOBALS['prefix_lms']."_certificate
		WHERE id_certificate = '".$id_certificate."'";
		list($name, $cert_structure, $base_language, $orientation, $bgimage) = mysql_fetch_row(mysql_query($query_certificate));
		
		//require_once($GLOBALS['where_framework'].'/addons/html2pdf/html2fpdf.php');
		
		if($array_substituton !== false) {
			$cert_structure = str_replace(array_keys($array_substituton), $array_substituton, $cert_structure);
		}
		$cert_structure = fillSiteBaseUrlTag($cert_structure);
		
		$name = str_replace(
			array('\\', '/', 	':', 	'\'', 	'\*', 	'?', 	'"', 	'<', 	'>', 	'|'),
			array('', 	'', 	'', 	'', 	'', 	'', 	'', 	'', 	'', 	'' ),
			$name
		);
		
		$name .= '.pdf';
		
		$this->getPdf($cert_structure, $name, $bgimage, $orientation, true, false);
	}
	
	function getPdf($html, $name, $img = false, $orientation = 'P', $download = true, $facs_simile = false, $for_saving = false)
	{
		
		require_once($GLOBALS['where_framework'].'/addons/tcpdf/tcpdf.php');

		$pdf = new PDF_php4($orientation);
			
		if($for_saving)
			return $pdf->getPdf($html, $name, $img, $download, $facs_simile, $for_saving);
		else
			$pdf->getPdf($html, $name, $img, $download, $facs_simile, $for_saving);
	}
	
	function getPdfPhp5($html, $name, $orientation = 'P', $img = false, $facs_simile = true, $for_saving = false)
	{
		if($orientation == 'P' || $orientation == 'p')
			$orientation = 'portrait';
		else
			$orientation = 'landspace';
		
		require_once($GLOBALS['where_framework'].'/addons/dompdf/dompdf_config.inc.php');
		
		if(get_magic_quotes_gpc())
			$html = stripslashes($html);
		
		$query =	"SELECT lang_browsercode, lang_direction"
					." FROM ".$GLOBALS['prefix_fw']."_lang_language"
					." WHERE lang_code = '".getLanguage()."'";
		
		list($lang_code, $lang_direction) = mysql_fetch_row(mysql_query($query));
		
		$lg = array();
		$lg['a_meta_charset'] = "UTF-8"; 
		$lg['a_meta_dir'] = 'rtl';
		$lg['a_meta_language'] = 'fa'; 
		$lg['w_page'] = "page";
		
		$dompdf = new DOMPDF();
		
		$old_limit = ini_set("memory_limit", "80M");
		
		if ($img != '')
			$html =	'<html><head>'
					.'<style>'
					.'body {background-image: url('.$img.') no-repeat;}'
					.'</style>'
					.'</head><body>'
					.$html
					.'</body></html>';
		else
			$html =	'<html><head></hedad><body>'
					.$html
					.'</body></html>';
		
		$facs_simile_text =	'<script type="text/php">

  $font = Font_Metrics::get_font("verdana");;
  $size = 6;
  $color = array(0,0,0);
  $text_height = Font_Metrics::get_font_height($font, $size);

  $foot = $pdf->open_object();
  
  $w = $pdf->get_width();
  $h = $pdf->get_height();

  // Draw a line along the bottom
  $y = $h - 2 * $text_height - 24;
  $pdf->line(16, $y, $w - 16, $y, $color, 1);

  $y += $text_height;

  $text = "Job: 132-003";
  $pdf->text(16, $y, $text, $font, $size, $color);

  $pdf->close_object();
  $pdf->add_object($foot, "all");

  global $initials;
  $initials = $pdf->open_object();
  
  // Add an initals box
  $text = "Initials:";
  $width = Font_Metrics::get_text_width($text, $font, $size);
  $pdf->text($w - 16 - $width - 38, $y, $text, $font, $size, $color);
  $pdf->rectangle($w - 16 - 36, $y - 2, 36, $text_height + 4, array(0.5,0.5,0.5), 0.5);
    

  $pdf->close_object();
  $pdf->add_object($initials);
 
  // Mark the document as a duplicate
  $pdf->text(110, $h - 240, "F a c - s i m i l e", Font_Metrics::get_font("verdana", "bold"),
             110, array(0.85, 0.85, 0.85), 0, -52);

  $text = "F a c - s i m i l e";  

  // Center the text
  $width = Font_Metrics::get_text_width($text, $font, $size);
  $pdf->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);
</script>

</body>';
		
		if($facs_simile)
			$html = str_replace('</body>', $facs_simile_text, $html);
		
		$dompdf->load_html($html);
		
		$dompdf->setLanguageArray($lg);
		
		$dompdf->set_paper('a4', $orientation);
		
		$dompdf->render();
		
		if(!$for_saving)
		{
			$dompdf->stream($name.'.pdf');
			
			exit(0);
		}
		
		return $dompdf->output();
	}
	
	function send_facsimile_certificate($id_certificate, $id_user, $id_course, $array_substituton = false)
	{
		$query_certificate = "
		SELECT name, cert_structure, base_language, orientation, bgimage
		FROM ".$GLOBALS['prefix_lms']."_certificate
		WHERE id_certificate = '".$id_certificate."'";
		list($name, $cert_structure, $base_language, $orientation, $bgimage) = mysql_fetch_row(mysql_query($query_certificate));
		
		if($array_substituton !== false) {
			$cert_structure = str_replace(array_keys($array_substituton), $array_substituton, $cert_structure);
		}
		$cert_structure = fillSiteBaseUrlTag($cert_structure);
		
		$this->getPdf($cert_structure, $name, $bgimage, $orientation, true, true);
	}
	
	function send_certificate($id_certificate, $id_user, $id_course, $array_substituton = false)
	{
		$id_meta = get_req('idmeta', DOTY_INT, 0);
		
		if(!isset($_GET['idmeta']))
			$query_certificate = "
			SELECT cert_file
			FROM ".$GLOBALS['prefix_lms']."_certificate_assign 
			WHERE id_certificate = '".$id_certificate."'
				 AND id_course = '".$id_course."' 
				 AND id_user = '".$id_user."' ";
		else
			$query_certificate = "
			SELECT cert_file
			FROM ".$GLOBALS['prefix_lms']."_certificate_meta_assign 
			WHERE idUser = '".$id_user."'
			AND idMetaCertificate = '".$id_meta."'";
		
		$re = mysql_query($query_certificate);
		echo mysql_error();
		if((mysql_num_rows($re) > 0)) {
			
			require_once($GLOBALS['where_framework'].'/lib/lib.download.php');
			list($cert_file) = mysql_fetch_row($re);
			sendFile(CERTIFICATE_PATH, $cert_file);
			return;
		}
		
		$query_certificate = "
		SELECT name, cert_structure, base_language, orientation, bgimage
		FROM ".$GLOBALS['prefix_lms']."_certificate
		WHERE id_certificate = '".$id_certificate."'";
		list($name, $cert_structure, $base_language, $orientation, $bgimage) = mysql_fetch_row(mysql_query($query_certificate));
		
		require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
		
		if($array_substituton !== false) {
			$cert_structure = str_replace(array_keys($array_substituton), $array_substituton, $cert_structure);
		}
		$cert_structure = fillSiteBaseUrlTag($cert_structure);
		
		$cert_file = $id_course.'_'.$id_certificate.'_'.time().'_'.$name.'.pdf';
		
		sl_open_fileoperations();
		if(!$fp = sl_fopen(CERTIFICATE_PATH.$cert_file, 'w')) { sl_close_fileoperations(); return false; }
		if(!fwrite($fp, $this->getPdf($cert_structure, $name, $bgimage, $orientation, false, false, true))) { sl_close_fileoperations(); return false; }
		fclose($fp);
		sl_close_fileoperations();
		
		//save the generated file in database
		if(!isset($_GET['idmeta']))
			$query = "INSERT INTO ".$GLOBALS['prefix_lms']."_certificate_assign " 
			." ( id_certificate, id_course, id_user, on_date, cert_file ) "
			." VALUES "
			." ( '".$id_certificate."', '".$id_course."', '".$id_user."', '".date("Y-m-d H:i:s")."', '".addslashes($cert_file)."' ) ";
		else
			$query =	"INSERT INTO ".$GLOBALS['prefix_lms']."_certificate_meta_assign " 
						." ( idUser, idMetaCertificate, idCertificate, on_date, cert_file ) "
						." VALUES "
						." ('".$id_user."', '".$id_meta."', '".$id_certificate."', '".date("Y-m-d H:i:s")."', '".addslashes($cert_file)."' ) ";
		
		if(!mysql_query($query)) return false;
		
		$this->getPdf($cert_structure, $name, $bgimage, $orientation, true, false);
	}
	
	function getCourseForCertificate($id_certificate)
	{
		$id_course = array();
		
		$query_id_course = "SELECT id_course" .
						" FROM ".$GLOBALS['prefix_lms']."_certificate_course" .
						" WHERE id_certificate = '".$id_certificate."'" .
						" AND available_for_status <> '".AVS_NOT_ASSIGNED."'";
		
		$result_id_course = mysql_query($query_id_course);
		
		while (list($id_course_find) = mysql_fetch_row($result_id_course))
			$id_course[] = $id_course_find;
		
		return $id_course;
	}
	
	function getInfoForCourseCertificate($id_course, $id_certificate, $id_user = false)
	{
		$info = array();
		
		$query = "SELECT *" .
				" FROM ".$GLOBALS['prefix_lms']."_certificate_assign" .
				" WHERE id_certificate = '".$id_certificate."'" .
				" AND id_course = '".$id_course."'";
		if ($id_user)
			$query .= " AND id_user = $id_user";
		
		$result = mysql_query($query);
		
		while ($row = mysql_fetch_row($result))
			$info[] = $row; 
		
		return $info;
	}
	
	function getCertificateInfo($id_certificate)
	{
		$info = array();
		
		$query = "SELECT id_certificate, name, description, base_language, cert_structure" .
				" FROM ".$GLOBALS['prefix_lms']."_certificate" .
				" WHERE id_certificate = '".$id_certificate."'";
		
		$result = mysql_query($query);
		
		while ($row = mysql_fetch_row($result))
			$info[$row[CERT_ID]] = $row; 
		
		return $info;
	}
	
	function delCertificateForUserInCourse($id_certificate, $id_user, $id_course)
	{
		$query = "DELETE " .
				" FROM ".$GLOBALS['prefix_lms']."_certificate_assign " .
				" WHERE id_certificate = '".$id_certificate."'" .
				" AND id_course = '".$id_course."'" .
				" AND id_user = '".$id_user."'";
		
		return mysql_query($query);
	}
	
	function getNumberOfCertificateForCourse($id_certificate, $id_course)
	{
		$query = "SELECT COUNT(*)" .
				" FROM ".$GLOBALS['prefix_lms']."_certificate_assign" .
				" WHERE id_certificate = '".$id_certificate."'" .
				" AND id_course = '".$id_course."'";
		
		list ($res) = mysql_fetch_row(mysql_query($query));
		
		return $res;
	}
}


?>