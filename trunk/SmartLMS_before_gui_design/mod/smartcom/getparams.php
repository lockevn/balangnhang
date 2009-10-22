<?php
	defined('smartcom_INCLUDE_TEST') OR die('not allowed');

	$currenttab  = optional_param('tab',smartcom_TAB1,PARAM_INT);
	$currentpage = optional_param('pag',1              ,PARAM_INT);
	switch ($currenttab) {
	case smartcom_TAB1:
	case smartcom_TAB2:
		switch ($currentpage) {
		case smartcom_TAB2_PAGE1:
			$currentpagename = smartcom_TAB2_PAGE1NAME;
			break;
		case smartcom_TAB2_PAGE2:
			$currentpagename = smartcom_TAB2_PAGE2NAME;
			break;
		case smartcom_TAB2_PAGE3:
			$currentpagename = smartcom_TAB2_PAGE3NAME;
			break;
		case smartcom_TAB2_PAGE4:
			$currentpagename = smartcom_TAB2_PAGE4NAME;
			break;
		case smartcom_TAB2_PAGE5:
			$currentpagename = smartcom_TAB2_PAGE5NAME;
			break;
		default:
			echo 'I am at the row '.__LINE__.' of the file '.__FILE__.'<br />';
			echo 'I have $currentpage = '.$currentpage.'<br />';
			echo 'But the right "case" is missing<br />';
		}
		break;
	case smartcom_TAB3:
		switch ($currentpage) {
		case smartcom_TAB3_PAGE1:
			$currentpagename = smartcom_TAB3_PAGE1NAME;
			break;
		case smartcom_TAB3_PAGE2:
			$currentpagename = smartcom_TAB3_PAGE2NAME;
			break;
		default:
			echo 'I am at the row '.__LINE__.' of the file '.__FILE__.'<br />';
			echo 'I have $currentpage = '.$currentpage.'<br />';
			echo 'But the right "case" is missing<br />';
		}
		break;
	default:
		echo 'I am at the row '.__LINE__.' of the file '.__FILE__.'<br />';
		echo 'I have $currenttab = '.$currenttab.'<br />';
		echo 'But the right "case" is missing<br />';
	}
	
?>