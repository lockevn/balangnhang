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
 * @package 	DoceboCore
 * @subpackage 	UserInterface
 * @version 	$Id: lib.form.php,v 1.32 2005/11/04 13:40:09 gishell Exp $
 * @author		Fabio Pirovano <fabio (@) docebo (.) com>
 */


class Form {

	/**
	 * function getFormHeader( $text )
	 *
	 * @param string $text 	the text that will be displayed as header of the form
	 * @return string with the form header html code
	 */
	function getFormHeader( $text ) {
		return '<div class="form_header">'.$text.'</div>'."\n";
	}

	/**
	 * function openForm( $id , $action, $css_class, $method, $enctype, $other )
	 *
	 * @param string $id 		the form id
	 * @param string $action 	the action of the form
	 * @param string $css_form 	optional css class for this form, if false default, if blacnk not added class=""
	 * @param string $method 	optional method for this form
	 * @param string $enctype 	optional enctype for this form
	 * @param string $other 	optional code for the form tag
	 * @return string 	with the form opening html code
	 */
	 function openForm( $id , $action, $css_form = false, $method = false, $enctype = '', $other = '' ) {

		if ($css_form  === false) $css_form = 'std_form';
		if ($method == false) $method = 'post';
		return '<form '
			.( $css_form != '' ? ' class="'.$css_form.'"' : '' )
			.' id="'.$id.'" method="'.$method.'" action="'.$action.'"'
			.( $enctype != '' ? ' enctype="'.$enctype.'"' : '' )
			.$other.'>'."\n"
			.'<div>'."\n";
    }

	/**
	 * function openElementSpace( $css_class )
	 *
	 * @param string $css_class optional css class for the element container
	 * @return string with the html for open the element container
	 */
	function openElementSpace( $css_class = 'form_elem' ) {
		return '<div class="'.$css_class.'">'."\n";
	}

	/**
	 * function getTextBox( $text , $css_line = '')
	 *
	 * @param string 	$text 			the text to display
	 * @param string 	$css_line 		the css of the container element
	 * @param boolean 	$inline 		if true use <span> , else <div>
	 * @return string 	with the html code for the text output
	 */
	function getTextBox( $text , $css_line = 'form_line_text', $inline = false ) {

		return '<'.( $inline ? 'span' : 'div' ).' class="'.$css_line.'">'
				.$text.'</'.( $inline ? 'span' : 'div' ).'>'."\n";
	}

	/**
	 * @param string 	$span_text 		the text to display on the left
	 * @param string 	$text 			the text to display on the right
	 * @param string 	$css_line 		the css of the container element
	 * @param string 	$css_f_effect 	the css of the left element
	 * @return string 	with the html code for the text output
	 */
	function getLineBox( $span_text, $text , $css_line = 'form_line_l', $css_f_effect = 'label_effect' ) {

		return '<div class="'.$css_line.'">'
				.'<div class="'.$css_f_effect.'">'.$span_text.'</div>'
				.$text
				.'</div>'."\n";
	}

	/**
	 * function getHidden( $id, $name, $value, $other_param )
	 *
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $value 		the default value for the hidden field
	 * @param string $other_param 	other element for the tag
	 * @return string 	with the html code for the input type="hidden" element
	 */
	function getHidden( $id, $name, $value, $other_param = '' ) {
		return '<input type="hidden" id="'.$id.'" name="'.$name.'" value="'.$value.'"'.( $other_param != '' ? ' '.$other_param : '' ).' />'."\n";
	}

	/**
	 * function getTextfield( $css_text, $id, $name, $value, $alt_name, $maxlenght, $other_param )
	 *
	 * @param string $css_text 		the css class for the input element
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $value 		the default value for the input field
	 * @param string $alt_name 		the alt name for the field
	 * @param string $maxlenght 	the max number of characters
	 * @param string $other_param 	other element for the tag
	 * @return string 	with the html code for the input type="text" element
	 */
	function getInputTextfield( $css_text, $id, $name, $value, $alt_name, $maxlenght, $other_param ) {
		return '<input type="text" '
			."\n\t".'class="'.$css_text.'" '
			."\n\t".'id="'.$id.'" '
			."\n\t".'name="'.$name.'" '
			."\n\t".'value="'.$value.'" '
			."\n\t".'maxlength="'.$maxlenght.'" '
			."\n\t".'alt="'.$alt_name.'"'.( $other_param != '' ? ' '.$other_param : '' ).' />'."\n";
	}

