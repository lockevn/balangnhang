<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package 	admin-library
 * @category 	File managment
 * @author 		Fabio Pirovano <fabio@docebo.com>
 * @version 	$Id: lib.download.php 1000 2007-03-23 16:03:43Z fabio $
 */

/**
 * able the user to download a specified file as an attachment
 *
 * @param string	$path		where the files is on the server filesystem without the filename
 * @param string	$filename	the name of the file
 * @param string	$ext		the extension of the file (.txt, .jpg ...)
 * @param string	$sendname	the name given to the downlodable file, if not passed it will be constructed in this way:
 *								assumed that $filename is [number]_[number]_[time]_[filename]
 *								the file sended will have the name [filename].$ext
 *
 * @return nothing
 */
function sendFile($path, $filename, $ext = NULL, $sendname = NULL) {

	//empty and close buffer
	if(!($GLOBALS['where_files_relative'] == substr($path, 0, strlen($GLOBALS['where_files_relative'])))) {
		$path = $GLOBALS['where_files_relative'].$path;
	}
	if($sendname === NULL) {
		$sendname = implode('_', array_slice(explode('_', $filename), 3));
		if($sendname == '') $sendname = $filename;
	}

	if($ext === NULL || $ext === false) {
		$ext = array_pop(explode('.', $filename), 1);

	}
	if(substr($sendname, - strlen($ext)) != $ext) $sendname .= '.'.$ext;

	@mysql_close();

	ob_end_clean();
	session_write_close();
	//ini_set("output_buffering", 0);
	//Download file
	//send file length info
	header('Content-Length:'. filesize($path.$filename));
	//content type forcing dowlad
	header("Content-type: application/download; charset=utf-8\n");
	//cache control
	header("Cache-control: private");
	//sending creation time
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	//content type
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		header('Pragma: private');
	}
	header('Content-Disposition: attachment; filename="'.$sendname.'"');
	//sending file
	$file=fopen($path.$filename, "rb");
	$i=0;
	if(!$file) return false;
	while(!feof($file)) {
		$buffer=fread($file, 4096);
		echo $buffer;
		if ($i % 100 == 0) {
			$i=0;
			@ob_end_flush();
		}
		$i++;
	}
	fclose($file);

	//and now exit
	exit();
}

function sendStrAsFile($string, $filename, $charset=false) {
	
	//empty and close buffer

	@mysql_close();

	ob_end_clean();
	session_write_close();
	//ini_set("output_buffering", 0);
	//Download file
	//send file length info
	header('Content-Length:'. strlen($string));
	//content type forcing dowlad
	header("Content-type: application/download".($charset ? "; charset=$charset" : "; charset=utf-8")."\n");
	//cache control
	header("Cache-control: private");
	//sending creation time
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	//content type
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		header('Pragma: private');
	}
	header('Content-Disposition: attachment; filename="'.$filename.'"');


	echo $string;
	
	//and now exit
	exit();
}
?>