<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once(ABSPATH."lib/External/xml2array.php");
require_once(ABSPATH."GBusiness/Security.php");


class QAPICommandStatus
{
	public $stat;
	public $id;
	public $info;

	public function QAPICommandStatus($stat, $id, $info)
	{
		$this->stat = $stat;
		$this->id = $id;
		$this->info = $info;
	}
}

class XHR
{
	/**
	*@desc exeCurl, wait and return result
	*/
	public static function execCURL($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);

		return $output;
	}

	public static function execCURL_ReturnXML2Array($url)
	{
		// HACK: LockeVN: for web only
		// HACK: LockeVN, auto add authkey
		$url .= "&p=".Common::GetCurrentPageNumber(). "&authkey=".Security::GetCurrentAUauthkey();
		
		$output = XHR::execCURL($url);
		if(empty($output))
		{
			return null;
		}

		$xmlObj    = new XmlToArray($output);
		$arrayData = $xmlObj->createArray();
		return $arrayData;
	}


	/**
	*@desc
	* @return QAPICommandStatus
	*/
	public static function execCURL_ReturnCommandStatus($url)
	{
		// HACK: LockeVN, auto add authkey
		$url .= "&authkey=".Security::GetCurrentAUauthkey();
				
		$output = XHR::execCURL($url);
		if(empty($output))
		{
			return null;
		}
		$dom = new domDocument;
		$dom->loadXML($output);
		if (!$dom)
		{
			return null;
		}

		$s = simplexml_import_dom($dom);
		$arrAtrribs = $s->attributes();
		$stat = strval($arrAtrribs[0]);
		$id = strval($s->results[0]->result[0]->id);
		$info = strval($s->results[0]->result[0]->info);

		return new QAPICommandStatus($stat, $id, $info);
	}

	/**
	*@desc
	* @return QAPICommandStatus
	*/
	public static function execCURL_PostData_ReturnCommandStatus($url, $arrayPostData)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		if(!empty($arrayPostData))
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, $arrayPostData);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);

		// TEST:        return $output;

		if(empty($output))
		{
			return null;
		}
		$dom = new domDocument;
		$dom->loadXML($output);
		if (!$dom)
		{
			return null;
		}

		$s = simplexml_import_dom($dom);
		$arrAtrribs = $s->attributes();
		$stat = strval($arrAtrribs[0]);
		$id = strval($s->results[0]->result[0]->id);
		$info = strval($s->results[0]->result[0]->info);

		return new QAPICommandStatus($stat, $id, $info);
	}


}

?>