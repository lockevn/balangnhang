<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Emanuele Sandri, Fabio Pirovano, Giovanni Derks */
/*                      http://www.docebocms.com                         */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS['where_cms'].'/lib/lib.cms_common.php');


$GLOBALS['page']->add('<link href="'.$GLOBALS['where_cms_relative'].
 '/templates/'.getTemplate().'/style/style-admin.css" rel="stylesheet" type="text/css" />', 'page_head');


/**
 * renderning function
 **/
function loadAdminArea( $idArea) {


	doAdminSubdivision( $idArea, 0, 0 );
}

function doAdminSubdivision($idArea, $idSubdivision, $rip) {
	//EFFECTS : create the page required

	$res=false;
	$out=& $GLOBALS['page'];

	//needest limit
	if($rip > '10') return;
	if($rip == '') $rip = 0;

	//finding area tree
	$reSubdivision = mysql_query("
	SELECT idSubdivision, areaWidth, areaType, margin
	FROM ".$GLOBALS["prefix_cms"]."_area_subdivision
	WHERE idParentSub = '$idSubdivision' AND idArea='$idArea'
	ORDER BY sequence ASC");

	if (($reSubdivision) && (mysql_num_rows($reSubdivision) > 0)) {
		$res=true;

		//subdive page and display content
		while(list($idS, $width, $type, $margin) = mysql_fetch_row($reSubdivision)) {
			$out->add('<div class="admin_contentBox'.$width.'">');

			if($type == 'content') {
				$has_child=doAdminSubdivision($idArea, $idS, ++$rip);
				$out->add('<div class="no_float"></div>');
			}
			else {
				$has_child=FALSE;
			}
			listAdminBlocks($idS, $idArea, $has_child);

			$out->add('</div>'."\n\n");
		}
	}

	return $res;
}

function listAdminBlocks($idSubdivision, $idArea, $has_child=FALSE) {

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_manpage', 'cms');

	$reBlock = mysql_query("
	SELECT t1.idBlock, t1.block_name, t1.title, t1.function, t2.label, t1.sequence
	FROM ".$GLOBALS["prefix_cms"]."_area_block as t1, ".$GLOBALS["prefix_cms"]."_blocktype as t2
	WHERE t1.idSubdivision = '$idSubdivision' AND t2.name=t1.block_name
	ORDER BY sequence ASC");

	$tot=mysql_num_rows($reBlock);

	while(list($idBlock, $block_name, $title, $function, $block_label, $seq) = mysql_fetch_row($reBlock)) {
		$out->add('<div class="contentblock_1">');
		//displayBlock($idBlock, $block_name, $title, $function);

		$block_label=$lang->def($block_label);

		$out->add('<b>'.$lang->def("_TITLEBLOCK").' :</b> '.$title.'<br /><br />');
		$out->add('<b>'.$lang->def("_TYPEBLOCK").' :</b> '.$block_label.'<br /><br />');

		//print_r(loadBlockOption($idBlock));

		//mod
		$out->add('<div class="modBlock">');
			/*.'<a href="index.php?modname=manpage&amp;op=modblock&amp;idSubdivision='
			.$idSubdivision.'&amp;idBlock='.$idBlock.'">'*/
		if ($seq < $tot-1)
			$out->add('<a href="index.php?modname=manpage&amp;op=pagemodblock&amp;idArea='.$idArea.'&amp;act_op=moveblkdown'
					.'&amp;block_id='.$idBlock.'&amp;sub_id='.$idSubdivision.'">'
					.'<img src="'.getPathImage().'standard/down.gif"  alt="'.$lang->def("_MOD").'" /></a>&nbsp;');
		else
			$out->add('<img class="fakebtn" src="'.getPathImage().'standard/pixel.gif"  alt="" />&nbsp;');

		if ($seq > 0)
			$out->add('<a href="index.php?modname=manpage&amp;op=pagemodblock&amp;idArea='.$idArea.'&amp;act_op=moveblkup'
					.'&amp;block_id='.$idBlock.'&amp;sub_id='.$idSubdivision.'">'
					.'<img src="'.getPathImage().'standard/up.gif"  alt="'.$lang->def("_MOD").'" /></a>&nbsp;');
		else
			$out->add('<img class="fakebtn" src="'.getPathImage().'standard/pixel.gif"  alt="" />&nbsp;');

		$out->add('<a href="index.php?modname=manpage&amp;op=modblock&amp;write=1'
			.'&amp;block_id='.$idBlock.'&amp;sub_id='.$idSubdivision.'">'
			.'<img src="'.getPathImage().'standard/mod.gif"  alt="'.$lang->def("_MOD").'" /></a>&nbsp;');

		if (checkPerm('del', true)) {
			$out->add('<a href="index.php?modname=manpage&amp;op=delblock&amp;sub_id='
			.$idSubdivision.'&amp;block_id='.$idBlock.'">'
			.'<img src="'.getPathImage().'standard/rem.gif"  alt="'.$lang->def("_DEL").'" /></a>&nbsp;');
		}
		else
			$out->add('<img class="fakebtn" src="'.getPathImage().'standard/pixel.gif"  alt="" />&nbsp;');

		$out->add('</div>');

		$out->add('</div>');
	}

	if ((checkPerm('del', true)) && (!$has_child)) {
		$out->add('<div class="addBlock">'
			.'<img src="'.getPathImage().'standard/add.gif"  alt="'.$lang->def("_ADD").'" />&nbsp;'
			.'<a href="index.php?modname=manpage&amp;op=addblock&amp;sub_id='.$idSubdivision.'">'.$lang->def("_ADD").'</a>'
			.'</div>');
	}
}

/**
 * create an istance of the specified block
 * @param string $name_block is the name of the block
 * @param int $idS is the target subdivision
 * @param array $back_url
 *		'address' => current address
 *		'backurl' => return address
 *		'param' => array( 'param name' => 'param value' , ... )
 **/
/*function createBlockIstance( $name_block, $idBlock, $idS, $back_url ) {


	$query = "SELECT className, classFile FROM ".$GLOBALS["prefix_cms"]."_blocktype WHERE name='$name_block'";
	$rs = mysql_query( $query );
	list( $className, $fileName ) = mysql_fetch_row( $rs );
	require_once($GLOBALS['where_cms'].'/lib/page_block/block.php');
	require_once($GLOBALS['where_cms'].'/lib/page_block/'.$fileName);

	$block = eval( "return new $className ( '$idBlock', '$idS', \$back_url );" );
	return $block;
}*/

/**
 * create a block in the subdivision specified by
 * @param $idS as target subdivision
 * @param $b_name as block name
 * @param $title as title
 * @param $function as function
 **/
/*function createNewBlock( $idS, $b_name, $title, $function) {


	//find sequence
	list($seq) =  mysql_fetch_row(mysql_query("
	SELECT MAX(sequence) + 1
	FROM ".$GLOBALS["prefix_cms"]."_area_block
	WHERE idSubdivision = '$idS'"));
	//insert block
	$re = mysql_query("
	INSERT INTO ".$GLOBALS["prefix_cms"]."_area_block
	SET idSubdivision = '$idS',
		block_name = '$b_name',
		title = '$title',
		function = '$function',
		sequence = '$seq'");
	if(!$re) return false;
	list($idBlock) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	return $idBlock;
}*/


?>
