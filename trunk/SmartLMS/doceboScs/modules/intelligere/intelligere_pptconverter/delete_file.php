<?php
	//$fileToFind = urldecode("input_file/334695450_676217634_574744177_73437500_Haivogliadigiocare.pps");
	$fileToFind= "";
	if(isset($_GET['fileToDelete']))
		$fileToFind=$_GET['fileToDelete'];


	if(isset($_POST['fileToDelete']))
		$fileToFind=$_POST['fileToDelete'];

	if($fileToFind!="")
	{
		//-- Classe perl 'XML
		include("xml_class.php");
		removeFile($fileToFind)	;
	}

//-------------------------------------------------------------------------------------------------------------
//												FUNCTIONS
//-------------------------------------------------------------------------------------------------------------
function removeFile($fileToFind)
{
	$xmlFileName = 'conversion_history.xml';

	//-- Creo l'oggetto e lo carico dal file
	$xml = new XMLFile();
	$fh = fopen( $xmlFileName, 'r' );
	$xml->read_file_handle( $fh );
	fclose($fh);

	//-- Punto alla ROOT
	$root = &$xml->roottag;
	$trovato = false;

	for ($i= 0; $i< $root->num_subtags(); $i++)
	{
		//-- il file ? quello che cercavo??
		if($root->tags[$i]->attributes["filePath"] == $fileToFind)
		{
			$trovato = true;
			if(!file_exists($fileToFind))
			{
				echo("Il file di input non esiste<br>");
				//return;
			}
			//-- se riesco eliminare il file modifico l'xml
			if(unlink($root->tags[$i]->attributes["filePath"] ) )
			{
				//-- E' un file convertito?
				if($root->tags[$i]->attributes["converted"]=="true")
					removeDir($root->tags[$i]->attributes["outputDir"]);

				//-- elimino
				$root->remove_subtag($i);

				//-- salvo le modifiche
				$fh = fopen( $xmlFileName, 'w' );
				$xml->write_file_handle( $fh );
				fclose($fh);

			}
			else
				echo("impossibile eliminare il file<br>");
			break;
		}
	}
	if(!$trovato)
		echo "Impossibile trovare il tag XML specificato<br>";

	$xml->cleanup();
	echo($trovato);
}
//-------------------------------------------------------------------------------------------------------------
function removeDir($dirname)
{
/****************************************
*	Rimuove directory e sottodirecory
*****************************************/

   if (is_dir($dirname)) {    //Operate on dirs only
	   $result=array();
	   if (substr($dirname,-1)!='/') {$dirname.='/';}    //Append slash if necessary
	   $handle = opendir($dirname);
	   while (false !== ($file = readdir($handle))) {
		   if ($file!='.' && $file!= '..') {    //Ignore . and ..
			   $path = $dirname.$file;
			   if (is_dir($path)) {    //Recurse if subdir, Delete if file
				   $result=array_merge($result,rmdirtree($path));
			   }else{
				   unlink($path);
				   $result[].=$path;
			   }
		   }
	   }
	   closedir($handle);
	   rmdir($dirname);    //Remove dir
	   $result[].=$dirname;
	   return $result;    //Return array of deleted items
   }else{
	   return false;    //Return false if attempting to operate on a file
   }
}
//-------------------------------------------------------------------------------------------------------------

?>