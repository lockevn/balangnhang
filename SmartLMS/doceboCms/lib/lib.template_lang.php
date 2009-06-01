<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/


function getCmsLang() {
	//REQUIRES: $newLang exist
	//EFFECTS : return the correct language
	global $defaultCmsLanguage;

	if(isset($_SESSION['sesCmsLanguage']) && ($_SESSION['sesCmsLanguage'] != ''))
		return $_SESSION['sesCmsLanguage'];
	else return getLanguage();
}

function getLangFlags() {
	$use_mod_rewrite=$GLOBALS["cms"]["use_mod_rewrite"];

	$res="";

	$lang=& DoceboLanguage::createInstance('blind_navigation', 'cms');
	$blind_link="<li><a href=\"#lang_box\">".$lang->def("_LANG_SELECT")."</a></li>";
	$GLOBALS["page"]->add($blind_link, "blind_navigation");

	$qtxt="";
	$qtxt.="SELECT t1.lang_code, t1.lang_description FROM ".$GLOBALS["prefix_fw"]."_lang_language as t1, ".$GLOBALS["prefix_cms"]."_area as t2 ";
	$qtxt.="WHERE t2.langdef='1' AND t2.publish='1' AND t1.lang_code=t2.title";

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$name=$row["lang_description"];

			if (strtolower($use_mod_rewrite) == "off") {
				$res.="\n<a href=\"index.php?special=changelang&amp;newLang=".$row["lang_code"]."\">";
			}
			else {
				$file_title=format_mod_rewrite_title($name);
				$res.="<a href=\"set-language_".$file_title."-".$row["lang_code"].".html\">";
			}

			$res.="<img src=\"".getPathImage()."language/".$row["lang_code"].".png\" alt=\"$name\" title=\"$name\" /></a>\n";
		}
	}

	return $res;
}



function loadMenuOver() {
	$use_mod_rewrite=$GLOBALS["cms"]["use_mod_rewrite"];

	$lang_root=get_lang_root();
	$current=getIdArea();

	$lang=& DoceboLanguage::createInstance('blind_navigation', 'cms');
	$blind_link="<li><a href=\"#menuover_box\">".$lang->def("_MENU_OVER")."</a></li>";
	$GLOBALS["page"]->add($blind_link, "blind_navigation");

	$res="";

	$query = "
	SELECT area.idArea, area.title, area.mr_title, area.alias, area.home, area.link
	FROM ".$GLOBALS["prefix_cms"]."_area AS area
	WHERE area.lev = '2' AND
		area.path LIKE '$lang_root%' AND
		area.publish = '1' AND
		area.show_in_macromenu = '1'
		ORDER BY area.path";
	$reMenu = mysql_query($query);


	$use_dropdown =($GLOBALS["cms"]["cms_use_dropdown_menu"] == 1 ? TRUE : FALSE);
	if ($use_dropdown) {
		setupDropdownMenu();
		$dropdown_arr =getMenuOverDropdownArr($lang_root);
		$action_code ='onmouseover="ManDropdown.open_menu(this);" onmouseout="ManDropdown.close_menu(this);"';
	}
	else
		$action_code = '';


	$res.='<ul class="main_menu_over">';

	$open_li ='<li class="menu_close" '.$action_code.'>';

	while(list($id_area, $page_title, $mr_title, $alias, $home, $link) = mysql_fetch_row($reMenu)) {

		if (checkRoleForItem("page", $id_area)) {

			$class=($current == $id_area ? "class=\"selected first_line\"" : "class=\"link first_line\"");
			$class_ext=($current == $id_area ? "selected first_line" : "link first_line");

			if ($alias != "")
				$title=$alias;
			else
				$title=$page_title;

			if ($link != "") {
				$link =fillSiteBaseUrlTag($link);
				
				if(!stristr($link, 'www') && !stristr($link, 'http://') && !stristr($link, 'https://') || stristr($link, $GLOBALS['_SERVER']['HTTP_REFERER']))
					$new_win = false;
				else
					$new_win = true;
				
				/*$host=$_SERVER['HTTP_HOST'];
				if (!strstr($link, $host)) $new_win=true; else $new_win=false;*/

				if (!$new_win)
					$res.=$open_li."<a ".$class." href=\"$link\">";
				else {
					$res.=$open_li.open_ext_link($link, $class_ext);
				}
			}
			else {

				if ($home) {
					$res.=$open_li.'<a '.$class.' href="'.$GLOBALS["where_cms_relative"].'/">';
				}
				else {

					if (strtolower($use_mod_rewrite) == "off") {
						$res.=$open_li.'<a '.$class.' href="index.php?special=changearea&amp;newArea='.$id_area.'">';
					}
					else {
						$res.=$open_li.getMrOpenLink($id_area, $title, $mr_title, $class);
					}
				}
			}

			$res.=$title;

			$res.='</a>';
			if (($use_dropdown) && (isset($dropdown_arr[$id_area])) && (!empty($dropdown_arr[$id_area]))) {
				$res.=$dropdown_arr[$id_area];
			}
			$res.='</li>';
		}
	}

	$res.="</ul>";

	return $res;
}


