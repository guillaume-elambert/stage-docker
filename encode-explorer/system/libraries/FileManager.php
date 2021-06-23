<?php

//
// The class for any kind of file managing (new folder, upload, etc).
//
class FileManager
{
	/* Obsolete code
	function checkPassword($inputPassword)
	{
		global $encodeExplorer;
		if(strlen(EncodeExplorer::getConfig("upload_password")) > 0 && $inputPassword == EncodeExplorer::getConfig("upload_password"))
		{
			return true;
		}
		else
		{
			$encodeExplorer->setErrorString("wrong_password");
			return false;
		}
	}
	*/
	function newFolder($location, $dirname)
	{
		global $encodeExplorer;
		if(strlen($dirname) > 0)
		{
			$forbidden = array(".", "/", "\\");
			for($i = 0; $i < count($forbidden); $i++)
			{
				$dirname = str_replace($forbidden[$i], "", $dirname);
			}

			if(!$location->uploadAllowed())
			{
				// The system configuration does not allow uploading here
				$encodeExplorer->setErrorString("upload_not_allowed");
			}
			/*else if(!$location->isWritable())
			{
				// The target directory is not writable
				$encodeExplorer->setErrorString("upload_dir_not_writable");
			}*/
			else if(!mkdir($location->getDir(true, false, false, 0).$dirname, EncodeExplorer::getConfig("new_dir_mode")))
			{
				// Error creating a new directory
				$encodeExplorer->setErrorString("new_dir_failed");
			}
			else if(!chmod($location->getDir(true, false, false, 0).$dirname, EncodeExplorer::getConfig("new_dir_mode")))
			{
				// Error applying chmod
				$encodeExplorer->setErrorString("chmod_dir_failed");
			}
			else
			{
				// Directory successfully created, sending e-mail notification
				Logger::logCreation($location->getDir(true, false, false, 0).$dirname, true);
				Logger::emailNotification($location->getDir(true, false, false, 0).$dirname, false);
			}
		}
	}

	function uploadFile($location, $userfile)
	{
		global $encodeExplorer;
		$name = basename($userfile['name']);
		if(get_magic_quotes_gpc())
			$name = stripslashes($name);

		$upload_dir = EncodeExplorer::getConfig("starting_dir").$location->getDir(true, false, false, 0);
		$upload_file = $upload_dir . $name;

		if(function_exists("finfo_open") && function_exists("finfo_file"))
			$mime_type = File::getFileMime($userfile['tmp_name']);
		else
			$mime_type = $userfile['type'];

		$extension = File::getFileExtension($userfile['name']);

		if(!$location->uploadAllowed())
		{
			$encodeExplorer->setErrorString("upload_not_allowed");
		}
		/*else if(!$location->isWritable())
		{
			$encodeExplorer->setErrorString("upload_dir_not_writable");
		}*/
		else if(!is_uploaded_file($userfile['tmp_name']))
		{
			$encodeExplorer->setErrorString("failed_upload");
		}
		else if(is_array(EncodeExplorer::getConfig("upload_allow_type")) && count(EncodeExplorer::getConfig("upload_allow_type")) > 0 && !in_array($mime_type, EncodeExplorer::getConfig("upload_allow_type")))
		{
			$encodeExplorer->setErrorString("upload_type_not_allowed");
		}
		else if(is_array(EncodeExplorer::getConfig("upload_reject_extension")) && count(EncodeExplorer::getConfig("upload_reject_extension")) > 0 && in_array($extension, EncodeExplorer::getConfig("upload_reject_extension")))
		{
			$encodeExplorer->setErrorString("upload_type_not_allowed");
		}
		else{
			
			$fichier = fopen($userfile['tmp_name'],"r");
			$contenu = base64_encode(fread($fichier, filesize($userfile['tmp_name']) ));
			fclose($fichier);

			$xml_data = "
			<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:mon=\"http://monserveur/\">
				<soapenv:Header/>
				<soapenv:Body>
					<mon:depotFichierInPart>
						<mon:repertoire>$upload_dir</mon:repertoire>
						<mon:nomFichier>$name</mon:nomFichier>
						<mon:enveloppe>$contenu</mon:enveloppe>
					</mon:depotFichierInPart>
				</soapenv:Body>
			</soapenv:Envelope>";

			$ch = @curl_init(EncodeExplorer::getConfig("serveur_webservice")."?method=depotFichier");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml','SOAPAction: "http://monserveur/depotFichier"'));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = @curl_exec($ch);
			curl_close($ch);

			
			$output = (int) simplexml_load_string(preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $output))->soapenvBody->resultLong[0];
			
			/*if(!@move_uploaded_file($userfile['tmp_name'], EncodeExplorer::getConfig("basedir")))*/
			if($output == 1)
			{
				$encodeExplorer->setSuccessString("file_upload_success");
				//chmod($upload_file, EncodeExplorer::getConfig("upload_file_mode"));
				Logger::logCreation($location->getDir(true, false, false, 0).$name, false);
				Logger::emailNotification($location->getDir(true, false, false, 0).$name, true);
			}
			else
			{
				$encodeExplorer->setErrorString("failed_move");
			}
		}
	}

	public static function delete_dir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir")
						FileManager::delete_dir($dir."/".$object);
					else
						unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

	public static function delete_file($file){
		if(is_file($file)){
			unlink($file);
		}
	}

	//
	// The main function, checks if the user wants to perform any supported operations
	//
	function run($location)
	{
		if(isset($_POST['userdir']) && strlen($_POST['userdir']) > 0){
			if($location->uploadAllowed() && GateKeeper::isUserLoggedIn() && GateKeeper::isAccessAllowed() && GateKeeper::isNewdirAllowed()){
				$this->newFolder($location, $_POST['userdir']);
			}
		}

		if(isset($_FILES['userfile']['name']) && strlen($_FILES['userfile']['name']) > 0){
			if($location->uploadAllowed() && GateKeeper::isUserLoggedIn() && GateKeeper::isAccessAllowed() && GateKeeper::isUploadAllowed()){
				$this->uploadFile($location, $_FILES['userfile']);
			}
		}

		if(isset($_GET['del'])){
			if(GateKeeper::isUserLoggedIn() && GateKeeper::isAccessAllowed() && GateKeeper::isDeleteAllowed()){
				$split_path = Location::splitPath($_GET['del']);
				$path = "";
				for($i = 0; $i < count($split_path); $i++){
					$path .= $split_path[$i];
					if($i + 1 < count($split_path))
						$path .= "/";
				}
				if($path == "" || $path == "/" || $path == "\\" || $path == ".")
					return;

				if(is_dir($path))
					FileManager::delete_dir($path);
				else if(is_file($path))
					FileManager::delete_file($path);
			}
		}
	}
}

?>