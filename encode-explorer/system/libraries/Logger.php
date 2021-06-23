<?php

//
// The class for logging user activity
//
class Logger
{
	public static function log($message)
	{
		global $encodeExplorer;
		if(strlen(EncodeExplorer::getConfig('log_file')) > 0)
		{
			if(Location::isFileWritable("system/log/".EncodeExplorer::getConfig('log_file')))
			{
				if(EncodeExplorer::getConfig('use_db')){
					global $db;
					$db->prepare($message)->execute();
				} else {
					$message = "[" . date("Y-m-d H:i:s", time()) . "] ".$message." (".$_SERVER["HTTP_USER_AGENT"].")".PHP_EOL;
					error_log($message, 3, EncodeExplorer::getConfig('log_file'));
				}
			}
			else
				$encodeExplorer->setErrorString("log_file_permission_error");
		}
	}

	public static function logAccess($path, $isDir)
	{		
		if(!$isDir && isset($path[strlen($path)-1]) && $path[strlen($path)-1] =="/"){
			$path = substr($path,0,-1) ;
		}

		if(EncodeExplorer::getConfig('use_db')){
			$message = "INSERT INTO log VALUES ('".time()."', '".GateKeeper::getUserName()."', 'access', '".($isDir?"dir":"file")."', '".SQLite3::escapeString($path)."','".SQLite3::escapeString($_SERVER["HTTP_USER_AGENT"].PHP_EOL)."');";
		} else {
			$message = $_SERVER['REMOTE_ADDR']." ".GateKeeper::getUserName()." accessed ";
			$message .= $isDir?"dir":"file";
			$message .= " ".$path;
		}
		Logger::log($message);
	}

	public static function logQuery()
	{
		if(isset($_POST['log']) && strlen($_POST['log']) > 0)
		{
			Logger::logAccess($_POST['log'], false);
			return true;
		}
		else
			return false;
	}

	public static function logCreation($path, $isDir)
	{		
		if(!$isDir && isset($path[strlen($path)-1]) && $path[strlen($path)-1] =="/"){
			$path = substr($path,0,-1) ;
		}

		if(EncodeExplorer::getConfig('use_db')){
			$message = "INSERT INTO log VALUES ('".time()."', '".GateKeeper::getUserName()."', 'create', '".($isDir?"dir":"file")."', '".SQLite3::escapeString($path)."','".SQLite3::escapeString($_SERVER["HTTP_USER_AGENT"].PHP_EOL)."');";
		} else {
			$message = $_SERVER['REMOTE_ADDR']." ".GateKeeper::getUserName()." created ";
			$message .= $isDir?"dir":"file";
			$message .= " ".$path;
		}
		Logger::log($message);
	}

	public static function emailNotification($path, $isFile)
	{
		if(strlen(EncodeExplorer::getConfig('upload_email')) > 0)
		{
			$message = "This is a message to let you know that ".GateKeeper::getUserName()." ";
			$message .= ($isFile?"uploaded a new file":"created a new directory")." in Encode Explorer.\n\n";
			$message .= "Path : ".$path."\n";
			$message .= "IP : ".$_SERVER['REMOTE_ADDR']."\n";
			mail(EncodeExplorer::getConfig('upload_email'), "Upload notification", $message);
		}
	}
}

?>