function setupDropdownMenu() {
	addJs($GLOBALS['where_framework_relative'].'/addons/scriptaculous/lib/', "prototype.js");
	addJs($GLOBALS['where_cms_relative'].'/lib/js/', "lib.menu_close.js");

	$code ='<script type="text/javascript">'."\n\t";
	$code.="setup_menu('open_close', 'menu_open', 'menu_close');\n";
	$code.='</script>'."\n";

	$GLOBALS["page"]->add($code, "footer");
}


/**
 * @author Giovanni Derks <virtualdarkness[AT]gmail-com>
 *
 * @return array array with menu list code.
 */
function getMenuOverDropdownArr($lang_root) {
	$res =array();
	$res_key =0;


	$open_ul ='<ul>';
	$open_first_ul ='<ul  class="list_modules">';
	$close_ul ="</ul>";
	$open_li ='<li class="menu_close">';
	$close_li ="</li>";
	
	$use_dropdown =($GLOBALS["cms"]["cms_use_dropdown_menu"] == 1 ? TRUE : FALSE);
	if ($use_dropdown)
	         $action_code ='onmouseover="ManDropdown.open_menu(this);" onmouseout="ManDropdown.close_menu(this);"';
	
	$open_submenu_li ='<li class="menu_close" '.$action_code.'>';
	$close_submenu_li ="</ul></li>";

	// We grab the menu starting from level 2, then
	// we will use the idParent key to build our result key
	$menu =getMenuArray($lang_root, 2);

	$pointer =& $menu;
	$prev_arr =array();
	$level =1;

	do {

		$item = each($pointer);

		if ($item !== FALSE) {

			list($key, $val) = $item;

			$is_submenu =(substr($key, -7) == "submenu" ? TRUE : FALSE);

			if (!$is_submenu) {

				if (($val["lev"] == 3) && ($val["idParent"] != $res_key)) {
					if ((isset($res[$res_key])) && (!empty($res[$res_key]))) {
						$res[$res_key].=$close_ul; // Close main ul
					}
					$res_key =$val["idParent"];
					if (!isset($res[$res_key])) {
						$res[$res_key] =$open_first_ul.$open_li.getDropdownMainMenuItem($val["idParent"]).$close_li; // Open main ul
					}
				}

				$has_submenu =(isset($pointer[$key."_submenu"]) ? TRUE : FALSE);

				if ($has_submenu) {
					// Add item and opens submenu ul
					$res[$res_key].=$open_submenu_li.getDropdownMenuItem($val, $has_submenu).$open_ul;
				}
				else {
					// Just add the item
					$res[$res_key].=$open_li.getDropdownMenuItem($val, $has_submenu).$close_li;
				}
			}
			else {
				// Switch to next level
				$prev_arr[]=& $pointer;
				$pointer =& $pointer[$key];
			}

		}
		else if (count($prev_arr) > 0) {
			// Rollback to previous level
			$last_key =end(array_keys($prev_arr));
			$pointer =& $prev_arr[$last_key];
			unset($prev_arr[$last_key]);
			$item =TRUE; // don't give up!

			// Close submenu ul and li
			$res[$res_key].=$close_submenu_li;
		}

	} while ($item !== FALSE);


	if ((isset($res[$res_key])) && (!empty($res[$res_key]))) {
		$res[$res_key].="</ul>\n\n"; // Close the very last one ul
	}

	return $res;
}


function getDropdownMainMenuItem($area_id) {
	$res ="";

	$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area WHERE ";
	$qtxt.="idArea='".(int)$area_id."' LIMIT 1";

	$q =mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row =mysql_fetch_assoc($q);
		$res =getDropdownMenuItem($row, FALSE);
	}

	return $res;
}



