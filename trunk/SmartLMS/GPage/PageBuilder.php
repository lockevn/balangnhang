<?php

class PageBuilder
{
	const PAGELET_PREFIX = 'pagelet_';
	/**
	*@desc assocarray (key = pagename (mod name), value = assocarray (key = 'zonename', 'pageletname1,pageletname2'))
	*/
	public static $PageMap = array(
		'SmartMoney/search' => array(
			'ZONE_MainContent' => array('SmartMoney/Pagelet/search_prepaidcard', '',''),                
			'ZONE_Right' => array('', '')
		),		
		'doceboLms/search' => array(
			'ZONE_MainContent' => array('doceboLms/Pagelet/search', '',''),                
			'ZONE_Right' => array('', '')
		)
	);


	public static $AllowedCustomModule = array(
	);
	
	public static $PageLayoutMap = array(		
		'pagelet_for_mashup' => 'pagelet_for_mashup'
	); 



	public static function Render($mod, $tpl)
	{
		$moduleConfig = PageBuilder::$PageMap[$mod];
		foreach ((array)$moduleConfig as $zonename => $arrPagelet)
		{
			$zonecontent = '';
			foreach ((array)$arrPagelet as $pagelet)
			{
				if($pagelet)
				{
					require_once(ABSPATH."$pagelet.php");
					$zonecontent .= ${PageBuilder::PAGELET_PREFIX.$pagelet};
				}
			}
			$tpl->assign($zonename, $zonecontent);
		}

		return true;
	}

}

?>