<?php
/************************************************************************/
/* DOCEBO LMS - Learning managment system        */
/* ============================================       */
/*                  */
/* Copyright (c) 2008             */
/* http://www.docebo.com            */
/*                  */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.  */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

define('_HTML', 'html');
define('_CSV', 'csv');
define('_XLS', 'xls');

define('_CSV_SEPARATOR', ',');
define('_CSV_ENDLINE', "\r\n");

define('_REPORT_TABLE_STYLE', 'report_table');


class ReportTablePrinter {
	
	var $type;
	var $buffer;
	var $rowCounter;
	var $overflow;
	
	function ReportTablePrinter($type=_HTML, $overflow=false) {
		$this->type = $type;
		$this->buffer = '';
		$this->rowCounter = 0;
		$this->overflow = $overflow;
		
		$this->addReportHeader();
	}
	
	
	function addBreak() {
		switch ($this->type) {
			
			case _HTML: {
				$this->buffer .= '<br /><br />';
			} break;
			
			case _CSV: {
				$this->buffer .= _CSV_ENDLINE._CSV_ENDLINE;
			} break;
			
			case _XLS: {
				$this->buffer .= '<br /><br />';
			} break;
			
			default: { } break;
		}
	}
	
	
	function addReportHeader() {
		$lang =& DoceboLanguage::createInstance('report', 'framework');
		$date = date("d/m/Y h:i:s");
		$temp = $lang->def('_CREATION_DATE');
		$content_html = '<b>'.$temp.'</b>: '.$date;
		$content_csv  = $temp.': '.$date;
		
		switch ($this->type) {
			
			case _HTML: {
				$this->buffer .= '<p id="report_info">'.$content_html.'</p><br />';
			} break;
			
			case _CSV: {
				$this->buffer .= $content_csv._CSV_ENDLINE._CSV_ENDLINE;
			} break;
			
			case _XLS: {
				$head='<head><meta http-equiv="content-type" content="text/html; charset=utf-8"></head>';
				$this->buffer .= $head.'<style>'.
					'td, th { border:solid 1px black; } '.
					'</style><table><tr><td>'.$content_html.'</td></tr></table><br /><br />';
			} break;
			
			default: { } break;
		}
	}
	
	function openTable($caption='', $summary='') {
		switch ($this->type) {
			
			case _HTML: {
				if ($this->overflow) $this->buffer .= '<div style="overflow:auto; padding:1px;">';
				$this->buffer .= '<table class="'._REPORT_TABLE_STYLE.'" summary="'.$summary.'">';
				if ($caption!='') $this->buffer .= '<caption>'.$caption.'</caption>';
			} break;
			
			case _CSV: {
				$this->buffer .= _CSV_ENDLINE.$caption._CSV_ENDLINE;
			} break;
			
			case _XLS: {
				//$this->buffer .= '<table>';
				$this->buffer .= '<table summary="'.$summary.'">';
				if ($caption!='') $this->buffer .= '<caption>'.$caption.'</caption>';
			} break;
			
			default: { } break;
		}
	}
	
	function closeTable() {
		switch ($this->type) {
			
			case _HTML: {
				$this->buffer .= '</table>';
				if ($this->overflow) $this->buffer .= '</div>';
			} break;
			
			case _CSV: {
			} break;
			
			case _XLS: {
				$this->buffer .= '</table>';
			} break;
			
			default: { } break;
		}
	}
	
	
	function openHeader() {
		switch ($this->type) {
			
			case _HTML: {
				$this->buffer .= '<thead>';
			} break;
			
			case _CSV: { 
				$this->buffer .= '';
			} break;
			
			case _XLS: {
				$this->buffer .= '<thead>';
			} break;
			
			default: { } break;
		}
	
	}
	
	
	function closeHeader() {
		switch ($this->type) {
			
			case _HTML: {
				$this->buffer .= '</thead>';
			} break;
			
			case _CSV: {
			} break;
			
			case _XLS: {
				$this->buffer .= '</thead>';
			} break;
			
			default: { } break;
		}
	}
	