	/**
	 * function getLineTextfield( $css_line, $css_label, $label_name, $css_text, $id, $name, $value, $alt_name, $maxlenght, $other_param, $other_after, $other_before )
	 *
	 * @param string $css_line 		css for the line
	 * @param string $css_label 	css for the label
	 * @param string $label_name 	text contained into the label
	 * @param string $css_text 		the css class for the input element
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $value 		the default value for the input field
	 * @param string $alt_name 		the alt name for the field
	 * @param string $maxlenght 	the max number of characters
	 * @param string $other_param 	other element for the tag
	 * @param string $other_after 	html code added after the input element
	 * @param string $other_before 	html code added before the label element
	 * @return string with the html code for a line with the input type="text" element
	 */
	function getLineTextfield( $css_line, $css_label, $label_name, $css_text, $id, $name, $value, $alt_name, $maxlenght, $other_param, $other_after, $other_before ) {

		return '<div class="'.$css_line.'">'."\n"
			.$other_before."\n"
			.'<p><label class="'.$css_label.'" for="'.$id.'">'.$label_name.'</label></p>'
			.Form::getInputTextfield( $css_text, $id, $name, $value, $alt_name, $maxlenght, $other_param )
			.$other_after."\n"
			.'</div>'."\n";
	}

	/**
	 * function getTextfield( $label_name, $id, $name, $maxlenght, $value, $other_after, $other_before )
	 *
	 * @param string $label_name 	text contained into the label
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $maxlenght 	the max number of characters
	 * @param string $value 		optional default value for the input field
	 * @param string $alt_name 		the alt name for the field
	 * @param string $other_after 	optional html code added after the input element
	 * @param string $other_before 	html code added before the label element
	 * @return string with the html code for the input type="text" element
	 */
	function getTextfield( $label_name, $id, $name, $maxlenght, $value = '', $alt_name = '', $other_after = '', $other_before = '' ) {

		if( $alt_name == '' ) $alt_name = strip_tags($label_name);
		return Form::getLineTextfield( 'form_line_l', 'floating', $label_name, 'textfield', $id, $name, $value, $alt_name, $maxlenght, '', $other_after, $other_before );
	}

	function getInputDatefield( $css_field, $id, $name, $value = '', $date_format = FALSE, $sel_time = FALSE, $alt_name = '', $other_after = '', $other_before = '' ) {

		if($date_format == false) {
            if ($sel_time === false) {
                $date_format = $GLOBALS["regset"]->date_token;
			} else {
                $date_format = $GLOBALS["regset"]->full_token;
			}
        }
		if(getAccessibilityStatus() == true) {

			return  $other_before.Form::getInputTextfield( $css_field, $id, $name, $value, $alt_name, '30', '').def('_DATE_FORMAT', 'standard', 'framework').' ( '.$date_format.' )';
		}

		if($css_field == false) $css_field = 'textfield';
		if(!isset($GLOBALS['jscal_loaded']) || $GLOBALS['jscal_loaded'] == false) {

			$lang_code = $GLOBALS['globLangManager']->getLanguageBrowsercode(getLanguage());
			$cut_at = strpos($lang_code, ';');
			if($cut_at == 0) {
				$lang_code = substr($lang_code, 0);
			} else {
				$lang_code = substr($lang_code, 0, $cut_at);
			}

			$sep = ( substr($GLOBALS['where_framework_relative'], -1) != '/' ? '/' : '' );
			if(file_exists($GLOBALS['where_framework_relative'].$sep.'/addons/calendar/lang/calendar-'.$lang_code.'.js')) {

				$lang_script = $GLOBALS['where_framework_relative'].$sep.'addons/calendar/lang/calendar-'.$lang_code.'.js';
			} else {

				$lang_script = $GLOBALS['where_framework_relative'].$sep.'addons/calendar/lang/calendar-en.js';
			}
			if(isset($GLOBALS['page'])) {
				$GLOBALS['page']->add("\n"
            	.'<link href="'.getPathTemplate('framework').'style/calendar/calendar-blue.css" rel="stylesheet" type="text/css" />'."\n"
				.'<script type="text/javascript" src="'
					.$GLOBALS['where_framework_relative'].$sep.'addons/calendar/calendar.js"></script>'."\n"
				.'<script type="text/javascript" src="'.$lang_script.'"></script>'."\n"
				.'<script type="text/javascript" src="'
					.$GLOBALS['where_framework_relative'].$sep.'addons/calendar/calendar-setup.js"></script>'."\n", 'page_head');
			}
			$GLOBALS['jscal_loaded'] = true;
        }
        $other_after_b = '<button class="trigger_calendar" id="trigger_'.$id.'"></button>'
            ."\n"
            .'<script type="text/javascript">'."\n"
            .'      Calendar.setup('."\n"
            .'        {'."\n"
            .'          inputField  : "'.$id.'",                // ID of the input field'."\n"
            .'          ifFormat    : "'.$date_format.'",        // the date format'."\n"
            .'          button      : "trigger_'.$id.'",        // ID of the button'."\n";

        if ($sel_time) {
            if (substr($GLOBALS["regset"]->time_token, 0, 2) == "%I")
                $other_after_b.= '      timeFormat   : 12, ';
            else
                $other_after_b.= '      timeFormat   : 24, ';
            $other_after_b.= '      showsTime   : true';
        }
        else
            $other_after_b.= '      showsTime   : false';

        $other_after_b.= '        }'."\n"
            .'      );'."\n"
            .'</script>';

        return  $other_before.Form::getInputTextfield( $css_field, $id, $name, $value, $alt_name, '30', '').$other_after_b.$other_after;

	}

