<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/Net/XHR.php");
require_once(ABSPATH."Lib/URLParamHelper.php");

require_once(ABSPATH."Lib/SmileyToImage.php");

require_once(ABSPATH."Business/Common.php");
require_once(ABSPATH."Business/Profile.php");
require_once(ABSPATH."Business/Group.php");


class Common
{

    public static function Paging($arrayData = array())
    {
        global $configs;
        
        $currentPage = Common::GetCurrentPageNumber();
        $nextPage = $currentPage + 1;
        $prevPage = $currentPage - 1;
        
        $uri = str_replace('/page/'.$currentPage,'',$_SERVER['REQUEST_URI']);

        if($uri == '/' || (strpos($uri,'index.php') && !$_GET['mod']))
        {
            $url = '/home';
        }
        else
        {
            $url = $uri;
        }

        if(is_array($arrayData) && count($arrayData) > 0 && count($arrayData) >= Config::ITEMPERPAGE)
        {
            $arr['next'] = '<a href = "'.$url.'/page/'.$nextPage.'" class = "paging">Trang sau</a>';
        }

        if($currentPage > 1)
        {
            $arr['prev'] = '<a href = "'.$url.'/page/'.$prevPage.'" class = "paging">Trang trước</a>';
        }

        return $arr;
    }

    public static function GetCurrentPageNumber()
    {
        $sCurrentPage = $_REQUEST['p'];
        if(empty($sCurrentPage) || $sCurrentPage == 0)   // still empty
        {
            return 1;
        }
        else
        {
            return $sCurrentPage;
        }
    }

    
    
    
    

    public static function GetCurrentUsername()
    {
        $user = GetParamSafe('user');
        $user = empty($user) ? $_COOKIE['username'] : $user;
        return $user;
    }

    public static function GetCurrentProfileInfo()
    {
        if(empty(RequestShare::$CACHE['ProfileInfo'] ))
        {
            $pu = Common::GetCurrentUsername();
            RequestShare::$CACHE['ProfileInfo'] = Profile::GetProfileInfo(null, $pu);
        }

        return RequestShare::$CACHE['ProfileInfo'];
    }



    public static function GetCurrentGroupcode()
    {
        $gcode = $_GET['gcode'];
        return $gcode;
    }

    public static function GetCurrentGroupID()
    {
        $gid = $_GET['gid'];
        return $gid;
    }


    public static function GetCurrentGroupInfo()
    {
        if(empty(RequestShare::$CACHE['GroupInfo'] ))
        {
            $gid = Common::GetCurrentGroupID();
            $gcode = Common::GetCurrentGroupcode();
            RequestShare::$CACHE['GroupInfo'] = Group::GetGroupInfo($gid, $gcode);
        }

        return RequestShare::$CACHE['GroupInfo'];
    }






    




    /**
    * @author LockeVN
    * @desc Take data (from cURL >> XML2Array. Modify the arrayData (by ref)
    * @param array after output from XML2Array.
    * @param string entityName to take from arrayData
    * @return the modified arrayData
    */
    public static function MakeDataArrayFromXMLArray($arrayData, $entityName)
    {
        $arrRet = array();
        if(empty($arrayData))
        {
            return $arrRet;
        }

        if(is_array($arrayData['qblog']['results'][0]))
        {
            if(array_key_exists($entityName, $arrayData['qblog']['results'][0]))
            {
                $arrayData = $arrayData['qblog']['results'][0][$entityName];
                $size = sizeof($arrayData);
            }
            else
            {
                return $arrRet;
            }
        }
        else
        {
            return $arrRet;
        }


        /********* PROCESS EACH TYPE OF DATA ENTITY ************/
        if($entityName == 'm')
        {
            //foreach ($arrayData as &$item)
//            {
//                $item['dt'] = LIB::formatDate($item['dt']);
//            }
//            unset($item);
        }
        elseif($entityName == 'p')
        {
            //foreach ($arrayData as &$item)
//            {
//                $item['dt'] = LIB::formatDate($item['dt']);
//            }
//            unset($item);
        }

        return $arrayData;
    }





