<?php
// php file to be accessed via xmlhttprequests
// retrieves, inserts and edits glossary terms

require("../../config.php");

if(!isloggedin())
	exit;

$data = required_param("data", PARAM_RAW);

$d = json_decode(stripslashes($data));

switch($d->act)
{
	case 'get':
		// Get all terms for a specific source language
		$data = get_records('pgq_terms', 'userid', $USER->id);
		$ret = array();
		$ret["terms"] = array();
		if ($data !== FALSE)
		{
			foreach ($data as $term)
			{
//				if ($d->source_lang && $term->source_lang == $d->source_lang)
//				{
//					array_push($ret["terms"], $term);
//				}
//				if ($d->target_lang && $term->target_lang == $d->target_lang)
//				{
					array_push($ret["terms"], $term);
//				}
			}
			$ret["ok"] = 1;
			print json_encode($ret);
		}
		else
			print json_encode(array("ok"=>0));
		break;
	case 'insert':
		$term = new object();
		$term->userid = $USER->id;
		$term->source_lang = $d->source_lang;
		$term->target_lang = $d->target_lang;
		$term->sl_value = $d->sl_value;
		$term->tl_value = $d->tl_value;
		$term->sl_notes = $d->sl_notes;
		$term->tl_notes = $d->tl_notes;
		$termid = insert_record('pgq_terms', $term);
		if (!$termid)
		{
			print json_Encode(array("ok" => 0));
		}
		else
			print json_encode(array("ok" => 1, "id"=>$termid));
		break;
	case 'edit':
		$term = new object();
		$term->userid = $USER->id;
		$term->id = $d->id;
		$term->source_lang = $d->source_lang;
		$term->target_lang = $d->target_lang;
		$term->sl_value = $d->sl_value;
		$term->tl_value = $d->tl_value;
		$term->sl_notes = $d->sl_notes;
		$term->tl_notes = $d->tl_notes;

		$r = update_record('pgq_terms', $term);
		// XXX: did this fail?
		if ($r)
			print json_encode(array("ok" => 1));
		else
			print json_encode(array("ok" => 0));
		break;
	case 'delete':					
		$r = delete_records('pgq_terms', 'id', $d->id, 'userid', $USER->id);
		// XXX: did this fail?
		if ($r)
			print json_encode(array("ok" => 1));
		else
			print json_encode(array("ok" => 0));
		break;
}

?>
