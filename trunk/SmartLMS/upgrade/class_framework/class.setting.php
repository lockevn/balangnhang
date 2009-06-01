<?php

class Upgrade_Setting extends Upgrade {
	
	var $platfom = 'framework';
	
	var $mname = 'setting';
	
	/**
	 * upgrade the module version
	 * @param string 	$start_version 	the start version, automaticaly prooceed to the next
	 *
	 * @return mixed 	true if the version jump was successful, else an array with an error code and an error message
	 * 					array( 'error_code', 'error_msg' )
	 **/
	function oneStepUpgrade($start_version) {
		
		switch($start_version) {
			case "2.0.4" : {
				
				$query = "CREATE TABLE `core_setting` (
				  `param_name` varchar(255) NOT NULL default '',
				  `param_value` text NOT NULL,
				  `value_type` varchar(255) NOT NULL default 'string',
				  `max_size` int(3) NOT NULL default '255',
				  `pack` varchar(255) NOT NULL default 'main',
				  `regroup` int(5) NOT NULL default '0',
				  `sequence` int(5) NOT NULL default '0',
				  `param_load` tinyint(1) NOT NULL default '1',
				  `hide_in_modify` tinyint(1) NOT NULL default '0',
				  `extra_info` text NOT NULL,
				  PRIMARY KEY  (`param_name`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "
				INSERT INTO `core_setting` VALUES ('title_organigram_chart', '', 'string', 255, 'main', 0, 5, 1, 1, '');
				INSERT INTO `core_setting` VALUES ('core_version', '3.0', 'string', 255, 'main', 0, 3, 1, 1, '');
				INSERT INTO `core_setting` VALUES ('do_debug', 'on', 'enum', 3, 'main', 0, 17, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('url', 'http://localhost/docebo.org/doceboCore/', 'string', 255, 'main', 0, 1, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('ttlSession', '2000', 'int', 5, 'main', 0, 4, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('defaultTemplate', 'standard', 'template', 255, 'main', 0, 3, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('default_language', 'italian', 'language', 255, 'main', 0, 2, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('use_accesskey', 'on', 'enum', 3, 'main', 0, 15, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('visuItem', '20', 'int', 3, 'main', 0, 11, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('visuUser', '20', 'int', 5, 'main', 0, 12, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('hteditor', 'fckeditor', 'hteditor', 255, 'main', 0, 7, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('layout', 'over', 'layout_chooser', 255, 'main', 0, 6, 1, 1, '');
				INSERT INTO `core_setting` VALUES ('pathfield', 'field/', 'string', 255, 'main', 1, 2, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('pathphoto', 'photo/', 'string', 255, 'main', 1, 2, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('register_type', 'moderate', 'register_type', 10, 'log_option', 0, 1, 0, 0, '');
				INSERT INTO `core_setting` VALUES ('max_log_attempt', '0', 'int', 2, 'log_option', 0, 2, 0, 0, '');
				INSERT INTO `core_setting` VALUES ('save_log_attempt', 'after_max', 'save_log_attempt', 255, 'log_option', 0, 3, 0, 0, '');
				INSERT INTO `core_setting` VALUES ('pass_min_char', '5', 'int', 2, 'log_option', 0, 4, 0, 0, '');
				INSERT INTO `core_setting` VALUES ('pass_alfanumeric', 'off', 'enum', 3, 'log_option', 0, 5, 0, 0, '');
				INSERT INTO `core_setting` VALUES ('hour_request_limit', '24', 'int', 2, 'log_option', 0, 7, 0, 0, '');
				INSERT INTO `core_setting` VALUES ('pass_max_time_valid', '90', 'int', 4, 'log_option', 0, 6, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('privacy_policy', 'on', 'enum', 3, 'log_option', 0, 8, 0, 0, '');
				INSERT INTO `core_setting` VALUES ('accessibility', 'on', 'enum', 255, 'main', 0, 16, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('lang_edit', 'off', 'enum', 3, 'main', 0, 14, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('mail_sender', 'framework@localhost.com', 'string', 255, 'log_option', 0, 0, 0, 0, '');
				INSERT INTO `core_setting` VALUES ('use_org_chart', '1', 'check', 1, 'main', 6, 1, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('use_groups', '1', 'menuvoice', 1, 'main', 6, 2, 1, 0, '/directory/view_group');
				INSERT INTO `core_setting` VALUES ('use_user_fields', '1', 'check', 1, 'main', 6, 3, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('use_admin', '1', 'menuvoice', 1, 'main', 6, 5, 1, 0, '/admin_manager/view');
				INSERT INTO `core_setting` VALUES ('use_org_chart_field', '1', 'check', 1, 'main', 6, 4, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('use_org_chart_multiple_choice', '1', 'check', 1, 'main', 6, 6, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('use_advanced_form', 'off', 'enum', 3, 'log_option', 0, 10, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('fck_image_godadmin', '1', 'check', 255, 'main', 0, 8, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('fck_image_admin', '1', 'check', 255, 'main', 0, 9, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('fck_image_user', '1', 'check', 255, 'main', 0, 10, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('default_pubflow_method', 'advanced', 'pubflow_method_chooser', 8, 'main', 0, 13, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('ldap_used', 'off', 'enum', 3, 'main', 5, 1, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('ldap_server', '192.168.0.1', 'string', 255, 'main', 5, 2, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('ldap_port', '389', 'string', 5, 'main', 5, 3, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('ldap_user_string', '".'$'."user@domain2.domain1', 'string', 255, 'main', 5, 4, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('sms_international_prefix', '+39', 'string', 3, 'main', 3, 1, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('sms_gateway', 'smsmarket', 'string', 50, 'main', 3, 3, 1, 1, '');
				INSERT INTO `core_setting` VALUES ('sms_gateway_user', '', 'string', 50, 'main', 3, 4, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('sms_gateway_pass', '', 'string', 255, 'main', 3, 5, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('sms_cell_num_field', '', 'field_select', 5, 'main', 3, 6, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('sms_gateway_id', '3', 'sel_sms_gateway', 1, 'main', 3, 7, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('sms_gateway_host', '193.254.241.47', 'string', 15, 'main', 3, 8, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('sms_gateway_port', '26', 'int', 5, 'main', 3, 9, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('sms_credit', '', 'string', 20, 'main', 3, 10, 1, 1, '');
				INSERT INTO `core_setting` VALUES ('sms_sent_from', '123456', 'string', 25, 'main', 3, 2, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('nl_sendpercycle', '1', 'int', 4, 'main', 2, 0, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('nl_sendpause', '20', 'int', 3, 'main', 2, 1, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('session_ip_control', 'on', 'enum', 3, 'main', 4, 1, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('session_ip_filter', '', 'textarea', 65535, 'main', 4, 2, 1, 0, '');
				INSERT INTO `core_setting` VALUES ('phantom', '', 'security_check', 255, 'main', 4, 3, 0, 0, '');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "CREATE TABLE `core_setting_list` (
				  `path_name` varchar(255) NOT NULL default '',
				  `label` varchar(255) NOT NULL default '',
				  `default_value` text NOT NULL,
				  `type` varchar(255) NOT NULL default '',
				  `visible` tinyint(1) NOT NULL default '0',
				  `load_at_startup` tinyint(1) NOT NULL default '0',
				  `sequence` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`path_name`)
				)";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				
				$query = "
				INSERT INTO `core_setting_list` VALUES ('ui.template', '_TEMPLATE', '', 'template', 1, 1, 0);
				INSERT INTO `core_setting_list` VALUES ('ui.language', '_LANGUAGE', '', 'language', 1, 1, 0);
				INSERT INTO `core_setting_list` VALUES ('admin_rules.max_user_insert', '_MAX_USER_INSERT', '0', 'integer', 1, 1, 2);
				INSERT INTO `core_setting_list` VALUES ('admin_rules.direct_user_insert', '_DIRECT_USER_INSERT', 'off', 'enum', 1, 1, 3);
				INSERT INTO `core_setting_list` VALUES ('admin_rules.max_course_subscribe', '_MAX_COURSE_SUBSCRIBE', '0', 'integer', 1, 1, 5);
				INSERT INTO `core_setting_list` VALUES ('admin_rules.direct_course_subscribe', '_DIRECT_COURSE_SUBSCRIBE', 'off', 'enum', 1, 1, 6);
				INSERT INTO `core_setting_list` VALUES ('ui.directory.custom_columns', '_CUSTOM_COLUMS', '', 'hidden', 0, 1, 0);
				INSERT INTO `core_setting_list` VALUES ('admin_rules.limit_user_insert', '_LIMIT_USER_INSERT', 'off', 'enum', 1, 1, 1);
				INSERT INTO `core_setting_list` VALUES ('admin_rules.limit_course_subscribe', '_LIMIT_COURSE_SUBSCRIBE', 'off', 'enum', 1, 1, 4);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);

				$this->end_version = '3.0';
				return true;
			};break;
			case "3.0.2" : {
				
				$query = "
				INSERT INTO `core_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES ('welcome_use_feed', 'on', 'enum', 3, 'main', 0, 16, 1, 1, '');
				INSERT INTO `core_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES ('common_admin_session', 'on', 'enum', 3, 'main', 4, 1, 1, 0, '');
				INSERT INTO `core_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES ('sender_event', 'webmaster@docebo.org', 'string', 255, 'main', 0, 4, 1, 0, '');
				INSERT INTO `core_setting` (`param_name`, `param_value`, `value_type`, `max_size`, `pack`, `regroup`, `sequence`, `param_load`, `hide_in_modify`, `extra_info`) VALUES ('templ_use_field', '0', 'id_field', 11, 'main', 0, 0, 1, 1, '');";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$query = "CREATE TABLE `core_field_template` (
				  `id_common` int(11) NOT NULL default '0',
				  `ref_id` int(11) NOT NULL default '0',
				  `template_code` varchar(255) NOT NULL default '',
				  PRIMARY KEY  (`id_common`,`ref_id`)
				);";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				
				$query = "UPDATE core_setting SET param_value = '3.0.3' WHERE param_name = 'core_version'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$this->end_version = '3.0.3';
				return true;
			};break;
			case "3.0.3" : {
				
				$query = "UPDATE core_setting SET param_value = '3.0.4' WHERE param_name = 'core_version'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0.4';
				return true;
			};break;
			case "3.0.4" : {
				
				$query = "UPDATE core_setting SET param_value = '3.0.5' WHERE param_name = 'core_version'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				
				$this->end_version = '3.0.5';
				return true;
			};break;
			case "3.0.5" : {
				
				$query = "INSERT INTO `core_setting` VALUES ('ldap_alternate_check', 'off', 'enum', 3, 'main', 5, 5, 1, 0, '')";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 1);
				

				$query = "INSERT INTO `core_setting` VALUES ('hteditor_height', '220', 'string', 4, 'main', 0, 8, 1, 0, '')";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 2);
				

				$query = "INSERT INTO `core_setting` VALUES ('hteditor_width',  '98%', 'string', 4, 'main', 0, 9, 1, 0, '')";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 3);
				
				$query = "UPDATE core_setting SET param_value = '3.0.6' WHERE param_name = 'core_version'";
				if(!$this->db_man->query($query)) return $this->_getErrArray($start_version, 4);
				
				$this->end_version = '3.0.6';
				return true;
			};break;
			case "3.0.6" : {
				$i = 0;
				
				$content = "UPDATE core_setting SET param_value = '3.5.0' WHERE param_name = 'core_version'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);

				$this->end_version = '3.5.0';
				return true;
			};break;
			case "3.5.0" : {
				$i = 0;
     
				$content = "INSERT INTO `core_setting` VALUES ('profile_only_pwd', 'off', 'enum', 3, 'log_option', 0, 15, 1, 0, '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "INSERT INTO `core_setting` VALUES ('register_with_code', 'off', 'enum', 3, 'log_option', 0, 16, 1, 0, '')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
		
				$content = "UPDATE core_setting SET param_value = '3.5.0.1' WHERE param_name = 'core_version'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				
				$this->end_version = '3.5.0.1';
				return true;
			};break;
			case "3.5.0.1" : {
				$i = 0;
				
				$query = "SELECT id_translation, translation_text FROM core_lang_translation WHERE translation_text LIKE '%@technogym.com%'";
				$re_text = mysql_query($query);
				while(list($id_t, $text) = mysql_fetch_row($re_text)) {
					
					$new_text = substr($text, 0, strpos($text, '</a>'));
					$query = "
					UPDATE core_lang_translation 
					SET translation_text = '".$new_text."</a>'
					WHERE id_translation = '".$id_t."'";
					mysql_query($query);
				}
				
				$content = "UPDATE core_platform
				SET is_active = 'false'
				WHERE platform = 'crm'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "UPDATE core_setting SET param_value = '3.5.0.2' WHERE param_name = 'core_version'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$this->end_version = '3.5.0.2';
				return true;
			};break;
			case "3.5.0.2" : {
				$i = 0;
				
				
				$content = "INSERT INTO `conference_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
				VALUES (
					'dimdim_user', '', 'string', '255', '6', '2', '1', '0', ''
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "INSERT INTO `conference_setting` ( `param_name` , `param_value` , `value_type` , `max_size` , `regroup` , `sequence` , `param_load` , `hide_in_modify` , `extra_info` )
				VALUES (
					'dimdim_password', '', 'string', '255', '6', '2', '1', '0', ''
				)";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "UPDATE core_setting SET param_value = '3.5.0.3' WHERE param_name = 'core_version'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$this->end_version = '3.5.0.3';
				return true;
			};break;
			case "3.5.0.4": {
				$i = 0;
				
				
				$content = "DELETE FROM core_hteditor WHERE hteditor = 'fckeditor'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "DELETE FROM core_hteditor WHERE hteditor = 'widgeditor'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "DELETE FROM core_hteditor WHERE hteditor = 'xinha'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "DELETE FROM core_hteditor WHERE hteditor = 'xstandard'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				// -----------------------------------------------------------------------------------
				
				$content = "INSERT INTO `core_hteditor` (`hteditor`,`hteditorname`) VALUES ('tinymce', '_TINYMCE')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "INSERT INTO `core_hteditor` (`hteditor`,`hteditorname`) VALUES ('yui', '_YUI')";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, $i);
				$i++;
				
				$content = "UPDATE core_setting SET param_value = 'tinymce' WHERE param_name = 'hteditor'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				// -----------------------------------------------------------------------------------
				
				$content = "INSERT INTO `core_setting` (`param_name` ,`param_value` ,`value_type` ,`max_size` ,`pack` ,`regroup` ,`sequence` ,`param_load` ,`hide_in_modify` ,`extra_info`)
							VALUES ('use_tag', 'on', 'enum', '3', 'main', '0', '18', '1', '0', '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$content = "INSERT INTO `core_setting` (`param_name` ,`param_value` ,`value_type` ,`max_size` ,`pack` ,`regroup` ,`sequence` ,`param_load` ,`hide_in_modify` ,`extra_info`)
							VALUES ('use_rest_api', 'off', 'enum', '3', 'main', '10', '0', '1', '0', '');";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				// -----------------------------------------------------------------------------------
				$content = "UPDATE core_setting SET param_value = '3.6.0' WHERE param_name = 'core_version'";
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0';
				return true;
			};break;
			
			case "3.6.0":
				$i = 0;
				
				$content =	"SELECT COUNT(*)"
							." FROM core_setting"
							." WHERE param_name = 'use_tag'";
				
				list($control) = mysql_fetch_row($this->db_man->query($content));
				
				if(!$control)
				{
					$content = "INSERT INTO `core_setting` (`param_name` ,`param_value` ,`value_type` ,`max_size` ,`pack` ,`regroup` ,`sequence` ,`param_load` ,`hide_in_modify` ,`extra_info`)
							VALUES ('use_tag', 'on', 'enum', '3', 'main', '0', '18', '1', '0', '');";
					if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				}
				
				$content = "UPDATE core_setting SET param_value = '3.6.0.1' WHERE param_name = 'core_version'";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0.1';
				return true;
			break;
			
			case "3.6.0.1":
				$i = 0;
				
				$content = "UPDATE core_setting SET param_value = '3.6.0.2' WHERE param_name = 'core_version'";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0.2';
				return true;
			break;
			
			case "3.6.0.2":
				$i = 0;
				
				$content = "UPDATE core_setting SET param_value = '3.6.0.3' WHERE param_name = 'core_version'";
				
				if(!$this->db_man->query($content)) return $this->_getErrArray($start_version, ++$i);
				
				$this->end_version = '3.6.0.3';
				return true;
			break;
		}
		return true;
	}
}

?>