function getDropdownMenuItem($row, $has_submenu) {
	$res ="";

	$use_mod_rewrite =($GLOBALS["cms"]["use_mod_rewrite"] == "on" ? TRUE : FALSE);


	$id =$row["idArea"];
	$lev =$row["lev"];
	$link =$row["link"];

	$new_win =FALSE;

	if (empty($link)) {
		if ($use_mod_rewrite) {
			$title =getCleanTitle((!empty($row["mr_title"]) ? $row["mr_title"] : $row["title"]));
			$url ="page/".$id."/".$title.".html";
		}
		else {
			$url ="index.php?special=changearea&amp;newArea=".$id;
		}
	}
	else {
		$link =fillSiteBaseUrlTag($link);
		
		if(!stristr($link, 'www') && !stristr($link, 'http://') && !stristr($link, 'https://') || stristr($link, $GLOBALS['_SERVER']['HTTP_REFERER']))
			$new_win = false;
		else
			$new_win = true;
		
		$url =$link;
	}

	if ($has_submenu) {
		$res.='<a class="arrow_left" href="'.$url.'">';
	}
	else
	{
		if($new_win)
			$res .= open_ext_link($link, "");
		else
			$res.='<a href="'.$url.'">';
	}
	$res.=$row["title"]."</a>";


	return $res;
}


/**
 * @author Giovanni Derks <virtualdarkness[AT]gmail-com>
 *
 * @return array multidimensional array with the pages tree
 */
function getMenuArray($base_path, $base_lev=1, $only_visible_items=TRUE) {
	$res =array();
	$debug =FALSE;

	$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area WHERE ";
	$qtxt.="lev > ".(int)$base_lev." AND path LIKE '".$base_path."%' ";
	$qtxt.="ORDER BY path";
	if ($debug) { echo $qtxt."<br />"; }

	$q =mysql_query($qtxt);

	$work_on =& $res;
	$prev_arr =array();
	$old_id =0;
	$old_lev =0;
	$submenu_lev =0;
	$last_submenu_id =FALSE;
	$first =TRUE;
	$skip =FALSE;
	$skip_from_lev =0;

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_assoc($q)) {

			$id =$row["idArea"];
			$lev =$row["lev"];


			if ($only_visible_items) {
				$show_page =FALSE;
				if ((checkRoleForItem("page", $id)) && ($row["publish"] == 1) && ($row["show_in_menu"] == 1)) {
					$show_page =TRUE;
				}
			}
			else {
				$show_page =TRUE;
			}

			if (($skip) &&  ($lev <= $skip_from_lev)) {
				if ($debug) {
					echo "Skip STOP: [".$id."] - Level: ".$lev."<br />";
				}

				$skip =FALSE;
				$skip_from_lev =0;
			}

			if ((!$skip) && ($show_page)) {

				if ((!$first) && ($lev > $old_lev)) {

					$key =$old_id."_submenu";
					$prev_arr[]=& $work_on;
					$work_on[$key]=array();
					$work_on =& $work_on[$key];
					$submenu_lev =$lev;

					if ($debug) {
						echo "&gt; ".$old_lev."::".$lev." [".$id."] - - ";
						echo str_replace("\n", "", var_export(array_keys($prev_arr), TRUE))."<br />";
					}
				}


				if (($lev < $submenu_lev) && (count($prev_arr) > 0)) {

					// Remove empty submenu
					$remove_empty =FALSE;
					if (empty($work_on)) {
						$remove_empty =TRUE;
					}

					// Rollback to previous level
					for ($i=0; $i<($submenu_lev-$lev); $i++) {
						$last_key =end(array_keys($prev_arr));
						$work_on =& $prev_arr[$last_key];
						unset($prev_arr[$last_key]);

						if ($remove_empty) { // Removes the last created level if it is empty.
							$last_key =end(array_keys($work_on));
							if (empty($work_on[$last_key])) {
								unset($work_on[$last_key]);
								$remove_empty =FALSE;
							}
						}
					}


					if ($debug) {
						echo "&lt; ".$submenu_lev."::".$lev." [".$id."] - - ";
						echo str_replace("\n", "", var_export(array_keys($prev_arr), TRUE))."<br />";
					}

					$submenu_lev =$lev;
				}


				$work_on[$id]=$row;

				$old_id =$id;
				$old_lev =$lev;
			}
			else if (!$skip) {

				if ($debug) {
					echo "Skip START: [".$id."] - Level: ".$lev."<br />";
				}

				$skip =TRUE;
				$skip_from_lev =$lev;
			}

			$first =FALSE; //echo $id.".";
		}
	}


	if ($debug) { echo "<pre>"; print_r($res); echo "</pre>"; die(); }
	return $res;
}


