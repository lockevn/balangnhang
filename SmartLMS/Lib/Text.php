<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
class Text
{
    public static $WelcomeMessageDictionary = array(
        'Chào %s, bạn đang làm gì vậy?',
        'Này %s, bạn đang làm gì thế?',
        '%s, chúc vui vẻ, bạn đang làm gì nhỉ?',
        'Xin chào %s, bạn đang làm gì đấy?',
        '%s ơi, bạn đang làm gì thía?'
    );
    
    



    /**
    *@desc Use 301 header to redirect, and die()
    */
    public static function GetRandomWelcomeMessage()
    {
        srand((float) microtime() * 10000000);
        $rand_key = array_rand(Text::$WelcomeMessageDictionary, 1);
        $welcomemessage = Text::$WelcomeMessageDictionary[$rand_key];
        return $welcomemessage;
    }
    
    
    

    public static function AddQuoteToListString($sourcestring, $delimiter=',')
    {
        return Text::AddPrefixSuffixToListString($sourcestring, $delimiter, '\'', '\'');

    }


    /**
     * @author LockeVN
     * @desc
     * @param string source string, in format a,b,c where delimiter = ,
     * @param string delimiter, what seperate element in list string
     * @param string what append to begin of each element
     * @param string what append to end of each element
     * @return string result string, in format 'a','b','c', where quotechar = '
     */
    public static function AddPrefixSuffixToListString($sourcestring, $delimiter=',', $prefix = '', $suffix = '')
    {
        $arrtemp = explode($delimiter, $sourcestring);
        foreach($arrtemp as &$item)
        {
            $item = $prefix .$item. $suffix;
        }
        unset($item);

        $resultstring = implode($delimiter,$arrtemp);
        return $resultstring;
    }

    /**
     * @author LockeVN
     * @desc
     * @param
     * @return string result string, Eg: <![CDATA[SourcestringIsHere]]>
     */
    public static function AddXMLCDataWrapToString($sourcestring)
    {
        $resultstring = '<![CDATA[' .$sourcestring. ']]>';
        return $resultstring;
    }

    /**
     * @author LockeVN
     * @desc
     * @param string source string, in format a,b,c where delimiter = ,
     * @param string delimiter, what seperate element in list string, Eg: , (comma)
     * @return string result string, Eg: <![CDATA[Sourcestring1IsHere]]>,<![CDATA[Sourcestring2IsHere]]>
     */
    public static function AddXMLCDataWrapToListString($sourcestring, $delimiter=',')
    {
        return Text::AddPrefixSuffixToListString($sourcestring, $delimiter, '<![CDATA[', ']]>');
    }


    /**
     * check if a "'" delimitered string after being exploded contains a string defined by $value
     *
     * @param String $str "'" delimitered string
     * @param String $value sub string
     */
    public static function IsStringContainValue($str, $value)
    {
        if(empty($str) || empty($value))
        {
            return false;
        }

        $tmpArr = explode(',', $str);
        if(empty($tmpArr))
        {
            return false;
        }

        foreach ($tmpArr as $tmpStr)
        {
            if($tmpStr == $value)
            {
                return true;
            }
        }
        return false;
    }

    public static function checkNumericString($str)
    {
        if(empty($str))
        {
            return false;
        }

        if(!is_numeric($str))
        {
            return false;
        }
        return true;
    }

    public static function explodeStringIntoAssArr($delimiter, $str,  $pairDelimiter = '=') {
        if(empty($str) || empty($delimiter))
        return false;
        $tmpArr = explode($delimiter, $str);
        $retArr = array();
        foreach ($tmpArr as $pair) {
            $tmpPairArr = explode($pairDelimiter, $pair);
            if(!empty($tmpPairArr)) {
                $retArr[$tmpPairArr[0]] = ($tmpPairArr[1] == '') ? '' : $tmpPairArr[1];
            }
        }
        return $retArr;
    }