	/**
	 * function getDatefield( $label_name, $id, $name, $value = '', $date_format = FALSE, $sel_time = FALSE, $alt_name = '', $other_after = '', $other_before = '' )
	 *
	 * @param string $label_name 	text contained into the label
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $maxlenght 	the max number of characters
	 * @param string $value 		optional default value for the input field
	 * @param string $alt_name 		the alt name for the field
	 * @param string $date_format 	optional string with the date format selected
     * @param bool	 $sel_time 		optional if true will show also the time selector
	 * @param string $alt_name 		optional with the alt value
	 * @param string $other_after 	optional html code added after the input element
	 * @param string $other_before 	optional html code added before the label element
	 *
	 * @return string with the html code for the input type="text" with a calendar
	 */
	function getDatefield( $label_name, $id, $name, $value = '', $date_format = FALSE, $sel_time = FALSE, $alt_name = '', $other_after = '', $other_before = '' ) {

		if($date_format == false) {
			if ($sel_time === false) {
				$date_format = $GLOBALS["regset"]->date_token;
			} else {
				$date_format = $GLOBALS["regset"]->full_token;
			}
		}
       if($alt_name == '') $alt_name = strip_tags($label_name);
	   if(getAccessibilityStatus() == true) {

			return  Form::getLineTextfield( 'form_line_l', 'floating', $label_name, 'textfield',
										$id, $name, $value, $alt_name, '30', '',
										def('_DATE_FORMAT', 'standard', 'framework').' ( '.$date_format.' )', '' );
		}
		if(!isset($GLOBALS['jscal_loaded']) || $GLOBALS['jscal_loaded'] == false) {

			$lang_code = $GLOBALS['globLangManager']->getLanguageBrowsercode(getLanguage());
			$cut_at = strpos($lang_code, ';');
			if($cut_at == 0) {
				$lang_code = substr($lang_code, 0);
			} else {
				$lang_code = substr($lang_code, 0, $cut_at);
			}

			$sep = ( substr($GLOBALS['where_framework_relative'], -1) != '/' ? '/' : '' );
			if(file_exists($GLOBALS['where_framework_relative'].$sep.'/addons/calendar/lang/calendar-'.$lang_code.'.js')) {

				$lang_script = $GLOBALS['where_framework_relative'].$sep.'addons/calendar/lang/calendar-'.$lang_code.'.js';
			} else {

				$lang_script = $GLOBALS['where_framework_relative'].$sep.'addons/calendar/lang/calendar-en.js';
			}
			if(isset($GLOBALS['page'])) {
				$GLOBALS['page']->add("\n"
            	.'<link href="'.getPathTemplate('framework').'style/calendar/calendar-blue.css" rel="stylesheet" type="text/css" />'."\n"
				.'<script type="text/javascript" src="'
					.$GLOBALS['where_framework_relative'].$sep.'addons/calendar/calendar.js"></script>'."\n"
				.'<script type="text/javascript" src="'.$lang_script.'"></script>'."\n"
				.'<script type="text/javascript" src="'
					.$GLOBALS['where_framework_relative'].$sep.'addons/calendar/calendar-setup.js"></script>'."\n", 'page_head');
			}
			$GLOBALS['jscal_loaded'] = true;
        }
        $other_after_b = '<button class="trigger_calendar" id="trigger_'.$id.'"></button>'
            ."\n"
            .'<script type="text/javascript">'."\n"
            .'      Calendar.setup('."\n"
            .'        {'."\n"
            .'          inputField  : "'.$id.'",                // ID of the input field'."\n"
            .'          ifFormat    : "'.$date_format.'",        // the date format'."\n"
            .'          button      : "trigger_'.$id.'",        // ID of the button'."\n";

        if ($sel_time) {
            if (substr($GLOBALS["regset"]->time_token, 0, 2) == "%I")
                $other_after_b.= '      timeFormat   : 12, ';
            else
                $other_after_b.= '      timeFormat   : 24, ';
            $other_after_b.= '      showsTime   : true';
        }
        else
            $other_after_b.= '      showsTime   : false';

        $other_after_b.= '        }'."\n"
            .'      );'."\n"
            .'</script>';


        return  Form::getLineTextfield( 'form_line_l', 'floating', $label_name, 'textfield',
										$id, $name, $value, $alt_name, '30', '', $other_after_b.$other_after, $other_before );

	}