function my_print_r($lang_root) {

	echo "<br />";
	echo memory_get_usage()."<br /><br />";

	$x =array();
	$y =array(12=>"kflsdf", 19=>"lfjsldjfsl");
	$z1 =array(13=>"hjghjn", 29=>"cmxvn");
	$z2 =array(41=>"cvweroxx");
	$z3 =array(45=>"mnvor", 32=>"eiourx", 44=>$z2);

	$x[4]="mcxasdo";
	$x[5]="oiwrod";
	$x[6] =$y;
	$x[7] =$z1;
	$x[8] =$z3;
	$x[9]="oiwrod";

	$pointer =& $x;
	$prev_arr =array();
	$level =1;
	$level_dot ="-";

	$xx =0;
	do {

		$item = each($pointer);

		if ($item !== FALSE) {

			list($key, $val) = $item;

			if (!is_array($val)) {
				echo($level_dot."[".$key."] ".$val."<br />");
			}
			else {
				$prev_arr[]=& $pointer;
				$pointer =& $pointer[$key];
				echo ($level_dot."[".$key."] <b>Array</b>: ("."<br />");
				$level++;
				$level_dot =($level > 1 ? str_repeat("&nbsp;", ($level-1)*4) : "").str_repeat("-", $level);
			}

		}
		else if (count($prev_arr) > 0) {
			$last_key =end(array_keys($prev_arr));
			$pointer =& $prev_arr[$last_key];
			unset($prev_arr[$last_key]);

			$level--;
			$level_dot =($level > 1 ? str_repeat("&nbsp;", ($level-1)*4) : "").str_repeat("-", $level);
			echo ($level_dot." )"."<br />");
			$item =TRUE;
		}

		$xx++;
		flush();
	} while (($item !== FALSE) && ($xx < 50));


	echo "<br />".memory_get_usage();

	die();
}

function getMenuOverDropdownArr_old2($lang_root) {
	$res =array();
	$debug =TRUE;

	$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area WHERE ";
	$qtxt.="lev > 1 AND path LIKE '".$lang_root."%' ";
	$qtxt.="ORDER BY path";

	$q =mysql_query($qtxt);

	if ($debug) {
		$dbg =10000;
		$res[$dbg]=$qtxt;
	}

	$close_ul =($debug ? "_]" : "")."</ul>".($debug ? "\n\n" : "");
	$close_li =($debug ? "-&gt;" : "")."</li>";

	$key =0;
	$old_lev =0;
	$prev_row =FALSE;
	$lev_code ="";
	$skip =FALSE;
	$skip_from_lev =0;
	$old_lev_before_skip =0;

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_assoc($q)) {

			if ($prev_row !== FALSE) {

				$id =$prev_row["idArea"];
				$lev =$prev_row["lev"];

				if ($lev == 2) {
					if (!empty($lev_code)) {
						$res[$key]=$lev_code;
						$lev_code ="";
					}

					$key =$id;
				}

				if ($lev > 2) {

					$show_page =FALSE;
					if ((checkRoleForItem("page", $id)) && ($prev_row["publish"] == 1) && ($prev_row["show_in_menu"] == 1)) {
						$show_page =TRUE;
					}

					if ($skip_from_lev == $lev) {
						$old_lev =$old_lev_before_skip;
						$skip_from_lev =0;
						$skip =FALSE;
						if ($debug) {
							$dbg++;
							$res[$dbg]="Skip STOP: ".$lev." (area: ".$id.")";
						}
					}

					if ((!$skip) && ($show_page)) {
						$lev_code.=processMenuOverDropdownItem($prev_row, $row, $old_lev);
					}
					else if (!$skip) {
						$skip =TRUE;
						$skip_from_lev =$lev;
						$old_lev_before_skip =$old_lev;
						if ($debug) {
							$dbg++;
							$res[$dbg]="Skip START: ".$lev." (area: ".$id.")";
						}
					}
				}

				$old_lev =$lev;
			}

			$prev_row =$row;
		}

		// Processing the last one

		$id =$prev_row["idArea"];
		$lev =$prev_row["lev"];

		if ($lev > 2) {

			$show_page =FALSE;
			if ((checkRoleForItem("page", $id)) && ($prev_row["publish"] == 1) && ($prev_row["show_in_menu"] == 1)) {
				$show_page =TRUE;
			}

			if ($skip_from_lev >= $lev) {
				$old_lev =$old_lev_before_skip;
				$skip_from_lev =0;
				$skip =FALSE;
				if ($debug) {
					$dbg++;
					$res[$dbg]="Skip STOP: ".$lev." (area: ".$id.")";
				}
			}

			if ((!$skip) && ($show_page)) {
				$lev_code.=processMenuOverDropdownItem($prev_row, FALSE, $old_lev);
			}

		}
		if (!empty($lev_code)) {
			if ($lev-$old_lev > 1) {
				$lev_code.=str_repeat($close_ul, $lev-$old_lev);
			}
			$res[$key]=$lev_code;
		}
	}

	return $res;
}


