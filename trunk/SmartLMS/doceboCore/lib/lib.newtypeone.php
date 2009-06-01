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
 * @package  admin-library
 * @subpackage interaction
 * @version 	$Id: lib.newtypeone.php 838 2006-12-01 17:56:20Z fabio $
 */

require_once(dirname(__FILE__).'/lib.navbar.php');

class TableCell {

	var $cell_type;
	var $style;
	var $abbr;
	var $label;
	var $colspan;
	var $rowspan;

	/**
	 * class constructor
	 * @param string	$label		content for this table cell
	 * @param string	$celltype	one of the two type of the cell 'header' or 'normal'
	 * @param string	$colspan	colspan for this table cell
	 * @param string	$rowspan	rowspan for this table cell
	 * @param string	$style		style class for this table cell
	 *
	 * @access public
	 */
	function TableCell($label, $cell_type = 'normal', $colspan = false, $rowspan= false, $style = false) {
		$this->label = $label;
		$this->abbr = strip_tags($label);
		$this->cell_type = $cell_type;
		if($colspan != false) 	$this->colspan = (int)$colspan;
		if($rowspan != false) 	$this->rowspan = (int)$rowspan;
		if($style != false) 	$this->style = $style;
	}

	/**
	 * @param string	$label	content for this table cell
	 *
	 * @access public
	 */
	function setLabel($label) {
		$this->label = $label;
	}

	/**
	 * @param string	$celltype	one of the two type of the cell 'header' or 'normal'
	 *
	 * @access public
	 */
	function setCellType($celltype) {
		$this->cell_type = (int)$celltype;
	}

	/**
	 * @param string	$colspan	colspan for this table cell
	 *
	 * @access public
	 */
	function setColspan($colspan) {
		$this->colspan = (int)$colspan;
	}

	/**
	 * @param string	$rowspan	rowspan for this table cell
	 *
	 * @access public
	 */
	function setRowspan($rowspan) {
		$this->rowspan = (int)$rowspan;
	}

	/**
	 * @param string	$style	style class for this table cell
	 *
	 * @access public
	 */
	function setStyle($style) {
		$this->style = $style;
	}


	/**
	 * @return string	a table cell
	 *
	 * @access public
	 */
	function getCell() {
		return '<'.( $this->cell_type == 'header' ? 'th scope="col"' : 'td' )
				.( $this->style != '' 	? ' class="'.$this->style.'"' 		: '' )
				.( $this->colspan != '' ? ' colspan="'.$this->colspan.'"' 	: '' )
				.( $this->rowspan != '' ? ' rowspan="'.$this->rowspan.'"' 	: '' ).'>'
				.( $this->label != '' ? $this->label  : '&nbsp;' )
				.'</'.( $this->cell_type == 'header' ? 'th' : 'td' ).'>'."\n";
	}

}

class TableRow {

	var $id;
	var $style;
	var $cells;
	var $cols;
	var $row_type;		// enum('header', 'normal', 'expanded')
	var $other_code;

	function TableRow($style = false, $row_type = false, $cols = false, $row_id = false, $other_code=FALSE) {
		$this->style = $style;
		$this->row_type = $row_type;
		$this->cols = (int)$cols;
		$this->id = $row_id;
		$this->other_code = strval($other_code);
	}

	function addRow($labels, $style = false, $colspan = false, $rowspan = false) {

		switch($this->row_type) {
			case 'header' : {
				$cell_type= 'header';
			};break;
			case 'expanded' :
			default : {
				$cell_type= 'normal';
			}
		}
		$i = 0;
		foreach($labels as $k => $label) {

			$this->cells[] = new TableCell($label, $cell_type, $colspan, $rowspan, ( isset($style[$i]) ? $style[$i] : '' ) );
			$i++;
		}
	}

	/**
	 * @param string	$cols	th number of cols, is used only if the row type is expanded
	 *
	 * @access public
	 */
	function setNumCol($cols) {
		$this->cols = (int)$cols;
	}