    private static function genChar()
    {
        $possible = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        return $char;
    }

    public static function generateGUID()
    {
        $GUID = Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar()."-";
        $GUID = $GUID .Text::genChar().Text::genChar().Text::genChar().Text::genChar()."-";
        $GUID = $GUID .Text::genChar().Text::genChar().Text::genChar().Text::genChar()."-";
        $GUID = $GUID .Text::genChar().Text::genChar().Text::genChar().Text::genChar()."-";
        $GUID = $GUID .Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar().Text::genChar();
        return $GUID;
    }

    public static function generateRandomStr($length = 4)
    {
        for($i=0; $i<$length; $i++)
        {
            $sRandom .= Text::genChar();
        }
        return $sRandom;
    }


     /**
    *@desc
    * @author CongTQ
    */
    public static function VN_ConvertVNCodePageCompoundComposite($str, $destinationconvertcodepage =  TEXT_DISPLAY_TYPE::COMPOUND)
    {
        $unicodecompound  =   array("à", "á", "ả", "ã", "ạ",
                                    "ằ", "ắ", "ẳ", "ẵ", "ặ",
                                    "ầ", "ấ", "ẩ", "ẫ", "ậ",
                                    "è", "é", "ẻ", "ẽ", "ẹ",
                                    "ề", "ế", "ể", "ễ", "ệ",
                                    "ì", "í", "ỉ", "ĩ", "ị",
                                    "ò", "ó", "ỏ", "õ", "ọ",
                                    "ồ", "ố", "ổ", "ỗ", "ộ",
                                    "ờ", "ớ", "ở", "ỡ", "ợ",
                                    "ù", "ú", "ủ", "ũ", "ụ",
                                    "ừ", "ứ", "ử", "ữ", "ự",
                                    "ỳ", "ý", "ỷ", "ỹ", "ỵ",
                            );

        $unicodecomposite =   array("à", "á", "ả", "ã", "ạ",
                                    "ằ", "ắ", "ẳ", "ẵ", "ặ",
                                    "ầ", "ấ", "ẩ", "ẫ", "ậ",
                                    "è", "é", "ẻ", "ẽ", "ẹ",
                                    "ề", "ế", "ể", "ễ", "ệ",
                                    "ì", "í", "ỉ", "ĩ", "ị",
                                    "ò", "ó", "ỏ", "õ", "ọ",
                                    "ồ", "ố", "ổ", "ỗ", "ộ",
                                    "ờ", "ớ", "ở", "ỡ", "ợ",
                                    "ù", "ú", "ủ", "ũ", "ụ",
                                    "ừ", "ứ", "ử", "ữ", "ự",
                                    "ỳ", "ý", "ỷ", "ỹ", "ỵ",
                            );

        if($destinationconvertcodepage == TEXT_DISPLAY_TYPE::COMPOUND)
        {
            $out = str_replace($unicodecomposite, $unicodecompound, $str);
        }
        else if($destinationconvertcodepage == TEXT_DISPLAY_TYPE::COMPOSITE)
        {
            $out = str_replace($unicodecompound, $unicodecomposite, $str);
        }
        else if($destinationconvertcodepage == TEXT_DISPLAY_TYPE::NOVNMASK)
        {
            $out = Text::VN_RemoveVNMask($str);
        }

        return $out;
    }

    /**
    *@desc
    * @author CongTQ
    */
    public static function VN_RemoveVNMask($str)
    {
        // Unicode
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/",  'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str );
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/" , 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o' , $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str );
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/" , 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str );
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/" , 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E' , $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/",  'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str );
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/" , 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str );

        // Unicode Composite
        $str = preg_replace("/(̀|́|̣|̉|̃)/",  '', $str);

        return $str;
    }