function processMenuOverDropdownItem($current, $next, $old_lev) {
	$res ="";
	$debug =TRUE;

	if ($next === FALSE) {
		$next =array();
		$next["lev"] =$current["lev"]-1;
	}

	$use_mod_rewrite =($GLOBALS["cms"]["use_mod_rewrite"] == "on" ? TRUE : FALSE);

	$open_ul ='<ul>';
	$open_first_ul ='<ul  class="list_modules">';
	$close_ul ="</ul>".($debug ? "\n\n" : "");
	$open_li ='<li class="menu_close">';
	$action_code ='onmouseover="opem_menu(this, \'menu_open\');" onmouseout="close_menu(this, \'menu_close\');"';
	$open_submenu_li ='<li class="menu_close" '.$action_code.'>';
	$close_li ="</li>";
	if ($debug) {
		$open_ul ='<ul>[_';
		$open_first_ul ='<ul>[_';
		$close_ul ="</ul>_]".($debug ? "\n\n" : "");
		$open_li ='<li>&lt;-';
		$open_submenu_li ='<li>&lt;-';
		$close_li ="-&gt;</li>";
	}

	$id =$current["idArea"];
	$lev =$current["lev"];

	$has_child =($next["lev"] > $lev ? TRUE : FALSE);

	if ($lev > $old_lev) {
		$res.=($lev == 3 ? $open_first_ul : $open_ul).($debug ? ".".$lev."." : "");
	}


	if ($use_mod_rewrite) {
		$title =getCleanTitle($current["title"]);
		$url ="page/".$id."/".$title.".html";
	}
	else {
		$url ="index.php?special=changearea&amp;newArea=".$id;
	}

	if ($has_child) {
		$res.=$open_submenu_li;
		$res.='<a class="arrow_left" href="'.$url.'">';
	}
	else {
		$res.=$open_li;
		$res.='<a href="'.$url.'">';
	}
	$res.=$current["title"]."</a>";
	$res.=($debug ? " (".$id.")" : "");

	if (!$has_child) {
		$res.=$close_li;
	}

	if ($lev > $next["lev"]) {
		$res.=str_repeat($close_ul, $lev-$next["lev"]);
		$res.=($lev > 3 ? $close_li : "");
	}

	return $res;
}