	/**
	 * @param string $css_text 		the css class for the input element
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $alt_name 		the alt name for the field
	 * @param string $maxlenght 	the max number of characters
	 * @param string $other_param 	other element for the tag
	 * @return string 	with the html code for the input type="password" element
	 */
	function getInputPassword( $css_text, $id, $name, $alt_name, $maxlenght, $other_param ) {
		return '<input type="password" '
			."\n\t".'class="'.$css_text.'" '
			."\n\t".'id="'.$id.'" '
			."\n\t".'name="'.$name.'" '
			."\n\t".'maxlength="'.$maxlenght.'" '
			."\n\t".'alt="'.$alt_name.'"'.( $other_param != '' ? ' '.$other_param : '' ).' />'."\n";
	}

	/**
	 * @param string $css_line 		css for the line
	 * @param string $css_label 	css for the label
	 * @param string $label_name 	text contained into the label
	 * @param string $css_text 		the css class for the input element
	 * @param string $id 			the id of the element
	 * @param string $value 		the default value for the input field
	 * @param string $alt_name 		the alt name for the field
	 * @param string $maxlenght 	the max number of characters
	 * @param string $other_param 	other element for the tag
	 * @param string $other_after 	html code added after the input element
	 * @param string $other_before 	html code added before the label element
	 * @return string with the html code for a line with the input type="text" element
	 */
	function getLinePassword( $css_line, $css_label, $label_name, $css_text, $id, $name, $alt_name, $maxlenght, $other_param, $other_after, $other_before ) {
		return '<div class="'.$css_line.'">'."\n"
			.$other_before."\n"
			.'<p><label class="'.$css_label.'" for="'.$id.'">'.$label_name.'</label></p>'
			.Form::getInputPassword( $css_text, $id, $name, $alt_name, $maxlenght, $other_param )
			.$other_after."\n"
			.'</div>'."\n";
	}

	/**
	 * @param string $label_name 	text contained into the label
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $maxlenght 	the max number of characters
	 * @param string $alt_name 		the alt name for the field
	 * @param string $other_after 	optional html code added after the input element
	 * @param string $other_before 	optional html code added before the label element
	 * @return string with the html code for the input type="text" element
	 */
	function getPassword( $label_name, $id, $name, $maxlenght, $alt_name = '', $other_after = '', $other_before = '' ) {

		if( $alt_name == '' ) $alt_name = strip_tags($label_name);
		return Form::getLinePassword( 'form_line_l', 'floating', $label_name, 'textfield', $id, $name, $alt_name, $maxlenght, '', $other_after, $other_before );
	}

	/**
	 * @param string $css_text 		the css class for the input element
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $value 		the default value for the input field
	 * @param string $alt_name 		the alt name for the field
	 * @param string $other_param 	other element for the tag
	 * @return string 	with the html code for the input type="text" element
	 */
	function getInputFilefield( $css_text, $id, $name, $value, $alt_name,  $other_param ) {
		return '<input type="file" '
			."\n\t".'class="'.$css_text.'" '
			."\n\t".'id="'.$id.'" '
			."\n\t".'name="'.$name.'" '
			."\n\t".'value="'.$value.'" '
			."\n\t".'alt="'.$alt_name.'"'.( $other_param != '' ? ' '.$other_param : '' ).' />'."\n";
	}

	/**
	 * @param string $css_line 		css for the line
	 * @param string $css_label 	css for the label
	 * @param string $label_name 	text contained into the label
	 * @param string $css_text 		the css class for the input element
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $value 		the default value for the input field
	 * @param string $alt_name 		the alt name for the field
	 * @param string $other_param 	other element for the tag
	 * @param string $other_after 	html code added after the input element
	 * @param string $other_before 	html code added before the label element
	 * @return string with the html code for a line with the input type="text" element
	 */
	function getLineFilefield( $css_line, $css_label, $label_name, $css_text, $id, $name, $value, $alt_name, $other_param, $other_after, $other_before ) {
		return '<div class="'.$css_line.'">'."\n"
			.$other_before."\n"
			.'<p><label class="'.$css_label.'" for="'.$id.'">'.$label_name.'</label></p>'
			.Form::getInputFilefield( $css_text, $id, $name, $value, $alt_name, $other_param )
			.$other_after."\n"
			.'</div>'."\n";
	}

	/**
	 * function getFilefield( $label_name, $id, $name, $value = '', $alt_name = '', $other_after = '', $other_before = '' )
	 *
	 * @param string $label_name 	text contained into the label
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $value 		optional default value for the input field
	 * @param string $alt_name 		the alt name for the field
	 * @param string $other_after 	optional html code added after the input element
	 * @param string $other_before 	optional html code added before the label element
	 * @return string with the html code for the input type="text" element
	 */
	function getFilefield( $label_name, $id, $name, $value = '', $alt_name = '', $other_after = '', $other_before = '' ) {

		if( $alt_name == '' ) $alt_name = strip_tags($label_name);

		$p_size = intval(ini_get('post_max_size'));
		$u_size = intval(ini_get('upload_max_filesize'));
		$max_kb = ( $p_size < $u_size ? $p_size : $u_size );
		$other_after = ' (Max. '.$max_kb.' Mb) '.$other_after;
		return Form::getLineFilefield( 'form_line_l', 'floating', $label_name, 'fileupload', $id, $name, $value, $alt_name, '', $other_after, $other_before );
	}

