<?php

//
// The class controls logging in and authentication
//
class GateKeeper
{
	public static function init()
	{
		global $encodeExplorer;
		global $_CONFIG;
		global $db;
		$_CONFIG['users'] = array();
		if(EncodeExplorer::getConfig('use_db')){
			if(!is_null(EncodeExplorer::getConfig('db_name')) && strlen(EncodeExplorer::getConfig('db_name'))>0 ){
				
				$db = new MyDb();
				$listUsers = $db->query("SELECT u.id, u.password, r.description FROM users u INNER JOIN role r ON u.role = r.id WHERE r.description != 'anonymous';");
				while($userInfos = $listUsers->fetchArray(SQLITE3_ASSOC)){
					$_CONFIG['users'][] = array($userInfos['id'],$userInfos['password'],$userInfos['description']);
				}

			}
		}
		
		if(is_null(EncodeExplorer::getConfig('db')) && empty(EncodeExplorer::getConfig('users'))) {
			//
			// Créé la liste des utilisateurs si la connexion est nécéssaire et si le fichier 
			// contenant les informations de connexion des utilisateurs existe.
			//
			if(EncodeExplorer::getConfig('require_login')){
				
				if(file_exists(EncodeExplorer::getConfig('users_list_file'))){

					$fichier = fopen(EncodeExplorer::getConfig('users_list_file'),"r");
					$utilisateurs = fread($fichier, filesize(EncodeExplorer::getConfig('users_list_file')));
					$utilisateurs = preg_split("/[\n]+/", $utilisateurs);

					//Parcours des lignes du fichiers UTILISATEUR.txt
					foreach($utilisateurs as $unUtilisateur){
						
						//Entrée : la ligne correpond au format "unIdentifiant:unMotDePasse>unRôle"
						if(preg_match("/^(([^:>\/\\\\\s]+):{1}([^:>\/\\\\\s]+)>{1}((admin)|(user)))/", $unUtilisateur)){
							$unUtilisateur = preg_split("/[:>]+/", preg_replace("/\\s$/", "", $unUtilisateur));
							$_CONFIG['users'][] = $unUtilisateur;
						}

					}

				}

				if(!empty(EncodeExplorer::getConfig('users'))){
					EncodeExplorer::setConfig('require_login', true);
				} else {
					EncodeExplorer::setConfig('require_login', false);
				}
			}
		}

		if(strlen(EncodeExplorer::getConfig("session_name")) > 0)
			session_name(EncodeExplorer::getConfig("session_name"));
		
		if(count(EncodeExplorer::getConfig("users")) > 0)
			session_start();
		else
			return;

		if(isset($_GET['logout']))
		{
			$_SESSION['ee_user_name'] = null;
			$_SESSION['ee_user_pass'] = null;
			$encodeExplorer->setSuccessString("deconnexion_success");
		}

		if(isset($_POST['user_pass']) && strlen($_POST['user_pass']) > 0)
		{
			if(GateKeeper::isUser((isset($_POST['user_name'])?$_POST['user_name']:""), $_POST['user_pass']))
			{
				$_SESSION['ee_user_name'] = isset($_POST['user_name'])?$_POST['user_name']:"";
				$_SESSION['ee_user_pass'] = $_POST['user_pass'];

				$addr  = $_SERVER['PHP_SELF'];
				$param = '';

				if(isset($_GET['m']))
					$param .= (strlen($param) == 0 ? '?m' : '&m');

				if(isset($_GET['s']))
					$param .= (strlen($param) == 0 ? '?s' : '&s');

				if(isset($_GET['dir']) && strlen($_GET['dir']) > 0)
				{
					$param .= (strlen($param) == 0 ? '?dir=' : '&dir=');
					$param .= urlencode($_GET['dir']);
				}

				if(isset($_GET['file']) && strlen($_GET['file']) > 0)
				{
					$param .= (strlen($param) == 0 ? '?file=' : '&file=');
					$param .= urlencode($_GET['file']);
				}
				$encodeExplorer->setSuccessString("connexion_success");
				//header( "Location: ".$addr.$param);
			}
			else
				$encodeExplorer->setErrorString("wrong_pass");
		}
	}

	public static function isUser($userName, $userPass)
	{
		foreach(EncodeExplorer::getConfig("users") as $user)
		{
			if($user[1] == $userPass)
			{
				if(strlen($userName) == 0 || $userName == $user[0])
				{
					return true;
				}
			}
		}
		return false;
	}

	public static function isLoginRequired()
	{
		if(EncodeExplorer::getConfig("require_login") == false){
			return false;
		}
		return true;
	}

	public static function isUserLoggedIn()
	{
		if(isset($_SESSION['ee_user_name'], $_SESSION['ee_user_pass']))
		{
			if(GateKeeper::isUser($_SESSION['ee_user_name'], $_SESSION['ee_user_pass']))
				return true;
		}
		return false;
	}

	public static function isAccessAllowed()
	{
		if(!GateKeeper::isLoginRequired() || GateKeeper::isUserLoggedIn())
			return true;
		return false;
	}

	public static function isUploadAllowed(){
		if(EncodeExplorer::getConfig("upload_enable") == true && GateKeeper::isUserLoggedIn() == true && GateKeeper::getUserStatus() == "admin")
			return true;
		return false;
	}

	public static function isNewdirAllowed(){
		if(EncodeExplorer::getConfig("newdir_enable") == true && GateKeeper::isUserLoggedIn() == true && GateKeeper::getUserStatus() == "admin")
			return true;
		return false;
	}

	public static function isDeleteAllowed(){
		if(EncodeExplorer::getConfig("delete_enable") == true && GateKeeper::isUserLoggedIn() == true && GateKeeper::getUserStatus() == "admin")
			return true;
		return false;
	}

	public static function getUserStatus(){
		if(GateKeeper::isUserLoggedIn() == true && EncodeExplorer::getConfig("users") != null && is_array(EncodeExplorer::getConfig("users"))){
			foreach(EncodeExplorer::getConfig("users") as $user){
				if($user[0] != null && $user[0] == $_SESSION['ee_user_name'])
					return $user[2];
			}
		}
		return null;
	}

	public static function getUserName()
	{
		if(GateKeeper::isUserLoggedIn() == true && isset($_SESSION['ee_user_name']) && strlen($_SESSION['ee_user_name']) > 0)
			return $_SESSION['ee_user_name'];
		if(isset($_SERVER["REMOTE_USER"]) && strlen($_SERVER["REMOTE_USER"]) > 0)
			return $_SERVER["REMOTE_USER"];
		if(isset($_SERVER['PHP_AUTH_USER']) && strlen($_SERVER['PHP_AUTH_USER']) > 0)
			return $_SERVER['PHP_AUTH_USER'];
		return "an anonymous user";
	}

	public static function showLoginBox(){
		if(!GateKeeper::isUserLoggedIn() && count(EncodeExplorer::getConfig("users")) > 0)
			return true;
		return false;
	}
}

?>