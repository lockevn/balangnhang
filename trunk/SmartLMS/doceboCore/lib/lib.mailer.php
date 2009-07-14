<?php
/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System                       */
/* ======================================================================*/
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS['where_framework'].'/addons/phpmailer/class.phpmailer.php');
require_once($GLOBALS['where_framework'].'/addons/phpmailer/language/phpmailer.lang-en.php');
require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');

//property name: multisending mode
define("MAIL_MULTIMODE", "multimode");
//multisending properties
define("MAIL_SINGLE", "single");
define("MAIL_CC", "cc");
define("MAIL_BCC", "bcc");

define("MAIL_WORDWRAP", "wordwrap");
define("MAIL_CHARSET", "charset");
define("MAIL_HTML", "is_html");
define("MAIL_SINGLETO", "singleto");

//property name: use or not acl names (taken from DB, slower if used)
define("MAIL_SENDER_ACLNAME", "use_sender_aclname");
define("MAIL_RECIPIENT_ACLNAME", "use_recipient_aclname");
define("MAIL_REPLYTO_ACLNAME", "use_replyto_aclname");

//property name: reply to parameters
define("MAIL_REPLYTO", "replyto");

//specify if class properties should be reset after sending
define("MAIL_RESET", "reset");





class DoceboMailer extends PHPMailer {

	//internal acl_manager instance
	var $acl_man; 
	
	
	var $utf8_trans_tbl; //Utf-8 translation table
	
	//var $reset_to_default = true;
	
	//default config for phpmailer, to set any time we send a mail, except for user-defined params
	var $default_conf = array(
		MAIL_MULTIMODE => MAIL_SINGLE,
		MAIL_SENDER_ACLNAME => false,
		MAIL_RECIPIENT_ACLNAME => false,
		MAIL_REPLYTO_ACLNAME => false,
		MAIL_HTML => true,
		MAIL_WORDWRAP => 0,
		MAIL_CHARSET => 'Utf-8',
		MAIL_SINGLETO => true
		//MAIL_ = ;
		//MAIL_ = ;
	);
	
	
	//the constructor
	function DoceboMailer($params=false) {
		$this->acl_man = new DoceboACLManager();
		
		if (is_array($params)) {
			//manage addictional parameters
			//$params should represent custom default configuration
		}
		
		//set initial default value
		$this->ResetToDefault();
		
		//write translation table for utf-8
/*
		$this->utf8_trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$this->utf8_trans_tbl = array_flip($this->utf8_trans_tbl);
		// changing translation table to UTF-8
		foreach( $this->utf8_trans_tbl as $key => $value ) {
			$this->utf8_trans_tbl[$key] = iconv( 'ISO-8859-1', 'UTF-8', $value );
		}*/
	}

	
	//return instance of the class
	function &getInstance() {
		if(!isset($GLOBALS['mailer'])) {
			$GLOBALS['mailer'] = new DoceboMailer();
		}
		return $GLOBALS['mailer'];
	}


	//convert html into plain txt in utf-8 avoiding the bug
	function ConvertToPlain_UTF8(&$html) {
		    
		//$string = strip_tags($html);
		
		// replace numeric entities
		//$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		//$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
		// replace literal entities
		
		//return strtr($string, $this->utf8_trans_tbl);
		$tags = array();
		return kses($html, $tags);
	}


	//restore default configuration after sending mail
	function ResetToDefault() {
		$this->From = '';
		$this->FromName = '';
		$this->CharSet = $this->default_conf[MAIL_CHARSET];
		$this->WordWrap = $this->default_conf[MAIL_WORDWRAP];
		$this->IsHTML($this->default_conf[MAIL_HTML]);
		$this->SingleTo = true;
		$this->Subject = '';
		$this->Body = '';
		$this->AltBody = '';
		//...
		
		//$this->ClearAddresses();
		//$this->ClearCCs();
		//$this->ClearBCCs();
		$this->ClearReplyTos();
		$this->ClearAllRecipients();
		$this->ClearAttachments();
		$this->ClearCustomHeaders();
	}
	