	/**
	 * function getInputDropdown( $css_text, $id, $name, $value, $alt_name, $maxlenght, $other_param )
	 *
	 * @param string $css_dropdown 	the css class for the select element
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $all_value 	the possible value of the textfield
	 * @param string $selected 		the element selected
	 * @param string $other_param 	other element for the tag
	 *
	 * @return string with the html code for the select element
	 */
	function getInputDropdown( $css_dropdown, $id, $name, $all_value, $selected, $other_param ) {

		$html_code = '<select class="'.$css_dropdown.'" '
					."\n\t".'id="'.$id.'" '
					."\n\t".'name="'.$name.'"  '.$other_param.'>'."\n";
		if( is_array($all_value) ) {
			while( list($key, $value) = each($all_value) ) {
				$html_code .= '	<option value="'.$key.'"'
							.((string)$key == (string)$selected ? ' selected="selected"' : '' )
							.'>'.$value.'</option>'."\n";
			}
		}
		$html_code .= '</select>'."\n";
		return $html_code;
	}

	/**
	 * function getLineDropdown( $css_line, $css_label, $label_name, $css_dropdown, $id, $name, $all_value, $selected, $other_param, $other_after, $other_before )
	 *
	 * @param string $css_line 		css for the line
	 * @param string $css_label 	css for the label
	 * @param string $label_name 	text contained into the label
	 * @param string $css_dropdown 	the css class for the select element
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $all_value 	all the possible value of the select element
	 * @param string $selected 		the element selected
	 * @param string $other_param 	other element for the tag
	 * @param string $other_after 	html code added after the select element
	 * @param string $other_before 	html code added before the label element
	 *
	 * @return string with the html code for a line with the select element
	 */
	function getLineDropdown( $css_line, $css_label, $label_name, $css_dropdown, $id, $name, $all_value, $selected, $other_param, $other_after, $other_before ) {
		return '<div class="'.$css_line.'">'."\n"
			.$other_before."\n"
			.'<p><label class="'.$css_label.'" for="'.$id.'">'.$label_name.'</label></p>'
			.Form::getInputDropdown( $css_dropdown, $id, $name, $all_value, $selected, $other_param )
			.$other_after."\n"
			.'</div>'."\n";
	}

	/**
	 * function getDropdown( $label_name, $id, $name, $all_value , $selected = '', $other_after = '', $other_before = '' )
	 *
	 * @param string $label_name 	text contained into the label
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $all_value 	all the possible value of the select element
	 * @param string $selected 		the element selected
	 * @param string $other_after 	html code added after the select element
	 * @param string $other_before 	html code added before the label element
	 *
	 * @return string with the html code for a line with the select element
	 */
	function getDropdown( $label_name, $id, $name, $all_value , $selected = '', $other_after = '', $other_before = '' ) {

		return Form::getLineDropdown( 'form_line_l', 'floating', $label_name, 'dropdown', $id, $name, $all_value, $selected, '', $other_after, $other_before );
	}

	/**
	 * function getInputListbox( $css_listbox, $id, $name, $all_value, $selected, $multiple, $other_param )
	 *
	 * @param string $css_listbox 	the css class for the select element
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $all_value 	the possible value of the textfield
	 * @param array  $selected 		the elements selected
	 * @param bool   $multiple		is a multi select listbox
	 * @param string $other_param 	other element for the tag
	 *
	 * @return string with the html code for the select element
	 */
	function getInputListbox( $css_listbox, $id, $name, $all_value, $selected, $multiple, $other_param ) {

		$html_code = '<select class="'.$css_listbox.'" '
					."\n\t".'id="'.$id.'" '
					."\n\t".'name="'.$name.'" '
					.(($multiple)?'multiple="multiple" ':'')
					.$other_param.'>'."\n";
		if( is_array($all_value) ) {
			while( list($key, $value) = each($all_value) ) {
				$html_code .= '	<option value="'.$key.'"'
							.(in_array ( $key, $selected) ? ' selected="selected"' : '' )
							.'>'.$value.'</option>'."\n";
			}
		}
		$html_code .= '</select>'."\n";
		return $html_code;
	}