	//bufferize a table header
	function addHeader(&$head) {
		switch ($this->type) {
			
			case _HTML: {
				$this->buffer .= '<tr>';
				foreach($head as $val) {
					if (is_array($val))
						$this->buffer .= '<th'
							.(isset($val['colspan']) ? ' colspan="'.$val['colspan'].'"' : '')
							//.(isset($val['rowspan']) ? ' rowspan="'.$val['rowspan'].'"' : '').
							.'>'.$val['value'].'</th>';
					else
						$this->buffer .= '<th>'.$val.'</th>';
				}
				$this->buffer .= '</tr>';
			} break;
			
			case _CSV: {
				$temp=array();
				foreach($head as $val) {
					if (is_array($val)) {
						$temp[] = $val['value'];
						if (isset($val['colspan']))
							for ($i=1; $i<$val['colspan']; $i++) $temp[]='';
					} else {
						$temp[] = $val;
					}
				}
				$this->buffer .= implode(_CSV_SEPARATOR, $temp)._CSV_ENDLINE;
			} break;
			
			case _XLS: {
				$this->buffer .= '<tr>';
				foreach($head as $val) {
					if (is_array($val))
						$this->buffer .= '<th colspan="'.$val['colspan'].'">'.$val['value'].'</th>';
					else
						$this->buffer .= '<th>'.$val.'</th>';
				}
				$this->buffer .= '</tr>';
			} break;
			
			default: { } break;
		}	
	}
	
	
	
	//table body management
	
	function openBody() {
		switch ($this->type) {
			
			case _HTML: {
				$this->buffer .= '<tbody>';
			} break;
			
			case _CSV: {
			} break;
			
			case _XLS: {
				$this->buffer .= '<tbody>';
			} break;
			
			default: { } break;
		}
	}
	
	
	
	function closeBody() {
		switch ($this->type) {
			
			case _HTML: {
				$this->buffer .= '</tbody>';
			} break;
			
			case _CSV: {
			} break;
			
			case _XLS: {
				$this->buffer .= '</tbody>';
			} break;
			
			default: { } break;
		}	
	}
	
	//bufferize a table row
	function addLine(&$line) {
		switch ($this->type) {
			
			case _HTML: {
				$this->buffer .= '<tr class="row'.( ++$this->rowCounter % 2 ? '' : '-col' ).'">';
				foreach($line as $val) { $this->buffer .= '<td>'.$val.'</td>'; }
				$this->buffer .= '</tr>';
			} break;
			
			case _CSV: { 
				$this->buffer .= implode(_CSV_SEPARATOR, $line)._CSV_ENDLINE;
			} break;
			
			case _XLS: {
				$this->buffer .= '<tr class="row'.( ++$this->rowCounter % 2 ? '' : '-col' ).'">';
				foreach($line as $val) { $this->buffer .= '<td>'.$val.'</td>'; }
				$this->buffer .= '</tr>';
			} break;
			
			default: { } break;
		}
	}
	
	
	//bufferize table foot
	function setFoot(&$line) {
		switch ($this->type) {
			
			case _HTML: {
				$this->buffer .= '<tfoot><tr>';
				foreach($line as $val) {
					if (is_array($val))
						$this->buffer .= '<td colspan="'.$val['colspan'].'">'.$val['value'].'</td>';
					else
						$this->buffer .= '<td>'.$val.'</td>';
				}
				$this->buffer .= '</tr></tfoot>';
			} break;
			
			case _CSV: {
				$temp=array();
				foreach($line as $val) {
					if (is_array($val)) {
						$temp[] = $val['value'];
						if (isset($val['colspan']))
							for ($i=1; $i<$val['colspan']; $i++) $temp[]='';
					} else {
						$temp[] = $val;
					}
				}
				$this->buffer .= implode(_CSV_SEPARATOR, $temp);
			} break;
			
			case _XLS: {
				$this->buffer .= '<tfoot><tr>';
				foreach($line as $val) {
					if (is_array($val))
						$this->buffer .= '<td colspan="'.$val['colspan'].'">'.$val['value'].'</td>';
					else
						$this->buffer .= '<td>'.$val.'</td>';
				}
				$this->buffer .= '</tr></tfoot>';
			} break;
			
			default: { } break;
		}
	}
	
	
	//return buffer content
	function get() {
		return $this->buffer;
	}
	
}

?>