    function countForUser($type, $idUser)
    {
        global $configs;

        $url = Config::API_URL.'//public/aggregate/count.php';
        $url .= '?format=xml&e='.$type.'&id='.$idUser;

        //echo $url . "<br>";

        $arrayData = XHR::execCURL_ReturnXML2Array($url);

        if(is_array($arrayData['qblog']['results']))
        {
            $arrayData = $arrayData['qblog']['results'][0]['result'][0];
        }

        return $arrayData['info'];
    }





    function convertTime($arr, $bShowSmiley = true)
    {
        for($i = 0; $i < count($arr); $i ++)
        {
            //calculate time
            //echo $arr[$i]['dt'].' - '.date('Y-m-d H:i:s').'<br>';
            $arr[$i]['dt'] = Common::strTextTime($arr[$i]['dt']);

            $c = $arr[$i]['c'];

            $c = htmlentities($c, ENT_COMPAT, 'UTF-8');
            //$c = preg_replace('/(\s+|^)(@)+(\w+)/i','$1$2<a href="/profile/$3">$3</a>', $c);
            $c = preg_replace('/(\s+|^)(@)(\w+)/i','$1$2<a href="/profile/$3">$3</a>', $c);

            /*create link for @@gcode*/
            $c = preg_replace('/(\s+|^)(@@)(\w+)/i','$1$2<a href="/group/$3">$3</a>', $c);

            $pattern = '/(https?|ftp):\/\/([-A-Z0-9+&@#%?=~_|!:,.;\/]*[-A-Z0-9+&@#%=~_|\/])/i';
            $c = preg_replace($pattern, ' <a href="\0">$2</a> ', $c);

            //TODO: replace @@gcode thanh link toi group
            //$arr[$i]['c'] = stripslashes($c);
            if($bShowSmiley)
            {
                $arr[$i]['c'] = SmileyToImage::Convert($c);
            }
            else
            {
                $arr[$i]['c'] = $c;
            }
        }

        return $arr;
    }

    public static final function strTextTime($date)
    {
        $time = Common::get_time_difference($date,date('Y-m-d H:i:s'));
        $strTime = 'khoảng ';

        if($time['days'] > 0)
        {
            $strTime .= $time['days'] . ' ngày ';
        }
        else
        {
            if($time['hours'] > 0)
            {
                $strTime .= $time['hours'] . ' giờ ';
            }
            else
            {
                if($time['minutes'] > 0)
                {
                    $strTime .= $time['minutes'] . ' phút ';
                }
                else
                {
                    if($time['seconds'] < 5)
                    {
                        $strTime .= ' ít hơn 5 giây ';
                    }
                    else
                    {
                        $strTime .= $time['seconds'] . ' giây ';
                    }
                }
            }
        }

        $strTime .= 'trước';
        $textTime = $strTime;

        return $textTime;
    }

    /**
     * Function to calculate date or time difference.
     *
     * Function to calculate date or time difference. Returns an array or
     * false on error.
     *
     * @author       J de Silva                             <giddomains@gmail.com>
     * @copyright    Copyright &copy; 2005, J de Silva
     * @link         http://www.gidnetwork.com/b-16.html    Get the date / time difference with PHP
     * @param        string                                 $start
     * @param        string                                 $end
     * @return       array
     */
    function get_time_difference( $start, $end )
    {
        $uts['start']      =    strtotime( $start );
        $uts['end']        =    strtotime( $end );
        if( $uts['start']!==-1 && $uts['end']!==-1 )
        {
            if( $uts['end'] >= $uts['start'] )
            {
                $diff    =    $uts['end'] - $uts['start'];
                if( $days=intval((floor($diff/86400))) )
                    $diff = $diff % 86400;
                if( $hours=intval((floor($diff/3600))) )
                    $diff = $diff % 3600;
                if( $minutes=intval((floor($diff/60))) )
                    $diff = $diff % 60;
                $diff    =    intval( $diff );
                return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
            }
            else
            {
                trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
            }
        }
        else
        {
            trigger_error( "Invalid date/time data detected", E_USER_WARNING );
        }
        return( false );
    }
}

?>