function getMenuOverDropdownArr_old($lang_root) {
	$res =array();
	$debug =FALSE;

	$use_mod_rewrite =($GLOBALS["cms"]["use_mod_rewrite"] == 1 ? TRUE : FALSE);

	$qtxt ="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area WHERE ";
	$qtxt.="lev > 1 AND lev < 4 AND path LIKE '".$lang_root."%' ";
	$qtxt.="ORDER BY path";

	$open_ul ='<ul>';
	$open_first_ul ='<ul  class="list_modules">';
	$close_ul ="</ul>".($debug ? "\n\n" : "");
	$open_li ='<li class="menu_close">';
	$close_li ="</li>";

	$q =mysql_query($qtxt);

	if ($debug) {
		$dbg =10000;
		$res[$dbg]=$qtxt;
	}

	$key =0;
	$old_lev =0;
	$lev_code ="";
	$skip =FALSE;
	$skip_from =0;
	$li_open_lev =array();
	$li_open_lev_ptr =0; // li_open_lev array pointer
	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_assoc($q)) {

			$id =$row["idArea"];
			$lev =$row["lev"];


			if ($debug) {
				$dbg++;
				$res[$dbg]="li_open_lev (".$id."): ".str_replace("\n", "", var_export($li_open_lev, true));
			}
			if ((count($li_open_lev) > 0) && ($lev <= $li_open_lev[$li_open_lev_ptr])) {
				$lev_code.=$close_li;
				unset($li_open_lev[$li_open_lev_ptr]);
				$li_open_lev_ptr =$li_open_lev_ptr-1;
			}


			if ($lev == 2) {
				if (!empty($lev_code)) {
					$lev_code.=$close_ul;
					$res[$key] =$lev_code;
					$lev_code ="";
				}

				$key =$id;
			}
			else if ($lev > 2) {


				$show_page =FALSE;
				if ((checkRoleForItem("page", $id)) && ($row["publish"] == 1) && ($row["show_in_menu"] == 1)) {
					$show_page =TRUE;
				}

				if ($lev > $old_lev) {

					if ((!$skip) && ($show_page)) {
						$lev_code.=($lev == 3 ? $open_first_ul : $open_ul).($debug ? ".".$lev."." : "");
					}
				}

				if ((!$skip) && (!$show_page)) {
					$skip =TRUE;
					$skip_from =$lev;
					if ($debug) {
						$dbg++;
						$res[$dbg]="Skip START: ".$lev." (area: ".$id.")";
					}
				}


				if ($lev < $old_lev) {

					if ((!$skip) && ($show_page)) {
						$lev_code.=$close_ul;
					}

					if (($skip) && ($lev <= $skip_from)) {
						$skip =FALSE;
						$skip_from =0;
						if ($debug) {
							$dbg++;
							$res[$dbg]="Skip STOP: ".$lev." (area: ".$id.")";
						}
					}

					if ((count($li_open_lev) > 0) && ($debug)) {
						$dbg++;
						$res[$dbg]="li_open_lev (".$id."): ".str_replace("\n", "", var_export($li_open_lev, true));
					}
					if (count($li_open_lev) > 0) {
						$lev_code.=str_repeat($close_li, count($li_open_lev));
						$li_open_lev =array();
						$li_open_lev_ptr =0;
					}
				}


				if ((!$skip) && ($show_page)) {

					$lev_code.=$open_li;
					$lev_code.='<a class="arrow_left" href="">';
					$lev_code.=$row["title"]."</a>";
					$lev_code.=($debug ? " (".$id.")" : "");
					//$lev_code.=$close_li;
					$li_open_lev_ptr++;
					$li_open_lev[$li_open_lev_ptr]=$lev;
				}
			}

			$old_lev =$lev;
		}

		if (!empty($lev_code)) {
			$lev_code.=$close_li.$close_ul;
			$res[$key] =$lev_code;
			$lev_code ="";
		}
	}


	return $res;
}


function getMrOpenLink($id_area, $title, $mr_title, $class) {

	if ($mr_title != "") {
		$file_title=format_mod_rewrite_title($mr_title);
	}
	else {
		$file_title=format_mod_rewrite_title($title);
	}
	$res="<a ".$class." href=\"page/".$id_area."/".$file_title.".html\">";

	return $res;
}


// -------------------------- Header/Meta information output -----------------------------

/**
 * function getCmsMeta
 * output the meta information for the selected page according to the id of the selected
 * area. It also checks for orphans parent blocks (pb) id and when they are found a
 * NO INDEX tag is added to prevent search engine indexing an empty page
 *
 * @return array with the meta information that has to be displayed in the page header
 * @author Giovanni Derks
 **/
function getCmsMeta() {

	$res =array();
	$header_meta="";

	// Looking for orphans blocks and prevent them to be indexed

	$meta_noindex_page ="<meta name=\"robots\" content=\"noindex\" />\n"; //, nofollow
	$meta_noindex_page.="<meta name=\"robots\" content=\"noarchive\" />\n";
	if ((isset($GLOBALS)) && ((int)$GLOBALS["pb"] > 0)) {
		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_area_block WHERE idBlock='".(int)$GLOBALS["pb"]."'";
		$q=mysql_query($qtxt);

		if ($q) {
			if (mysql_num_rows($q) <= 0) {
				$header_meta.=$meta_noindex_page;
				return "";
			}
		}

	}

	// ---------------------------------------------------------

	$idArea=getIdArea();

	$fields="browser_title, keyword, sitedesc";
	$qtxt="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_area WHERE idArea='$idArea'";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);

		$browser_title=$row["browser_title"];
		$keyword=$row["keyword"];
		$sitedesc=$row["sitedesc"];
	}
	else // Query failed, aborting..
		return "";