    /**
    *@desc check if provided string is email
    */
    public static function checkEmail($str)
    {
        if($str == '') return false;

        if (!preg_match( "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $str)) {
            return false;
        }

        return true;
    }












    /**
    *@desc remove all space charactor from string
    */
    public static function removeSpace($str)
    {
        if(!empty($str))
        {
            $str = strtr($str, ' ', '');
            return $str;
        }
        return '';
    }

    /**
     * @author LockeVN
     *@desc remove control charector from string. We let some charactor remain like tab, newline.
     * WARNING: if you are developer and you want to modify the function, be careful of the order in replace string. See http://www.asciitable.com/ for ascii I (lockevn) use here.
     * @return string removed all noncharacter in input string.
     */
    public static function removeControlCharacter($str)
    {
        return strtr($str,
            "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x0C\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F"
            ,
            ' - .                        ');
    }

    /**
    *@desc
    * @return false if input string is not a valid username in TiuTit.com (length > 4, contain char and number only
    */
    public static function checkUsername($u)
    {
        $u = trim($u);
        if(empty($u))
        {
            return false;
        }

        if(preg_match("#^[a-z][\da-z_]{4,22}\$#i", $u) == 0)
        {
        //if($matchCount != 1 || $matchArr[0][0] != $u) {
            return false;
        }
        return true;
    }

    /**
    *@desc check to determine the provide string is Vietnamese phone number?
    */
    public static function checkMobileNumber($str)
    {
        $str = Text::removeSpace($str);
        if(empty($str))
        {
            return false;
        }
        if(strlen($str) < 7 && strlen($str) > 11)
        {
            return false;
        }
        if(!Text::checkNumericString($str) || substr($str, 0, 1) != '0')
        {
            return false;
        }
        return true;
    }

    /**
    * @author: original by Danhut, modified by LockeVN (more compact)
    *@desc try to compact datetime, fromusername, content of a post to SMS size. Remove VNMask.
    */
    public static final function SMS_FormatSendBackContent($p_dt, $p_fromu, $p_content)
    {
        if(empty($p_dt))
        {
            $datetime = '';
        }
        else
        {
            $datetime = date('dmy H:i', strtotime($p_dt)) . ' ';
        }

        $content = Text::VN_RemoveVNMask($p_content);

        if(empty($p_fromu))
        {
            $fromu = '';
        }
        else
        {
            $fromu = $p_fromu . ':';
        }

        $sendBackContent = $datetime . $fromu . $content;
        $sendBackContentLength = strlen($sendBackContent);
        if($sendBackContentLength > 160)
        {
            $exceededchars = $sendBackContentLength - 160;
            $fromu = substr($p_fromu, 0, strlen($p_fromu) - $exceededchars - 1) . '_:';
            $sendBackContent = $datetime . $fromu . $content;
        }
        return $sendBackContent;
    }



    /**
     * @author: copied here by LockeVN
     * @desc Parse CamelCase to Camel Case
     * @param string $string
     * @return string
     */
    public static final function ParseCamelCase($string)
    {
        // both pattern work. I choose the first (lockevn)
        return preg_replace('/(?<=[a-z])(?=[A-Z])/',' ',$string);
        // return preg_replace('/(?!^)[[:upper:]]/',' \0',$test);
    }


    /**
    * @author copied an modified by LockeVN
    * @desc Example: strtocamel('Str tO CAMEL'); This will output 'StrToCamel'.
    */
    public static final function ToCamelCase($string, $AlsoMakeStringToLower = false)
    {
        $str = explode(' ', $AlsoMakeStringToLower? strtolower($string) : $string);

        // To make the first letter lower case, change to $i = 1
        for($i = 0; $i < count($str); $i++)
        {
            $str[$i] = strtoupper(substr($str[$i], 0, 1)) . substr($str[$i], 1);
        }
        return implode('', $str);
    }

    public static final function GetFileExtension($filename)
    {
        if(empty($filename))
        {
            return '';
        }
        $pos = strrpos($filename, '.');
        if($pos === false)
        {
            return '';
        }
        return substr($filename, $pos + 1);
    }

}   // end classs
?>