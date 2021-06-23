<?php

class Loader{

    public static function requireAllFiles(){
        global $_CONFIG;
        global $_TRANSLATIONS;
        global $_ERROR;
		global $_SUCCESS;
        global $_START_TIME;
        global $_IMAGES;
        global $db;
        global $encodeExplorer;

        $_CONFIG = array();
        $_ERROR = "";
        $_SUCCESS = "";
        $_START_TIME = microtime(TRUE);

        foreach(Loader::getListOfFiles(".") as $filePath){
            if(!strpos($filePath,"views/") && !strpos($filePath,"bd/") && !strpos($filePath, "log/") && !strpos($filePath,"css/") && !strpos($filePath,"index.php") ) {
                if(strpos($filePath,".php") || strpos($filePath,".html")){
                    require_once($filePath);
                }
            }
        }
    }

    public static function getListOfFiles($folder){
        $listOfFiles = array();
        foreach(scandir($folder) as $element){
            if($element != "." && $element != ".."){
                if(is_dir($folder."/".$element)){
                    $listOfFiles = array_merge($listOfFiles, Loader::getListOfFiles($folder."/".$element));
                } else {
                    $listOfFiles[] = $folder."/".$element;
                }
            }
        }
        return $listOfFiles;
    }

}

?>