	/**
	 * function getLineListbox( $css_line, $css_label, $label_name, $css_listbox, $id, $name, $all_value, $selected, $multiple, $other_param, $other_after, $other_before )
	 *
	 * @param string $css_line 		css for the line
	 * @param string $css_label 	css for the label
	 * @param string $label_name 	text contained into the label
	 * @param string $css_listbox 	the css class for the select element
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $all_value 	all the possible value of the select element
	 * @param array  $selected 		the elements selected
	 * @param bool   $multiple		is a multi select listbox
	 * @param string $other_param 	other element for the tag
	 * @param string $other_after 	html code added after the select element
	 * @param string $other_before 	html code added before the label element
	 *
	 * @return string with the html code for a line with the select element
	 */
	function getLineListbox( $css_line, $css_label, $label_name, $css_listbox, $id, $name, $all_value, $selected, $multiple, $other_param, $other_after, $other_before ) {
		return '<div class="'.$css_line.'">'."\n"
			.$other_before."\n"
			.'<p><label class="'.$css_label.'" for="'.$id.'">'.$label_name.'</label></p>'
			.Form::getInputListbox( $css_listbox, $id, $name, $all_value, $selected, $multiple, $other_param )
			.$other_after."\n"
			.'</div>'."\n";
	}

	/**
	 * function getListbox( $label_name, $id, $name, $all_value , $selected = FALSE, $multiple = TRUE, $other_after = '', $other_before = '' )
	 *
	 * @param string $label_name 	text contained into the label
	 * @param string $id 			the id of the element
	 * @param string $name 			the name of the element
	 * @param string $all_value 	all the possible value of the select element
	 * @param array  $selected 		the elements selected
	 * @param bool   $multiple		is a multi select listbox
	 * @param string $other_after 	optional html code added after the input element
	 * @param string $other_before 	optional html code added before the label element
	 *
	 * @return string with the html code for the input type="text" element
	 */
	function getListbox( $label_name, $id, $name, $all_value, $selected = FALSE, $multiple = TRUE, $other_after = '', $other_before = '' ) {
		return Form::getLineListbox('form_line_l',
									'floating',
									$label_name,
									'listbox',
									$id,
									$name,
									$all_value,
									($selected === FALSE)?array():$selected,
									$multiple,
									'',
									$other_after,
									$other_before );
	}

	/**
	 * @param string 	$id 			the id of the element
	 * @param string 	$name 			the name of the element
	 * @param string 	$value 			default value for the input field
	 * @param boolean 	$is_checked 	true if checkbox is checked
	 * @param string 	$other_param 	added in the html code
	 *
	 * @return string with the html code for the input type="checkbox" element
	 */
	function getInputCheckbox( $id, $name, $value, $is_checked, $other_param ) {

		return '<input class="check" type="checkbox" id="'.$id.'" name="'.$name.'" value="'.$value.'"'
				.( $is_checked ? ' checked="checked"' : '' )
				.( $other_param != '' ? ' '.$other_param : '' ).' />';
	}

	/**
	 * function getLineCheckbox( $css_line, $css_label, $label_name, $id, $name, $value, $is_selected )
	 *
	 * @param string 	$css_line 		the css class for the line
	 * @param string 	$css_label 		the css label for the label
	 * @param string 	$label_name 	text contained into the label
	 * @param string 	$id 			the id of the element
	 * @param string 	$name 			the name of the element
	 * @param string 	$value 			default value for the input field
	 * @param boolean 	$is_checked 	true if checkbox is checked
	 * @param string 	$other_param 	added in the html code
	 *
	 * @return string with the html code for the input type="checkbox" element
	 */
	function getLineCheckbox( $css_line, $css_label, $label_name, $id, $name, $value, $is_checked, $other_param ) {

		return '<div class="'.$css_line.'">'."\n"
			.Form::getInputCheckbox( $id, $name, $value, $is_checked, $other_param )
			.' <label class="'.$css_label.'" for="'.$id.'">'.$label_name.'</label>'."\n"
			.'</div>'."\n";
	}

	/**
	 * function getCheckbox(  $label_name, $id, $name, $is_checked = false, $value = 1 )
	 *
	 * @param string 	$label_name 	text contained into the label
	 * @param string 	$id 			the id of the element
	 * @param string 	$name 			the name of the element
	 * @param string 	$value 			value for the input field
	 * @param boolean 	$is_checked 	optional,if true the checkbox is checked, default is false
	 * @param string 	$other_param 	added in the html code
	 *
	 * @return string with the html code for the input type="checkbox" element
	 */
	function getCheckbox( $label_name, $id, $name, $value, $is_checked = false, $other_param = '' ) {

		return Form::getLineCheckbox( 'form_line_l', 'label_bold', $label_name, $id, $name, $value, $is_checked, $other_param );
	}