	/**
	 * @return string	a table row
	 *
	 * @access public
	 */
	function getRow() {

		if(!is_array($this->cells)) return '';

		$row = '<tr'.($this->style != '' ? ' class="'.$this->style.'"' : '' )
			.( $this->id !== false ? ' id="'.$this->id.'"' : '' )
			.(!empty($this->other_code) ? ' '.$this->other_code : '').'>'."\n";

		if($this->row_type == 'expanded') {

			$this->cells[0]->setColspan( ( $this->cols ? $this->cols : 1 ) );
			$row .= $this->cells[0]->getCell();
		} else {
			reset($this->cells);
			while(list(, $cell) = each($this->cells)) {

				$row .= $cell->getCell();
			}
		}
		$row .= '</tr>'."\n";
		return $row;
	}
}

class TypeOne {

	var $nav_bar;

	var $table_style;

	var $cols;
	var $max_rows;
	var $cols_class;

	var $table_caption;
	var $table_summary;
	var $table_id=FALSE;

	var $table_head;
	var $table_body;
	var $table_footer;

	var $row_count =0;
	var $join_next_row =FALSE;

	function TypeOne($max_rows = 10, $caption = '', $summary = '') {

		$this->table_style 		= 'type-one';

		$this->cols 			= 0;
		$this->max_rows 		= $max_rows;
		$this->cols_class 		= array();

		$this->table_caption 	= $caption;
		$this->table_summary 	= $summary;

		$this->table_head 		= array();
		$this->table_body 		= array();
		$this->table_foot 		= array();

		$this->nav_bar = NULL;

		/*i need this for the transiction from old to new*/
		$this->rows = 0;
		$this->maxRowsAtTime = $max_rows;
	}


	function setTableStyle($table_style) {

		$this->table_style = $table_style;
	}

	function setMaxRows($max_rows) {

		$this->max_rows = $max_rows;
	}

	function setCols($cols) {

		$this->cols = $cols;
	}

	function setColsStyle($style) {

		if(is_array($style)) $this->cols_class = $style;
	}

	function setCaption($caption) {
		$this->table_caption = $caption;
	}

	function setSummary($summary) {
		$this->table_summary = $summary;
	}

	function setTableId($table_id) {
		$this->table_id=$table_id;
	}

	function getTableId() {
		return $this->table_id;
	}

	function getRowCount() {
		return (int)$this->row_count;
	}

	function resetRowCount() {
		return $this->row_count=0;
	}

	function increaseRowCount() {
		return (int)$this->row_count++;
	}

	function getJoinNextRow() {
		$res =(bool)$this->join_next_row;
		$this->join_next_row =FALSE;
		return $res;
	}

	function setJoinNextRow() {
		$this->join_next_row =TRUE;
	}


	function addHead($labels, $style = false) {

		if($style !== false) $this->setColsStyle($style);
		$this->setCols(count($labels));

		$row = count($this->table_head) + 1;
		$this->table_head[$row] = new TableRow('type-one-header', 'header', $this->cols);

		$this->table_head[$row]->addRow($labels, $this->cols_class);
	}

	function addHeadCustom($label) {

		$row = count($this->table_head) + 1;
		$this->table_head[$row]['label'] = $label;
		$this->table_head[$row]['is_string'] = true;
	}

	function addBody($labels, $style_row = false, $style_cell = false, $row_id = false) {

		if($style_cell !== false) $this->setColsStyle($style_cell);
		if (!$this->getJoinNextRow()) {
			$this->increaseRowCount();
		}
		$row_count = $this->getRowCount();
		$row = count($this->table_body) + 1;
		if($style_row === false) {
			if($row_count % 2) $style_row = 'line-col';
			else $style_row = 'line';
		}

		$this->table_body[$row] = new TableRow($style_row, 'normal', $this->cols, $row_id);

		$this->table_body[$row]->addRow($labels, $this->cols_class);
	}

	function addBodyExpanded($label, $style_row = false, $other_code=FALSE) {

		if($style_row === false) $style_row = 'type-one-bodyexp-row';

		if (!$this->getJoinNextRow()) {
			$this->increaseRowCount();
		}
		$row_count = $this->getRowCount();
		$row = count($this->table_body) + 1;

		if($row_count % 2) $style_row .= ' line-col';
		else $style_row .= ' line';

		$this->table_body[$row] = new TableRow($style_row, 'expanded', $this->cols, FALSE, $other_code);

		$this->table_body[$row]->addRow(array($label), array($style_row));
	}

