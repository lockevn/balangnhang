<?php // 4, compatibile 5.0 e 5.1
// Directory di destinazione
$destination_dir = 'input_file/';

if(
	isset($_FILES['Filedata']) 
    // n array ?
    && is_array($_FILES['Filedata']) 
     //esistono gli elementi tmp_name, name, size, error
        // di questo array ?
    && isset(
        $_FILES['Filedata']['tmp_name'],
        $_FILES['Filedata']['name'],
        $_FILES['Filedata']['size'],
        $_FILES['Filedata']['error']
    ) 
    // l' errore sattamente zero ?
    && ($_FILES['Filedata']['error'] === 0)
) 
{
	//--------------------------------
	//	CREO NUOVO NOME PER IL FILE
	$newFileName ="";
	do
	{
		$newFileName =$destination_dir. rand(0,99999999999) ."_" . rand(0,99999999999) ."_"  . rand(0,99999999999) ."_" .rand(0,99999999) ."_".$_FILES['Filedata']['name'];
		$newFileName = str_replace(" ", "-",$newFileName);
		//$newFileName = $destination_dir . "_".$_FILES['Filedata']['name'];
	}while (file_exists($newFileName));
	
	//--------------------------------
	// SPOSTO IL FILE    
	$result = move_uploaded_file($_FILES['Filedata']['tmp_name'],$newFileName ) ; 
	
	/******************************************************************
	*                  PARAMETRI  IN
	* *****************************************************************
	*  1 - Nome File Uploadato
	*  2 - User che sta uploadando
	*  3 - Account Conference
	*  4 - original_name
	*  5 - description
	* ******************************************************************/
	
	$user = isset($_GET['user']) ?  urlencode(str_replace(" ","%20",$_GET['user'])  )  : '';
	$account = isset($_GET['account']) ? urlencode(str_replace(" ","%20",$_GET['account'])) : '';
	$description = isset($_GET['description']) ? urlencode(str_replace(" ","%20",$_GET['description'])) : '';
	$original_name = urlencode(str_replace(" ","%20",$_FILES['Filedata']['name']));
	
	$fp = fopen("conversion.txt", 'w+');
	
	//-- NON INVERTIRE I PARAMETRI
	fwrite($destination_dir."result.txt",$fp,shell_exec("PptConverter.exe $FileName" . " " . ($newFileName) . " " 
												. ($user). " " . ($account) . " " .  ($original_name) . " " .$description));		

	fwrite($fp, " user: " . $user);
	fwrite($fp," account: " .  $account);
	fwrite($fp, " description: " . $description);
	fwrite($fp, " original_name: " . $original_name);
	fclose($fp);
	
    
}
?>
