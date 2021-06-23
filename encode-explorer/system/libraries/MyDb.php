<?php

class MyDb extends SQLite3 {
	
	function __construct()
	{
		//GE ICI
		//$this->open('mysqlitedb.db');
		global $encodeExplorer;
		global $db;
		
		try {
			$this->open("./system/db/".str_replace(".db","",EncodeExplorer::getConfig('db_name')).'.db');
			$this->init();
		} catch( Exception $e){
			$encodeExplorer->setErrorString("db_open_error");
		}


	}

	function init()
	{
		global $encodeExplorer;

		$dbTest = $this->query('SELECT COUNT(*) FROM sqlite_master WHERE name IN ("users","role","log") GROUP BY (type);')->fetchArray();
		
		if(empty($dbTest) || $dbTest[0] != 3)
		{
			if(!$this->exec(fread(fopen("script.sql","r"),filesize("script.sql") ) ) ){
				$encodeExplorer->setErrorString("db_init_error");
			}
		}
		
	}
	
}

?>