	function addBodyCustom($label) {

		$row = count($this->table_body) + 1;
		$this->table_body[$row]['label'] = $label;
		$this->table_body[$row]['is_string'] = true;
	}

	function emptyBody() {

		$this->table_body = array();
	}

	function emptyFoot() {

		$this->table_foot = array();
	}

	function addFoot($labels, $style = false) {

		if($style !== false) $this->setColsStyle($style);

		$row = count($this->table_foot) + 1;
		$this->table_foot[$row] = new TableRow($style, 'normal', $this->cols);

		$this->table_foot[$row]->addRow($labels, $this->cols_class);
	}

	function addFootCustom($label) {

		$row = count($this->table_head) + 1;
		$this->table_foot[$row]['label'] = $label;
		$this->table_foot[$row]['is_string'] = true;
	}

	function addActionAdd($label, $style = false) {

		if($style === false) $style = 'type-one-add-row';
		$row = count($this->table_foot) + 1;
		$this->table_foot[$row] = new TableRow($style, 'expanded', $this->cols);

		$this->table_foot[$row]->addRow(array($label), array($style));
	}

	/**
	 * @return	string	the xhtml code for the composed table
	 */
	function getTable() {

		$this->resetRowCount();

		if(count($this->table_head) == 0 && count($this->table_foot) == 0 && count($this->table_body) == 0) {
			return '';
		}
		$table = '<table class="'.$this->table_style.'" ';
		$table.= ($this->getTableId() !== FALSE ? 'id="'.$this->getTableId().'" ' : "");
		$table.= 'summary="'.$this->table_summary.'" cellspacing="0">'."\n";
		if($this->table_caption != '') {
			$table .= '<caption>'.$this->table_caption.'</caption>'."\n";
		}
		if(count($this->table_head)) {

			reset($this->table_head);
			$table .= '<thead>'."\n";
			while(list(, $row) = each($this->table_head)) {
				if(is_array($row)) $table .= $row['label'];
				else $table .= $row->getRow();
			}
			$table .= '</thead>'."\n";
		}
		if(count($this->table_foot)) {

			reset($this->table_foot);
			$table .= '<tfoot>'."\n";
			while(list(, $row) = each($this->table_foot)) {
				if(is_array($row)) $table .= $row['label'];
				else $table .= $row->getRow();
			}
			$table .= '</tfoot>'."\n";
		}
		if(count($this->table_body)) {

			reset($this->table_body);
			$table .= '<tbody>'."\n";
			while(list($k, $row) = each($this->table_body)) {
				if(is_array($row)) $table .= $row['label'];
				else $table .= $row->getRow();
			}
			$table .= '</tbody>'."\n";
		}
		$table .= '</table>';
		return $table;
	}

	function initNavBar($var_name, $kind_of = false) {

		$this->nav_bar = new NavBar($var_name, $this->max_rows, 0, $kind_of);
	}

	/**
	 * @param string	$var_name 	the variable name
	 */
	function setVarName($var_name) {

		if($this->nav_bar === NULL) return;
		$this->nav_bar->setVarName($var_name);
	}

	/**
	 * @param string	$link 	the link used in the navbar if the kindof is link
	 */
	function setLink($link) {

		if($this->nav_bar === NULL) return;
		$this->nav_bar->setLink($link);
	}

	/**
	 * @param array	$kind_of 	the kind of nav bar(link or button)
	 */
	function setKindOf($kind_of) {

		if($this->nav_bar === NULL) return;
		$this->nav_bar->setKindOf($kind_of);
	}

	/**
	 * @param string	$current_element 	the current first element (from 0 to $total_element)
	 * @param string	$total_element 		the numbers of all elements
	 *
	 * @return string html code for a navigation bar
	 */
	function getNavBar($current_element, $total_element) {

		if($this->nav_bar === NULL) return '';
		$this->nav_bar->setElementTotal($total_element);

		return $this->nav_bar->getNavBar($current_element);
	}

	function getSelectedElement($var_name = false, $kind_of = false) {

		return $this->nav_bar->getSelectedElement($var_name, $kind_of);
	}

	function getSelectedPage($var_name = false, $kind_of = false) {

		return $this->nav_bar->getSelectedPage($var_name, $kind_of);
	}