	/**
	 * @param string 	$id 			the id of the element
	 * @param string 	$name 			the name of the element
	 * @param string 	$value 			default value for the input field
	 * @param boolean 	$is_checked 	true if radio is checked
	 * @param string 	$other_param 	added in the html code
	 *
	 * @return string with the html code for the input type="radio" element
	 */
	function getInputRadio( $id, $name, $value, $is_checked, $other_param ) {

		return '<input class="radio" type="radio" id="'.$id.'" name="'.$name.'" value="'.$value.'"'
				.( $is_checked ? 'checked="checked"' : '' )
				.( $other_param != '' ? ' '.$other_param : '' ).' />';
	}

	/**
	 * function getLineRadio( $css_line, $css_label, $label_name, $id, $name, $value, $is_selected )
	 *
	 * @param string 	$css_line 		the css class for the line
	 * @param string 	$css_label 		the css label for the label
	 * @param string 	$label_name 	text contained into the label
	 * @param string 	$id 			the id of the element
	 * @param string 	$name 			the name of the element
	 * @param string 	$value 			default value for the input field
	 * @param boolean 	$is_checked 	true if radio is checked
	 * @param string 	$other_param 	added in the html code
	 *
	 * @return string with the html code for the input type="radio" element
	 */
	function getLineRadio( $css_line, $css_label, $label_name, $id, $name, $value, $is_checked, $other_param = '' ) {

		return '<div class="'.$css_line.'">'."\n"
			.Form::getInputRadio( $id, $name, $value, $is_checked, $other_param )
			.' <label class="'.$css_label.'" for="'.$id.'">'.$label_name.'</label>'."\n"
			.'</div>'."\n";
	}

	/**
	 * function getRadio(  $label_name, $id, $name, $is_checked = false, $value = 1 )
	 *
	 * @param string 	$label_name 	text contained into the label
	 * @param string 	$id 			the id of the element
	 * @param string 	$name 			the name of the element
	 * @param string 	$value 			value for the input field
	 * @param boolean 	$is_checked 	optional,if true the radio is checked, default is false
	 *
	 * @return string with the html code for the input type="radio" element
	 */
	function getRadio( $label_name, $id, $name, $value, $is_checked = false ) {

		return Form::getLineRadio( 'form_line_l', 'label_bold', $label_name, $id, $name, $value, $is_checked );
	}

	/**
	 * function getRadioSet(  $label_name, $id, $name, $all_value , $selected = '', $other_after = '', $other_before = '' )
	 *
	 * @param string 	$label_name 	text contained into the label
	 * @param string 	$id 			the id of the element
	 * @param string 	$name 			the name of the element
	 * @param string 	$value 			value for the input field
	 * @param boolean 	$is_checked 	optional,if true the radio is checked, default is false
	 *
	 * @return string with the html code for the input type="radio" element
	 */
	function getRadioSet( $group_name, $id, $name, $all_value , $selected = '', $other_after = '', $other_before = '' ) {
		$count = 0;
		$out = '<div class="form_line_l">'."\n"
				.$other_before."\n"
				.'<div class="label_effect">'.$group_name.'</div>'
				.'<div class="grouping">';
		foreach( $all_value as $label_item => $val_item ) {
			$out .= Form::getInputRadio( 	$id.'_'.$count,
											$name,
											$val_item,
											$val_item == $selected,
											'' );
			$out .= ' <label class="label_padded" for="'.$id.'_'.$count.'">'
					.$label_item.'</label>'."\n";
			$count++;
		}
		$out .= '</div>'.$other_after.'</div>'."\n";

		return $out;
	}


	/**
	 * function getOpenCombo( $group_name, $css_line, $other_before )
	 *
	 * @param string 	$group_name 	text contained into the group intestation
	 * @param string 	$css_line 		optional the css class of the line
	 * @param string 	$other_before 	optional html code added before the label element
	 * @return string with the html code for open a group of combo element (checkbox, radio, ...)
	 */
	function getOpenCombo( $group_name, $css_line = 'form_line_l', $other_before = '' ) {

		return'<div class="'.$css_line.'">'."\n"
			.$other_before."\n"
			.'<div class="label_effect">'.$group_name.'</div>'
			.'<div class="grouping">';
	}

	/**
	 * function getcloseCombo( $other_after )
	 * @param string 	$other_after 	optional html code added after the input element
	 *
	 * @return string with the html code for close a combo group
	 */
	function getCloseCombo( $other_after = '' ) {

		return '</div>'
			.$other_after."\n"
			.'</div>'."\n";
	}

	/**
	 * @param string 	$legend 		text contained into the legend tag
	 * @param string 	$id_field 		id of the fieldset
	 * @param string 	$css_line 		optional the css class of the fieldset
	 *
	 * @return string 	with the html code for open a fieldset
	 */
	function getOpenFieldset( $legend, $id_field = '', $css_line = 'fieldset_std'  ) {

		return'<fieldset'.( $id_field != '' ? ' id="'.$id_field.'"' : '' ).' class="'.$css_line.'">'."\n"
			.( $legend != '' ? '<legend>'.$legend.'</legend>' : '' )
			.'<div class="fieldset_content"'.( $id_field != '' ? ' id="content_'.$id_field.'"' : '' ).'>';
	}

