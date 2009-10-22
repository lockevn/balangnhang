<?php
	require_once('config.php');
    require_once('lib/filelib.php');
    require_once('lib/uploadlib.php');
    require_once('lib/gdlib.php');
    
    
    global $CFG;
    $fileurl = required_param('url');
    $userid = required_param('userid');
    $tmpArr = explode('/',$fileurl);
    if(!$tmpArr || sizeof($tmpArr) < 2) {
    	echo 'wrong param url ' . $fileurl;
    	exit;
    }
    $avatarFileName = $tmpArr[sizeof($tmpArr) - 1];
    $tmpArr2 = array();
    $tmpArr2 = explode('.', $avatarFileName);
    if(!$tmpArr2 || sizeof($tmpArr2) < 2) {
    	echo 'wrong filename ' . $fileurl;
    	exit;
    }
    $avatarFileType = $tmpArr2[1];
    $fhandler = fopen($fileurl, 'r');
    if(!$fhandler) {
    	echo 'error while reading file ' . $fileurl;
    	exit;
    }
    $newFile = $CFG->dataroot . "/user/0/$userid/$avatarFileName";    
    $newfhandler = fopen($newFile, 'w');
    
    $content = file_get_contents($fileurl); 
    $result = fwrite($newfhandler, $content);
    $avatarFileSize = filesize($newFile);     
    fclose($fhandler);
    fclose($newfhandler);
    
    if(!$result) {
    	echo 'error while writting file ' . $fileurl;
    	exit;
    }       
    $result = process_profile_image($newFile, $CFG->dataroot . "/user/0/$userid");       
    echo  $result;
    
?>