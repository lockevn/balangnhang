<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/SmileyToImage.php");
require_once(ABSPATH."Lib/Net/XHR.php");
require_once(ABSPATH."Lib/URLParamHelper.php");

require_once(ABSPATH."Lib/External/Savant3.php");

require_once(ABSPATH."Business/Common.php");
require_once(ABSPATH."Business/Security.php");


class Community
{
    public static function GetMostActiveUser($ipp = 9)
    {   
        //list friend of user
        $url = Config::API_URL."/public/activity/user.most.active.read.php?format=xml&ipp=$ipp";

        $arrayData = XHR::execCURL_ReturnXML2Array($url);
        $arrayData = Common::MakeDataArrayFromXMLArray($arrayData, 'p');
        $arrayData = (array)$arrayData;
        foreach ($arrayData as &$value) {
            $value['id'] = $value['fromid'];
            $value['u'] = $value['fromu'];
        }
        unset($value);
        return $arrayData;
    }
    
    public static function GetHighLightUser($ipp = 10)
    {
        //list friend of user
        $url = Config::API_URL."/public/activity/user.most.power.read.php?format=xml&ipp=$ipp";

        $arrayData = XHR::execCURL_ReturnXML2Array($url);
        $arrayData = Common::MakeDataArrayFromXMLArray($arrayData, 'p');
        $arrayData = (array)$arrayData;
        foreach ($arrayData as &$value) {
            $value['id'] = $value['fromid'];
            $value['u'] = $value['fromu'];
        }
        unset($value);
        return $arrayData;
    }
}

?>