	//sendmail function
	function SendMail($sender, &$recipients, &$subject, &$body, $attachments=false, $params=false) {
	
		//analyze params, can be a string or an associative array
		if (is_string($params)) {
			//parse string params (TO DO)
			//...
			$temp = $params;
			$params=array();
			//parse $temp ...
		} elseif (!is_array($params)) $params=array();
		
		//set properties, overwrite default props if  redefined in $params ...
		if (isset($params[MAIL_WORDWRAP])) $conf_arr[MAIL_WORDWRAP] = $params[MAIL_WORDWRAP];
		if (isset($params[MAIL_HTML])) $conf_arr[MAIL_HTML] = $params[MAIL_HTML];
		if (isset($params[MAIL_SINGLETO])) $conf_arr[MAIL_SINGLETO] = $params[MAIL_SINGLETO];
		  
		$conf_arr[MAIL_SENDER_ACLNAME] = (isset($params[MAIL_SENDER_ACLNAME]) ? $params[MAIL_SENDER_ACLNAME] : $this->default_conf[MAIL_SENDER_ACLNAME]); 
		$conf_arr[MAIL_RECIPIENT_ACLNAME] = (isset($params[MAIL_RECIPIENT_ACLNAME]) ? $params[MAIL_RECIPIENT_ACLNAME] : $this->default_conf[MAIL_RECIPIENT_ACLNAME]);
		$conf_arr[MAIL_REPLYTO_ACLNAME] = (isset($params[MAIL_REPLYTO_ACLNAME]) ? $params[MAIL_REPLYTO_ACLNAME] : $this->default_conf[MAIL_REPLYTO_ACLNAME]);
		$conf_arr[MAIL_MULTIMODE] = (isset($params[MAIL_MULTIMODE]) ? $params[MAIL_MULTIMODE] : $this->default_conf[MAIL_MULTIMODE]);
			
		if (isset($params[MAIL_CHARSET])) $conf_arr[MAIL_CHARSET] = $params[MAIL_CHARSET];
		if (isset($params[MAIL_REPLYTO])) $conf_arr[MAIL_REPLYTO] = $params[MAIL_REPLYTO];
  	
		$_sender = '';
		$_recipients = array();
		$_replyto = array();
		//$_attachments = array();
		
		//check each time because global configuration may have changed since last call
		if (isset($GLOBALS['use_smtp'])) {
			if ($GLOBALS['use_smtp']=='on') {
				$this->IsSMTP();
				$this->Hostname = $GLOBALS['framework']['smtp_host'];
				$this->Username = $GLOBALS['framework']['smtp_user'];
				$this->Password = $GLOBALS['framework']['smtp_pwd'];
			} else {
				$this->IsMail();
			}
		} else {
			$this->IsMail();
		}
  	
		//configure sending address
		//----------------------------------------------------------------------------
		if(is_int($sender))  {
			//idst
			//...
		} elseif (is_string($sender)) {
			//should check if $from is a valid email address with a regular expression
	  		$_sender=$sender;
	  	} else {
			//handle invalid recipient case
			//...
		}
	
		$this->From = $_sender;
		if($conf_arr[MAIL_SENDER_ACLNAME]) {
	  		$temp = $this->acl_man->getUserByEmail($sender);
	  		$this->FromName = $temp[ACL_INFO_FIRSTNAME].' '.$temp[ACL_INFO_LASTNAME];
	  	}
		//----------------------------------------------------------------------------
	
		//configure attachments
		//----------------------------------------------------------------------------	
		if (is_string($attachments)) {
			//single attachment
			$this->addAttachment($attachments);
		} elseif (is_array($attachments)) {
  		foreach($attachments as $key=>$value) {
  			//maybe check if file exists, if necessary ...
				$this->addAttachment($value);
			}
		}

		//----------------------------------------------------------------------------
		
		//configure replyto(s)
		//----------------------------------------------------------------------------
		if  (isset($conf_arr[MAIL_REPLYTO] )) {
			//retrieve replyto(s) from params
			if (is_string($conf_arr[MAIL_REPLYTO])) {
				$_replyto[] = $conf_arr[MAIL_REPLYTO];
			} elseif (is_array($conf_arr[MAIL_REPLYTO])) {
				foreach ($conf_arr[MAIL_REPLYTO] as $key=>$value) {
					$_replyto[] = $value;
				}
			}
		}
		foreach ($_replyto as $key=>$value) {
			
			if ($conf_arr[MAIL_REPLYTO_ACLNAME]) {
				$temp = $this->acl_man->getUserByEmail($value);
				$this->AddReplyTo($value, $temp[ACL_INFO_FIRSTNAME].' '.$temp[ACL_INFO_LASTNAME]);
			} else {
				$this->AddReplyTo($value);
			}
		}
		//----------------------------------------------------------------------------

		if  (isset($conf_arr[MAIL_CHARSET])) {
			$this->CharSet = $conf_arr[MAIL_CHARSET];
		}  
		
		if  (isset($conf_arr[MAIL_WORDWRAP])) {
			$this->WordWrap = $conf_arr[MAIL_WORDWRAP];
		}
		
		if  (isset($conf_arr[MAIL_HTML])) {
			$this->IsHTML($conf_arr[MAIL_HTML]);
		}
		
		if  (isset($conf_arr[MAIL_SINGLETO])) {
			$this->SingleTo = $conf_arr[MAIL_SINGLETO];
		}
		
		$this->Subject = $subject;
		$this->Body    = $body;
		$this->AltBody = $this->ConvertToPlain_UTF8($body);

		//configure recipient(s) and send mail(s)
		//----------------------------------------------------------------------------
		if (is_string($recipients)) {
			
			$_recipients[] = $recipients;
		} elseif (is_array($recipients)) {
			
			//multiple sending ...
			foreach ($recipients as $key=>$value) {
				$_recipients[] = $value;			
			}		
		} else {
			$this->ResetToDefault();
			return false;
		}
		
		foreach ($_recipients as $key=>$value) {
			
			if ($conf_arr[MAIL_RECIPIENT_ACLNAME]) {
				
				$temp = $this->acl_man->getUserByEmail($value);
				$name = $temp[ACL_INFO_FIRSTNAME].' '.$temp[ACL_INFO_LASTNAME];
			} else {
				
				$name = '';
			}
			
			switch($conf_arr[MAIL_MULTIMODE]) {
				case MAIL_CC     : //$this->AddCC($value,$name); break; //not supported yet
				case MAIL_BCC    : //$this->AddBCC($value,$name); break; //not supported yet
				case MAIL_SINGLE : $this->AddAddress($value,$name); break;
				default: $this->AddAddress($value,$name); break;
			}
		}
		//----------------------------------------------------------------------------
		
		$output = $this->Send(); 
		
		//reset the class
		$this->ResetToDefault();
		return $output;
	}


}


?>