	function asSelected($var_name = false, $kind_of = false) {

		return $this->nav_bar->asSelected($var_name, $kind_of);
	}












































	/******************************************************************/
	/*this is for transition before old typeone to the new one*********/
	/******************************************************************/


	function openTable( $title, $summary = '' ) {
		// EFFECTS: write title if exists and open the table
		global $module_cfg;
		return '<table class="type-one" cellspacing="0" summary="'.( $summary != '' ? $summary : 'table summary' ).'">'."\n"
				.( ($title != '') ? '<caption>'.$title.'</caption>' : '' );
	}

	function closeTable() {
		// EFFECTS: close the table
		return '</tbody>'
			.'</table>'."\n\n";
	}

	function writeHeader($colElem, $colsType) {
		// EFFECTS: write the header of the table

		$this->typeCol = $colsType;
		$code = '<thead>'."\n"
			.'<tr>'."\n";
		while(list($key, $contentCell) = each($colElem)) {
			++$this->cols;
			$code .= "\t".'<th';
			if (trim($colsType[$key]) != '') {
				switch(trim($colsType[$key])) {
					case "img" : $code .= ' class="image"';break;
					default : $code .= ' class="'.$colsType[$key].'"';
				}
			}
			$code .= '>'."\n"
				."\t\t".$contentCell."\n"
				."\t".'</th>'."\n";
		}
		return $code.'</tr>'."\n"
				.'</thead>'."\n"
				.'<tbody>'."\n";
	}

	function setTypeCol( $colsType ) {
		//EFFECTS: assign cols type

		$this->typeCol = $colsType;
	}

	/**
 	 * function WriteHeaderCss
	 *
	 * @param $colElem an array of column headers
	 * 	each element must contain:
	 *	['hLable'] => header Lable
	 *	['hClass'] => header Class
	 *	['toDisplay'] => toDysplay true or false
	 *	['sortable'] => sortable true or false
	 **/
	function writeHeaderCss($colElem) {
		//EFFECTS: write the header of the table
		$code = '<thead>'."\n"
			.'<tr>'."\n";
		while(list($key, $contentCell) = each($colElem)) {
			if( $contentCell['toDisplay'] ) {
				++$this->cols;
				$code .= '<th'.($contentCell['hClass'] != '' ? ' class="'.$contentCell['hClass'].'"' : '' ).'>'
					.$contentCell['hLabel']
					.'</th>';
			}
		}
		return $code.'</tr>'."\n"
				.'</thead>'."\n"
				.'<tbody>'."\n";
	}

	function writeRow($colsContent) {
		//EFFFECTS: write the row
		$code = '<tr class="line'.($this->rows % 2? '' : '-col' ).'">'."\n";
		while(list($key, $contentCell) = each($colsContent)) {
			$code .= "\t".'<td';
			if (trim($this->typeCol[$key]) != '') {
				switch(trim($this->typeCol[$key])) {
					case "img" : $code .= ' class="image"';break;
					default : $code .= ' class="'.$this->typeCol[$key].'"';
				}
			}
			$code .='>'."\n"
				."\t\t".(($contentCell != '') ? $contentCell : '&nbsp;')."\n"
				."\t".'</td>'."\n";
		}
		++$this->rows;
		return $code.'</tr>'."\n";
	}

	/**
 	 * function WriteRowCss
	 *
	 * @param $colElem an array of field
	 * 	each element must contain:
	 *	['data'] => string to print out
	 *	['filedClass'] => header Class
	 *	['toDisplay'] => toDysplay true or false
	 *	['sortable'] => sortable true or false
	 **/
	function writeRowCss($colElem) {
		//EFFFECTS: write the row
		$code = '<tr class="line'.($this->rows % 2? '' : '-col' ).'">';
		while(list($key, $contentCell) = each($colElem)) {
			if( $contentCell['toDisplay'] ) {
				$code .= '<td'.($contentCell['fieldClass'] != '' ? ' class="'.$contentCell['fieldClass'].'"' : '' ).'>'
					.$contentCell['data']
					.'</td>';
			}
		}
		$code .= '</tr>';
		++$this->rows;
		return $code;
	}