/*
	$load_default=0;
	foreach ($row as $key=>$val) {
		if ($val == "") $load_default=1;
	}

	if ($load_default)
*/	
//	{
	$lang=get_area_lang($idArea);
	if(isset($_GET['mn']))
	switch ($_GET['mn']) {
		case 'docs': {
			switch ($_GET['op']) {
			case 'showdoc' :
				$qdocs='SELECT ct.title AS folder_title, ci.title AS doc_title, ci.keywords, ci.sdesc, ci.ldesc, c.publish_date, c.author '.
					'FROM '.$GLOBALS["prefix_cms"].'_docs c, '
						.$GLOBALS["prefix_cms"].'_docs_info ci, '
						.$GLOBALS["prefix_cms"].'_docs_titles ct '.
						'WHERE c.idFolder = ct.iddir AND ci.idd = c.idDocs '.
						'AND c.idDocs='.$_GET['id'].' AND c.idFolder='.$_GET['folder'].' '.
						'AND c.publish=1 AND ci.lang="'.$lang.'" AND ct.lang="'.$lang.'"';
				$qd=mysql_query($qdocs);
				if (($qd) && (mysql_num_rows($qd) > 0))
				{
					$doc=mysql_fetch_array($qd);
					$browser_title.=' - '.$doc["folder_title"].' - '.$doc["doc_title"];
					if ($doc["keywords"]>' ') $keyword.=', '.$doc["keywords"];
//					$sitedesc.=' '.$doc["sdesc"];
					$sitedesc.=' - '.$doc["folder_title"].' - '.$doc["sdesc"];
					
				}
				break;
			case 'docs':
				$qdocs='SELECT title FROM '.$GLOBALS["prefix_cms"].'_docs_titles '.
					'WHERE iddir='.$_GET['folder'].' AND lang="'.$lang.'"';
				$qd=mysql_query($qdocs);
				if (($qd) && (mysql_num_rows($qd) > 0)) {
					$doc=mysql_fetch_array($qd);
					$browser_title.=' - '.$doc["title"];
				}
				break;
			}
		};break;
		case 'news': {
			$qnews='SELECT title, short_desc, author FROM '.
				$GLOBALS["prefix_cms"].'_news WHERE idNews= '.$_GET['id'];
			$qn=mysql_query($qnews);
			if (($qn) && (mysql_num_rows($qn) > 0)) {
				$news=mysql_fetch_array($qn);
				$browser_title.=' - '.strip_tags($news["title"]);
				$sitedesc.=' '.$news["short_desc"];
				$author=$news['author'];
			}
		};break;
	}
	
	if (isset($author) && $author > ' ')     {
		$header_meta.="<meta name=\"author\" content=\"".preg_replace("(\n|\r)", "",stripslashes($author))."\" />\n";
	}
//}

	$keyword=preg_replace("(\n|\r)", "", $keyword);
	$sitedesc=preg_replace("(\n|\r)", "", $sitedesc);

	$res["title_page"]=stripslashes($browser_title);

	$header_meta.="<meta name=\"keywords\" content=\"".strip_tags(stripslashes($keyword))."\" />\n";
	$header_meta.="<meta name=\"description\" content=\"".strip_tags(stripslashes($sitedesc))."\" />\n";
	$res["header_meta"]=$header_meta;

	return $res;
}


function setPageTemplate() {
	$area_id=getIdArea();

	$key=array("page_template", $area_id, "name");

	if (!getItemValue($key)) {
		$fields="template";
		$qtxt="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_area WHERE idArea='".$area_id."'";
		$q=mysql_query($qtxt);

		$row=array();
		$template="";
		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$template=$row["template"];
		}

		setItemValue($key, $template);
	}
	else
		$template=getItemValue($key);

	setTemplate($template);
}