	/**
	 * @return string with the html code for close a fieldset
	 */
	function getCloseFieldset( ) {

		return '</div>'
			.'</fieldset>'."\n";
	}

	/**
	 * function getTextarea( $label_name, $id, $name, $maxlenght, $value, $other_after )
	 *
	 * this function is a temporary substitute for a more complete one
	 */
	function getTextarea($label_name, $id, $name, $value = '', $extra_param_for = false, $id_form = '',
				$css_line = 'form_line_l', $css_label = 'floating', $css_text = 'textarea') {

		$html_code = '<div class="'.$css_line.'">'."\n"
			.'<p><label class="'.$css_label.'" for="'.$id.'">'.$label_name.'</label></p><br />'."\n"
			.loadHtmlEditor($id_form, $id, $name, $value, $css_text, $extra_param_for)."\n"
			.'</div>'."\n";
		return $html_code;
	}

	function getInputTextarea($id ,$name , $value = '', $css_text = false, $rows = 5 ) {

		if($css_text === false) $css_text = 'textarea';

		return '<textarea class="'.$css_text.'" id="'.$id.'" name="'.$name.'" rows="'.$rows.'">'.$value.'</textarea>'."\n";
	}

	function getSimpleTextarea($label_name, $id ,$name , $value = '',
				$css_line = false, $css_label = false, $css_text = false, $rows = 5 ) {

		if($css_line === false) $css_line = 'form_line_l';
		if($css_label === false) $css_label = 'floating';
		if($css_text === false) $css_text = 'textarea';

		return '<div class="'.$css_line.'">'."\n"
			.'<p><label class="'.$css_label.'" for="'.$id.'">'.$label_name.'</label></p>'
			.Form::getInputTextarea($id ,$name , $value, $css_text, $rows)
			.'</div>'."\n";
	}


	/**
	 * function getBreakRow()
	 *
	 * @return string with the html for a line break
	 */
	function getBreakRow( ) {

		return '<div class="no_float"></div><br />'."\n";
	}

	function openFormLine() {
		return '<div class="form_line_l">'."\n";
	}

	function closeFormLine() {
		return '</div>'."\n";
	}

	function getLabel( $for, $label_name, $css_label = 'floating' /*'label_bold'*/ ) {
		return '<label class="'.$css_label.'" for="'.$for.'">'.$label_name.'</label>'."\n";
	}

	/**
	 * function closeElementSpace()
	 * @return string contains the close tag for element container
	 */
	function closeElementSpace( ) {
		return '<div class="no_float"></div>'."\n"
			.'</div>'."\n";
	}

	/**
	 * function openButtonSpace()
	 * @param string $css_div the css class
	 *
	 * @return string contains the open tag for button element
	 */
	function openButtonSpace($css_div = false) {
		return '<div class="'.( $css_div == false ? 'form_elem_button' : $css_div ).'">'."\n";
	}

	/**
	 * function getReset( $id, $name, $value, $css_class )
	 *
	 * @param string $id 			the id of the reset button
	 * @param string $name 			the name of the reset button
	 * @param string $value 		the value of the reset button
	 * @param string $css_button 	optional css class for the button
	 *
	 * @return string contains the close tag for reset element
	 */
	function getReset( $id, $name, $value, $css_button = 'button' ) {
		return '<input type="reset" '
				."\n\t".'class="'.$css_button.'" '
				."\n\t".'id="'.$id.'" '
				."\n\t".'name="'.$name.'" '
				."\n\t".'value="'.$value.'" />'."\n";
	}

	/**
	 * function getButton( $id, $name, $value, $css_class )
	 *
	 * @param string $id 			the id of the submit button
	 * @param string $name 			the name of the submit button
	 * @param string $value 		the value of the submit button
	 * @param string $css_button 	optional css class for the button
	 * @param string $other_param 	other element for the tag
	 *
	 * @return string contains the close tag for button element
	 */
	function getButton( $id, $name, $value, $css_button = 'button', $other_param = '' ) {
		return '<input type="submit" '
				."\n\t".'class="'.$css_button.'" '
				."\n\t".'id="'.$id.'" '
				."\n\t".'name="'.$name.'" '
				."\n\t".'value="'.$value.'"'.( $other_param != '' ? ' '.$other_param : '' ).' />'."\n";
	}

	/**
	 * function closeButtonSpace()
	 *
	 * @return string contains the close tag for button element
	 */
	function closeButtonSpace( ) {
		return '</div>'."\n";
	}

	/**
	 * function closeForm()
	 *
	 * @return string contains the close tag for the form
	 */
	function closeForm() {

		return '</div>'."\n"
			.'</form>'."\n";
	}
}

?>
