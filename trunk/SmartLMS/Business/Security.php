<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/Net/XHR.php");
require_once(ABSPATH."Lib/HttpNavigation.php");

class ProfileInfo
{
	public $id;
	public $u;
}

class Security
{
	/**
	*@desc
	* @return true if authkey is valid, false if not valid, or empty $authkey
	*/
	public static function CheckAuthkeyValid($authkey)
	{
		if(empty($authkey))
		{
			return false;
		}
		else
		{
			$commandstatus = XHR::execCURL_ReturnCommandStatus(
				Config::API_URL."/public/qid/checkauthenticatetoken.php?format=xml&authkey=$authkey");
			return (boolean)$commandstatus->info;
		}
	}


	public static final function Logout($redirecttoURL = null)
	{
		setcookie('username','',time() - 3600, '/');
		setcookie('authkey','',time() - 3600, '/');

		session_destroy();

		if(!empty($redirecttoURL))
		{
			HttpNavigation::OutputRedirectToBrowser($redirecttoURL);
		}
	}


	/**
	*@desc try to take from session, and then from cookie.
	* If get empty, set empty string to cookie, session, and then return empty string.
	* if authkey has value,  check its validation, if not value, reset to empty string.
	* @return string empty if get nothing or invalid
	*/
	public static function GetCurrentAUauthkey($NeedToCheckValidAuthkeyWithQID = false)
	{
		$authkey = $_SESSION['authkey'];
		if(empty($authkey)) // give it one more change, take authkey from cookie
		{
			//// try to take from cookie
			$authkey = $_COOKIE['authkey'];
		}

		if(empty($authkey))
		{
			$authkey = '';
			$_SESSION["authkey"] = '';
		}
		else
		{
			if($NeedToCheckValidAuthkeyWithQID)
			{
				/// authkey has value, check API to see is it valid at current time.
				if(Security::CheckAuthkeyValid($authkey) == false)  // if not valid
				{
					$authkey = '';
					$_SESSION["authkey"] = '';
					setcookie("authkey", "0", strtotime('-2 Days'));
				}
			}
		}

		return $authkey;
	}

	/**
	*@desc
	* @return ProfileInfo
	*/
	public static function GetCurrentAUProfileInfo($authkey)
	{
		$sAUprofileinfo = $_SESSION['auprofileinfo'];
		if(empty($sAUprofileinfo)) // give it one more change, take authkey from cookie
		{
			//// try to take from cookie
			$sAUprofileinfo = $_COOKIE['auprofileinfo'];
		}

		if(empty($sAUprofileinfo))
		{
			$url = Config::API_URL."/public/qid/getauthkeyinfo.php?authkey=$authkey";
			$cs = XHR::execCURL_ReturnCommandStatus($url);
			if($cs->stat == 'ok')
			{
				$sAUprofileinfo = $cs->info;
			}
		}
		else
		{
			return null;
		}

		parse_str($sAUprofileinfo, $arr);
		$pinfo = new ProfileInfo();
		$pinfo->id = $arr['uid'];
		$pinfo->u = $arr['u'];

		return $pinfo;
	}

}

?>