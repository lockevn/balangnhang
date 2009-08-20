<?php

    defined('smartcom_INCLUDE_TEST') OR die('not allowed');

    $tabs = array();
    $row  = array();
    $inactive = array();
    $activated = array();

    $baseurl = $CFG->wwwroot.'/mod/smartcom/view.php?id='.$id.'&amp;tab=';

    // the main three tabs
    //==> first tab
    $row[] = new tabobject(smartcom_TAB1, $baseurl.smartcom_TAB1, get_string('firsttabname', 'smartcom'));

    //==> second tab
	$row[] = new tabobject(smartcom_TAB2, $baseurl.smartcom_TAB2, get_string('secondtabname', 'smartcom'));

    //==> third tab
    $row[] = new tabobject(smartcom_TAB3, $baseurl.smartcom_TAB3, get_string('thirdtabname', 'smartcom'));



    //==> tab definition
    $tabs[] = $row; //$tabs is an array of arrays

    $inactive[] = $currenttab;
    $activated[] = $currenttab;

    switch ($currenttab) {
    case smartcom_TAB1:
        break;
    case smartcom_TAB2:
        $inactive[] = $currentpagename;
        $activated[] = $currentpagename;

        $baseurl = $CFG->wwwroot.'/mod/smartcom/view.php?id='.$cm->id.'&amp;tab='.smartcom_TAB2.'&amp;pag=';

        $row  = array();
        $strlabel = get_string('tab2page1', 'smartcom');
        $row[] = new tabobject(smartcom_TAB2_PAGE1NAME, $baseurl.smartcom_TAB2_PAGE1, $strlabel);

        $strlabel = get_string('tab2page2', 'smartcom');
        $row[] = new tabobject(smartcom_TAB2_PAGE2NAME, $baseurl.smartcom_TAB2_PAGE2, $strlabel);

        $strlabel = get_string('tab2page3', 'smartcom');
        $row[] = new tabobject(smartcom_TAB2_PAGE3NAME, $baseurl.smartcom_TAB2_PAGE3, $strlabel);

        $strlabel = get_string('tab2page4', 'smartcom');
        $row[] = new tabobject(smartcom_TAB2_PAGE4NAME, $baseurl.smartcom_TAB2_PAGE4, $strlabel);

        $strlabel = get_string('tab2page5', 'smartcom');
        $row[] = new tabobject(smartcom_TAB2_PAGE5NAME, $baseurl.smartcom_TAB2_PAGE5, $strlabel);

        $tabs[] = $row;
        break;
    case smartcom_TAB3:
        $inactive[] = $currentpagename;
        $activated[] = $currentpagename;

        $baseurl = $CFG->wwwroot.'/mod/smartcom/view.php?id='.$cm->id.'&amp;tab='.smartcom_TAB3.'&amp;pag=';

        $row  = array();
		$strlabel = get_string('tab3page1', 'smartcom');
		$row[] = new tabobject(smartcom_TAB3_PAGE1NAME, $baseurl.smartcom_TAB3_PAGE1, $strlabel);

		$strlabel = get_string('tab3page2', 'smartcom');
		$row[] = new tabobject(smartcom_TAB3_PAGE2NAME, $baseurl.smartcom_TAB3_PAGE2, $strlabel);

        $tabs[] = $row;
        break;
    default:
        echo 'I am at the row '.__LINE__.' of the file '.__FILE__.'<br />';
        echo 'I have $currenttab = '.$currenttab.'<br />';
        echo 'But the right "case" is missing<br />';
    }
/*print_object($tabs);
print_object($inactive);
print_object($activated);*/

    print_tabs($tabs, $currenttab, $inactive, $activated);
?>