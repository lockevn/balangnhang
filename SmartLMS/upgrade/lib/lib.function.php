<?php

class DoceboUpgradeGui {
	
	function getLanguage() {

        if (isset($_POST["sel_lang"])) {
            $GLOBALS["lang"]=$_POST["sel_lang"];
            $_SESSION["default_language"]=$_POST["sel_lang"];
        }
        else if (isset($_SESSION["default_language"]))
            $GLOBALS["lang"]=$_SESSION["default_language"];
        else
            $GLOBALS["lang"]="english";

        return $GLOBALS["lang"];
    }

    function includeLang($language = false) {

        if ($language === FALSE)
            $language=DoceboUpgradeGui::getLanguage();

        require_once($GLOBALS['where_upgrade'].'/lang/'.$language.'.php');
    }

    function getBrowserLangCode($language = false) {

        if ($language === FALSE)
            $language=DoceboUpgradeGui::getLanguage();

        $code_arr=array_flip(DoceboUpgradeGui::getLanguageList());

        return $code_arr[$language];
    }

    function getLanguageList($key="code") {
        // key can be "code" or "language"

        $res=array();
        
        $db_sorg = new DoceboSql($GLOBALS['dbhost'], $GLOBALS['dbuname'], $GLOBALS['dbpass'], $GLOBALS['dbname']);
      	
      	$query = "
		SELECT lang_code, lang_browsercode 
		FROM `core_lang_language`";
		$lang = $db_sorg->query($query);
		
		while(list($lang_code, $b_code) = $db_sorg->fetchRow($lang)) { 
			if ($key == "code") {
				
				$res[$lang_code] = $lang_code;
			} elseif ($key == "language") {
				$res[$lang_code] = $lang_code;
			}
		}
		if(empty($res)) {
			
			$lang_d = dir(dirname(__FILE__).'/../lang/');
			while($elem = $lang_d->read()) {
		
				if(strpos($elem, 'php')!== false) {
					
					$elem = substr($elem, 0, -4);
					$res[$elem] = $elem;
				}
			}
			closedir($lang_d->handle);
		}
       /* if ($key == "code") {
            $res["en"]="english";
            $res["it"]="italian";
        }
        else if ($key == "language") {
            $res["english"]="english";
            $res["italian"]="italian";
        }*/

        return $res;
    } 
}

?>
