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

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @package 	admin-library
 * @category 	wrapper
 * @version 	$Id:$
 * @author		Fabio Pirovano
 */

class FSock {
	
	var $_user_agent = "PHP Script";
	var $_protocol = 'http';
	var $_prot_version = '1.1';
	var $_stream_timeout = 5;
	
	var $_header = false;
	var $_footer = false;
	
	var $_errno = 0;
	var $_errstr = '';
	
	function Fsock($protocol = false, $version = false) {
	
		if($protocol != false) $this->_protocol = $protocol;
		if($version != false) $this->_prot_version = $version;
	}
	
	function setProtocol($new_protocol, $version) {
		$this->_protocol = $new_protocol;
		$this->_prot_version = $version;
	}
	
	/**
	 * The only purpose of this function is to send the message to the server, read the server answer,
	 * discard the header and return the other content
	 * fi you need the header yuo can use the apropiate funtion in order to retrive it
	 *
	 * @param 	string	$url 		the server url
	 * @param 	string	$port 		the server port
	 * @param	string 	$get_params	the get_params
	 *
	 * @return 	json 	
	 */
	function send_request($url, $port = '80', $get_params = '') {

		$json_response = '';
		if(strpos($url, 'http') !== 0) $url .= 'http://';
		$tmp_url = parse_url($url);
					
		$this->_header = false;
		$this->_footer = false;
		
		if(( $sock = fsockopen($tmp_url['host'], $port, $this->errno, $this->errstr, $this->_stream_timeout)) !== false) {
			
			socket_set_timeout($sock, $this->_stream_timeout);
			
			if(isset($tmp_url['path'])) $path = ($tmp_url['path']{0} == '/' ? '' : '/' ).$tmp_url['path'];
			else $path = '/';
			
		    $request  = "GET ".$path.$get_params." HTTP/1.1\r\n";
		    $request .= "Host: ".$tmp_url['host']."\r\n";
		    $request .= "User-Agent: ".$this->_user_agent."\r\n";
		    $request .= "Accept: text/xml,application/xml,application/xhtml+xml,";
		    $request .= "text/html;q=0.9,text/plain;q=0.8,video/x-mng,image/png,";
		    $request .= "image/jpeg,image/gif;q=0.2,text/css,*/*;q=0.1\r\n";
		    $request .= "Accept-Language: en-us, en;q=0.50\r\n";
		    $request .= "Accept-Encoding: gzip, deflate, compress;q=0.9\r\n";
		    $request .= "Connection: Close\r\n\r\n";
			
		    fputs ( $sock, $request );
		    
			$header = '';
			do {
				$header .= fgets ( $sock, 4096 );
			} while( strpos ( $header, "\r\n\r\n" ) === false );
			$info = $this->_decode_header ( $header );
			
			$body = '';
			while(!feof($sock)) {
				$body .= fread ( $sock, 8192 );
			}
			fclose ( $sock );

			$server_response = $this->_decode_body ( $info, $body );
		} else {
			return false;
		}
		return $server_response;
	}
	
	
	/**
	 * Thanks to : jbr at ya-right dot com
	 * http://it2.php.net/manual/it/function.fsockopen.php
	 * for the HTTP 1.1 implementation
	 */
	function _decode_header($str) {
	    
	    $out = array ();
	    $part = preg_split ( "/\r?\n/", $str, -1, PREG_SPLIT_NO_EMPTY );
		for( $h = 0; $h < sizeof ( $part ); $h++ ) {
			
			if ( $h != 0 ) {
	        
				$pos = strpos ( $part[$h], ':' );
				$k = strtolower ( str_replace ( ' ', '', substr ( $part[$h], 0, $pos ) ) );
				$v = trim ( substr ( $part[$h], ( $pos + 1 ) ) );
			} else {
				
				$k = 'status';
	            $v = explode ( ' ', $part[$h] );
	            $v = $v[1];
	        }
	        if ( $k == 'set-cookie' ) {
				$out['cookies'][] = $v;
	        } else if ( $k == 'content-type' ) {
	            
	            if(($cs = strpos ($v, ';')) !== false ) { $out[$k] = substr ( $v, 0, $cs ); }
	            else { $out[$k] = $v; }
			} else {
				$out[$k] = $v;
			}
	    }
	    $this->_header = $out;
	    return $out;
	}
	
	function _decode_body( $info, $str, $eol = "\r\n" ) {
	   
	    $tmp = $str;
	    $add = strlen ( $eol );
	    if ( isset ( $info['transfer-encoding'] ) && $info['transfer-encoding'] == 'chunked' ) {
	        
	        do {
	            $tmp = ltrim ( $tmp );
	            $pos = strpos ( $tmp, $eol );
	            $len = hexdec ( substr ( $tmp, 0, $pos ) );
	            if ( isset ( $info['content-encoding'] ) )  {
	                $str .= gzinflate ( substr ( $tmp, ( $pos + $add + 10 ), $len ) );
	            } else {
	                $str .= substr ( $tmp, ( $pos + $add ), $len );
	            }
	            $tmp = substr ( $tmp, ( $len + $pos + $add ) );
	            $check = trim ( $tmp );
	        } while ( ! empty ( $check ) );
	    }
	    else if ( isset ( $info['content-encoding'] ) ) {
	        $str = gzinflate ( substr ( $tmp, 10 ) );
	    }else {
	    	$str = $tmp;
	    }
	    $this->_body = $str;
	    return $str;
	}
	
}

?>