	function writeAddRow($text) {
		//write the bar for navigate the result
		return '<tr class="type-one-add-row">'
			.'<td colspan="'.$this->cols.'">'.$text.'</td>'
			.'</tr>';
	}

	function writeNavBar($symbol, $link, $actualRow, $totalRow, $existNext = false) {
		//REQUIRES: _PREV, _PREVELEM, _NEXT, _NEXTELEM, $link end with ini=
		//	$symbols = array(
		//		'start' => '',
		//		'prev' => '',
		//		'next' => '',
		//		'end' => ''
		//	);
		//EFFECTS: write the navbar

		//math for number of page
		if($this->maxRowsAtTime == 0) return;
		if( !is_array($symbol) ) {
			$symbol = array(
				'start' => '<img src="'.getPathImage().'standard/start.gif" alt="'.def('_START').'" title="'.def('_START').'" />',
				'prev' => '<img src="'.getPathImage().'standard/prev.gif" alt="'.def('_PREV').'" title="'.def('_PREV').'" />',
				'next' => '<img src="'.getPathImage().'standard/next.gif" alt="'.def('_NEXT').'" title="'.def('_NEXT').'" />',
				'end' => '<img src="'.getPathImage().'standard/end.gif" alt="'.def('_END').'" title="'.def('_END').'" />'
			);
		}
		$nav = '';
		if($totalRow) {
			//if i have the number of the result i can write the navbar with the page number
			$numberOfPage = (int)(($totalRow / $this->maxRowsAtTime) + (($totalRow % $this->maxRowsAtTime) ? 1 : 0));
			$currentPage = (int)($actualRow / $this->maxRowsAtTime) + 1;

			if ($numberOfPage <= 7) {
				$start = 1;
				$end = $numberOfPage;
			}
			else {
				$start = (($currentPage - 3 < 1) ? 1 : $currentPage - 3);
				$end = (($currentPage + 3 > $numberOfPage) ? $numberOfPage : $currentPage + 3);
			}
			//total number of page
			$nav .= '<div class="nav-bar">';

			$nav .= '<div class="float_right">'
				.def('_RE').$totalRow.' '
				.def('_PG').$numberOfPage.'</div>';

			//jump to start position
			if($start != '1') $nav .= '<a href="'.$link.'0">'.$symbol['start'].'</a>&nbsp;';
			//jump one backward
			if($currentPage != '1') $nav .= '<a href="'.$link.($actualRow - $this->maxRowsAtTime).'">'.$symbol['prev'].'</a>&nbsp;';
			$nav .= '(&nbsp;';
			if($start != '1') $nav .= '...&nbsp;';

			//print pages numbers
			for($page = $start; $page <= $end; $page++) {
				if($page == $currentPage) $nav .= '<span class="current">[ '.$page.' ]</span> ';
				else {
					$nav .= '<a href="'.$link.(($page - 1) * $this->maxRowsAtTime).'">'.$page.'</a>&nbsp;';
				}

			}

			if(($page - 1) != $numberOfPage) $nav .= '...&nbsp;';
			$nav .= ')&nbsp;';
			//jump one forward
			if($currentPage != $numberOfPage) $nav .= '<a href="'.$link.($actualRow + $this->maxRowsAtTime).'">'.$symbol['next'].'</a>';
			//jump to end position
			if(($page - 1) != $numberOfPage) $nav .= '&nbsp;<a href="'.$link.(($numberOfPage - 1) * $this->maxRowsAtTime).'">'.$symbol['end'].'</a>';
			$nav .= '<div class="no_float"></div></div>';
		}
		else {
			//if i haven't the number of result
			if(($actualRow != '0') || $existNext) $nav .= '<div class="nav-bar">';

			if($actualRow != '0') $nav .= '<a href="'.$link.($actualRow - $this->maxRowsAtTime).'">'.$symbol['prev'].'</a>&nbsp;';
			if(($actualRow != '0') && $existNext)$nav .= ' -------- ';
			//jump one forward
			if($existNext) $nav .= '<a href="'.$link.($actualRow + $this->maxRowsAtTime).'">'.$symbol['next'].'</a>';

			if(($actualRow != '0') || $existNext) $nav .= '</div>';
		}
		return $nav;
	}
}

?>