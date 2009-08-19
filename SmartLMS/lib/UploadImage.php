<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once(ABSPATH."Lib/Text.php");



class UploadImage {

	static public $SUPPORT_TYPE = array('image/gif','image/jpg','image/png','image/jpeg','image/pjpeg','image/vnd.wap.wbmp');
	static public $SUPPORT_MAX_SIZE = 400000; // 400KB
	static public $SUPPORT_AVATAR_MAX_SIZE = 150000; // 150KB
	static public $SUPPORT_EXT = array('gif','jpg','png','jpeg','pjpeg','bmp');


	public static function receiveImg($inputFileFieldName = 'imgdata', $prefix = 'qapi', $maxsize = '')
	{		
		$filesize = $_FILES[$inputFileFieldName]['size'];
		$filetempname = $_FILES[$inputFileFieldName]['tmp_name'];
		$mime = strtolower(image_type_to_mime_type(exif_imagetype($filetempname)));
		// filetype is allow, and filesize is small enough
		if(in_array($mime, UploadImage::$SUPPORT_TYPE) && ($filesize < $maxsize) && $_FILES[$inputFileFieldName]['error'] <= 0)
		{
			$file_ext = substr($mime, 6);
			$time = date("dmHis", time());
            $GUID = Text::generateRandomStr();
			$filenamestoreonmediaserver = "$prefix-$time.$file_ext";

			/*if(!empty($filenamestoreonmediaserver))
			{
				make thumbnail and upload 
				if($thumbnailRequired)
				{
					$thumbnailImg = 'thumb-' . $filenamestoreonmediaserver;
					$result = ImageResizer::createThumbnail($filetempname, $thumbnailImg, 120, '', '');					
					if($result === false)
					{
						return false;
					}
					other wise upload thumbnail img to CDN
					UploadImage::curlImageToCDN($result, $thumbnailImg);
				}
				upload original image
				return UploadImage::curlImageToCDN($filetempname, $filenamestoreonmediaserver);
				
			}
			else    // error, can not make random file name
			{
				return false;
			}*/
			
			return (empty($filenamestoreonmediaserver) ? false : $filenamestoreonmediaserver);
		}
		else     // file  size, file type is not ok
		{
			return false;
		}

	}   // end function

	public static function curlImageToCDN($sourceFileName, $targetFolder, $targetFileName)
	{
		if(empty($sourceFileName) || empty($targetFileName))
		{
			return false;
		}
		$ch = curl_init();
		$fp = fopen($sourceFileName, 'r');
		curl_setopt($ch, CURLOPT_URL, Config::CDN_FTP_ADDRESS . $targetFolder . $targetFileName);
		curl_setopt($ch, CURLOPT_UPLOAD, 1);
		curl_setopt($ch, CURLOPT_INFILE, $fp);
		curl_setopt($ch, CURLOPT_INFILESIZE, filesize($sourceFileName));

		if (curl_exec($ch))
		{
			curl_close ($ch);
			return $targetFileName;
		}
		curl_close($ch);
		return false;
	}


}   // end class

?>