function get_default_meta($lang) {

	$key=array("cms_default", $lang, "isset");

	if (!getItemValue($key)) {
		$fields="browser_title, keyword, sitedesc";
		$qtxt="SELECT $fields FROM ".$GLOBALS["prefix_cms"]."_area WHERE title='$lang' AND langdef='1'";
		$q=mysql_query($qtxt);

		$row=array();
		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
		}

		setItemValue(array("cms_default", $lang, "isset"), 1);
		setItemValue(array("cms_default", $lang, "browser_title"), $row["browser_title"]);
		setItemValue(array("cms_default", $lang, "keyword"), $row["keyword"]);
		setItemValue(array("cms_default", $lang, "sitedesc"), $row["sitedesc"]);

		return $row;
	}
	else {
		$res["browser_title"]=getItemValue(array("cms_default", $lang, "browser_title"));
		$res["keyword"]=getItemValue(array("cms_default", $lang, "keyword"));
		$res["sitedesc"]=getItemValue(array("cms_default", $lang, "sitedesc"));

		return $res;
	}

}



/**
 * @param string	$text		The title of the area
 * @param string	$image		not used
 * @param string	$alt_image	not used
 *
 * @return string 	the code for a graceful title area
 */
function getCmsTitleArea($text, $image = '', $alt_image = '') {

	$image="not used!";
	$alt_image="not used!";

	$is_first = true;
	if(!is_array($text))
		$text = array($text);

	$html = '<div class="area_block">'."\n";

	foreach($text as $link => $title) {

		if($is_first) {

			$is_first = false;

			// Area title

			$GLOBALS['page']->add('<li><a href="#main_area_title">'.def('_JUMP_TO', 'standard').' '.$title.'</a></li>', 'blind_navigation');

			// Init navigation
			if(count($text) > 1) {
				$html .= '<ul class="navigation">';
				if(!is_int($link)) {
					$html .= '<li><a href="'.$link.'">'.def('_START_PAGE', 'standard').' '.strtolower($title).'</a></li>';
				} else $html .= '<li>'.def('_START_PAGE', 'standard').' '.strtolower($title).'</li>';
			}
		} else {

			if(is_int($link)) $html .= '<li> &gt; '.$title.'</li>';
			else $html .= ' <li> &gt; <a href="'.$link.'">'.$title.'</a></li>';
		}
	}
	if(count($text) > 1) $html .= '</ul>'."\n";
	$html .= '</div>'."\n";


	if (count($text) <= 1)
		$html="";

	return $html;
}


function setPageLanguage($check_language=TRUE) {
	$area_id=getIdArea();

	$key=array("page_language", $area_id, "name");

	if (!getItemValue($key)) {
		$language=get_area_lang($area_id, TRUE);
		setItemValue($key, $language);
	}
	else
		$language=getItemValue($key);


	setLanguage($language);
	if ($check_language)
		checkCmsLanguage();
}


function checkCmsLanguage() {

	if (!isset($_SESSION["cms_lang_check"])) {

		$cms_lang=get_lang_list();

		if ((is_array($cms_lang)) && (count($cms_lang) > 0) && (!in_array(getLanguage(), $cms_lang))) {

			setLanguage($cms_lang[0]);

		}
	}

	$_SESSION["cms_lang_check"]=1;
}


function getAdminQuickLink() {
	$res="";

	$level=$GLOBALS["current_user"]->getUserLevelId();
	$is_admin=($level == ADMIN_GROUP_ADMIN ? TRUE : FALSE);
	$is_god_admin=($level == ADMIN_GROUP_GODADMIN ? TRUE : FALSE);
	if (($is_admin) || ($is_god_admin)) {

		$title=def('_GOTO_ADMIN', 'standard');
		$img="<img src=\"".getPathImage()."standard/goto_admin.gif\" alt=\"".$title."\" title=\"".$title."\" />";
		$res.="<a href=\"".$GLOBALS["where_framework_relative"]."/\">".$img."</a>";

	}

	return $res;
}


function getLogoutQuickLink() {
	$res="";

	if (!$GLOBALS["current_user"]->isAnonymous()) {

		$title=def('_LOG_LOGOUT', 'login');
		$img="<img src=\"".getPathImage()."standard/logout.gif\" alt=\"".$title."\" title=\"".$title."\" />";
		$res.="<a href=\"index.php?action=logout\">".$img."</a>";

	}

	return $res;
}


// ---------------------------------------------------------------------------------------


?>
