<?

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System						 */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*																		 */
/* Copyright (c) 2002 by Claudio Erba (webmaster@spaghettilearning.com)  */
/* & Fabio Pirovano (gishell@tiscali.it) http://www.spaghettilearning.com*/
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

class Module_Bugtracker extends Module {


	function loadHeader() {
		//EFFECTS: write in standard output extra header information
		global $op;

		switch($op) {
			case "appmodgroups" : {
				echo '<script type="text/javascript">
							/*
							 * Questa funzione imposta tutti i checkbox presenti in un form
							 * il cui nome (name) è check_name (anche ad es check_name[34])
							 * i checkbox trovati vengono impostati a assign. Se questo parametro
							 * è omesso il valore dei checkbox viene invertito
							 */
							function checkall( form_name, check_name, assign ) {
								var form = document.forms[form_name];
								for (var i = 0; i < form.elements.length; i++) {
									if( form.elements[i].name.indexOf( check_name + "[" ) >= 0 )
										if( arguments.length > 2 ) {
											form.elements[i].checked = assign;
										}
										else {
											form.elements[i].checked = !form.elements[i].checked;
										}
									//alert("["+i+"]"+check_name+": "+form.elements[i].name);
								} // end for
							}
						</script>';
			} break;

		}

		return;
	}
}

$module_cfg = new Module_Bugtracker();

?>