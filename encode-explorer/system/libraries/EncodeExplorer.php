<?php

class EncodeExplorer
{
	var $location;
	var $dirs;
	var $files;
	var $sort_by;
	var $sort_as;
	var $mobile;
	var $logging;
	var $spaceUsed;
	var $lang;

	//
	// Determine sorting, calculate space.
	//
	function init()
	{
		$this->sort_by = "";
		$this->sort_as = "";
		if(isset($_GET["sort_by"], $_GET["sort_as"]))
		{
			if($_GET["sort_by"] == "name" || $_GET["sort_by"] == "size" || $_GET["sort_by"] == "mod")
				if($_GET["sort_as"] == "asc" || $_GET["sort_as"] == "desc")
				{
					$this->sort_by = $_GET["sort_by"];
					$this->sort_as = $_GET["sort_as"];
				}
		}
		if(strlen($this->sort_by) <= 0 || strlen($this->sort_as) <= 0)
		{
			$this->sort_by = "name";
			$this->sort_as = "desc";
		}


		global $_TRANSLATIONS;
		if(isset($_GET['lang'], $_TRANSLATIONS[$_GET['lang']]))
			$this->lang = $_GET['lang'];
		else
			$this->lang = EncodeExplorer::getConfig("lang");

		$this->mobile = false;
		if(EncodeExplorer::getConfig("mobile_enabled") == true)
		{
			if((EncodeExplorer::getConfig("mobile_default") == true || isset($_GET['m'])) && !isset($_GET['s']))
				$this->mobile = true;
		}

		$this->logging = false;
		if(EncodeExplorer::getConfig("log_file") != null && strlen(EncodeExplorer::getConfig("log_file")) > 0)
			$this->logging = true;
	}

	//
	// Read the file list from the directory
	//
	function readDir()
	{
		global $encodeExplorer;
		//
		// Reading the data of files and directories
		//	
		if(!is_null($this->location))
		{
			try {
				$open_dir = @file_get_contents($this->location->getFullPath());

				if($open_dir){
					$open_dir = simplexml_load_string(preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $open_dir) );		
				
					if(!empty($open_dir))
					{
						$open_dir = $open_dir->soapenvBody->dossier;
						$this->dirs = array();
						$this->files = array();

						foreach ($open_dir->fichier as $unFichier )
						{
							if($unFichier != "." && $unFichier != "..")
							{
								if($unFichier->type == "Dossier")
								{	
									if(!in_array((string)$unFichier->type[0], EncodeExplorer::getConfig('hidden_dirs')))
										$this->dirs[] = new Dir($unFichier->nom, $this->location);
								}
								else if(!in_array((string)$unFichier->type[0], EncodeExplorer::getConfig('hidden_files')))
									$this->files[] = new File($unFichier->nom, $this->location, $unFichier->taille, $unFichier->mtime/1000, (strlen($unFichier->enveloppe)>0)?$unFichier->enveloppe:null);
							}
						}
					}
				}
				else
				{
					$encodeExplorer->setErrorString("unable_to_read_dir");
				}

			} catch (Exception $e) {
				$encodeExplorer->setError($e);				
			}
		}
		else
		{
			$encodeExplorer->setErrorString("unable_to_read_dir");;
		}
	}

	//
	// A recursive function for calculating the total used space
	//
	function sum_dir($start_dir, $ignore_files, $levels = 1)
	{
		if ($dir = opendir($start_dir))
		{
			$total = 0;
			while ((($file = readdir($dir)) !== false))
			{
				if (!in_array($file, $ignore_files))
				{
					if ((is_dir($start_dir . '/' . $file)) && ($levels - 1 >= 0))
					{
						$total += $this->sum_dir($start_dir . '/' . $file, $ignore_files, $levels-1);
					}
					elseif (is_file($start_dir . '/' . $file))
					{
						$total += File::getFileSize($start_dir . '/' . $file) / 1024;
					}
				}
			}

			closedir($dir);
			return $total;
		}
	}

	function calculateSpace()
	{
		if(EncodeExplorer::getConfig('calculate_space_level') <= 0)
			return;
		$ignore_files = array('..', '.');
		$start_dir = getcwd();
		$spaceUsed = $this->sum_dir($start_dir, $ignore_files, EncodeExplorer::getConfig('calculate_space_level'));
		$this->spaceUsed = round($spaceUsed/1024, 3);
	}

	function sort()
	{
		if(is_array($this->files)){
			usort($this->files, "EncodeExplorer::cmp_".$this->sort_by);
			if($this->sort_as == "desc")
				$this->files = array_reverse($this->files);
		}

		if(is_array($this->dirs)){
			usort($this->dirs, "EncodeExplorer::cmp_name");
			if($this->sort_by == "name" && $this->sort_as == "desc")
				$this->dirs = array_reverse($this->dirs);
		}
	}

	function makeArrow($sort_by)
	{
		if($this->sort_by == $sort_by && $this->sort_as == "asc")
		{
			$sort_as = "desc";
			$img = "arrow_up";
		}
		else
		{
			$sort_as = "asc";
			$img = "arrow_down";
		}

		if($sort_by == "name")
			$text = $this->getString("file_name");
		else if($sort_by == "size")
			$text = $this->getString("size");
		else if($sort_by == "mod")
			$text = $this->getString("last_changed");

		return "<a href=\"".$this->makeLink(false, false, $sort_by, $sort_as, null, $this->location->getDir(false, false, false, 0),null)."\">
			$text <img style=\"border:0;\" alt=\"".$sort_as."\" src=\"?img=".$img."\" /></a>";
	}

	function makeLink($switchVersion, $logout, $sort_by, $sort_as, $delete, $dir, $file)
	{
		$link = "?";
		if($switchVersion == true && EncodeExplorer::getConfig("mobile_enabled") == true)
		{
			if($this->mobile == false)
				$link .= "m&amp;";
			else
				$link .= "s&amp;";
		}
		else if($this->mobile == true && EncodeExplorer::getConfig("mobile_enabled") == true && EncodeExplorer::getConfig("mobile_default") == false)
			$link .= "m&amp;";
		else if($this->mobile == false && EncodeExplorer::getConfig("mobile_enabled") == true && EncodeExplorer::getConfig("mobile_default") == true)
			$link .= "s&amp;";

		if($logout == true)
		{
			$link .= "logout";
			return $link;
		}

		if(isset($this->lang) && $this->lang != EncodeExplorer::getConfig("lang"))
			$link .= "lang=".$this->lang."&amp;";

		if($sort_by != null && strlen($sort_by) > 0)
			$link .= "sort_by=".$sort_by."&amp;";

		if($sort_as != null && strlen($sort_as) > 0)
			$link .= "sort_as=".$sort_as."&amp;";

		$link .= "dir=".$dir;
		if($delete != null)
			$link .= "&amp;del=".$delete;

		if(!is_null($file))
			$link .= "&file=".rawurldecode($file);
		return $link;
	}

	function makeIcon($l)
	{
		$l = strtolower($l);
		return "?img=".$l;
	}

	function formatModTime($time)
	{
		$timeformat = "d/m/y H:i:s";
		if(EncodeExplorer::getConfig("time_format") != null && strlen(EncodeExplorer::getConfig("time_format")) > 0)
			$timeformat = EncodeExplorer::getConfig("time_format");
		
		if($time == 0)
			return "";
		else
			return date($timeformat, $time);
	}

	function formatSize($size)
	{
		$sizes = Array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
		$y = $sizes[0];
		for ($i = 1; (($i < count($sizes)) && ($size >= 1024)); $i++)
		{
			$size = $size / 1024;
			$y  = $sizes[$i];
		}
		return round($size, 2)." ".$y;
	}

	//
	// Debugging output
	//
	function debug()
	{
		print("Explorer location: ".$this->location->getDir(true, false, false, 0)."\n");
		for($i = 0; $i < count($this->dirs); $i++)
			$this->dirs[$i]->output();
		for($i = 0; $i < count($this->files); $i++)
			$this->files[$i]->output();
	}

	//
	// Comparison functions for sorting.
	//

	public static function cmp_name($b, $a)
	{
		return strcasecmp($a->name, $b->name);
	}

	public static function cmp_size($a, $b)
	{
		return ($a->size - $b->size);
	}

	public static function cmp_mod($b, $a)
	{
		return ($a->modTime - $b->modTime);
	}

	//
	// The function for getting a translated string.
	// Falls back to english if the correct language is missing something.
	//
	public static function getLangString($stringName, $lang)
	{
		global $_TRANSLATIONS;
		if(isset($_TRANSLATIONS[$lang]) && is_array($_TRANSLATIONS[$lang])
			&& isset($_TRANSLATIONS[$lang][$stringName]))
			return $_TRANSLATIONS[$lang][$stringName];
		else if(isset($_TRANSLATIONS["en"]))// && is_array($_TRANSLATIONS["en"])
			//&& isset($_TRANSLATIONS["en"][$stringName]))
			return $_TRANSLATIONS["en"][$stringName];
		else
			return "Translation error";
	}

	function getString($stringName)
	{
		return EncodeExplorer::getLangString($stringName, $this->lang);
	}

	//
	// The function for getting configuration values
	//
	public static function getConfig($name)
	{
		global $_CONFIG;
		if(isset($_CONFIG, $_CONFIG[$name]))
			return $_CONFIG[$name];
		return null;
	}
	
	//
	// The function for setting configuration values
	//
	public static function setConfig($name,$value)
	{
		global $_CONFIG;
		if($_CONFIG[$name] = $value)
			return true;
		return false;
	}

	public static function setError($message)
	{
		global $_ERROR;
		if(isset($_ERROR) && strlen($_ERROR) > 0)
			;// keep the first error and discard the rest
		else
			$_ERROR = $message;
	}

	function setErrorString($stringName)
	{
		EncodeExplorer::setError($this->getString($stringName));
	}

	public static function setSuccess($message)
	{
		global $_SUCCESS;
		if(isset($_SUCCESS) && strlen($_SUCCESS) > 0)
			;// keep the first error and discard the rest
		else
			$_SUCCESS = $message;
	}

	function setSuccessString($stringName)
	{
		EncodeExplorer::setSuccess($this->getString($stringName));
	}

	//
	// Main function, activating tasks
	//
	function run($location)
	{	
		
		
		$this->location = $location;

		if(isset($_GET['file']) && strlen($_GET['file']) > 0){
			
			try{
				
				$contenu = @file_get_contents($this->getConfig('serveur_webservice')."?method=getFichier&identite=".EncodeExplorer::getConfig('starting_dir').$location->getDir(true, true, false, 0)/*.$_GET['file']*/);

				if(isset($contenu)){
					$contenu = simplexml_load_string( preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $contenu ) );
					
					if(isset($contenu->soapenvBody->fichier->type) && $contenu->soapenvBody->fichier->type != "dossier"){
						
						if(isset($contenu->soapenvBody->fichier->enveloppe) && trim(base64_decode($contenu->soapenvBody->fichier->enveloppe))!="" ){

							$fichier = $contenu->soapenvBody->fichier;
							$this->files = new File($fichier->nom, $this->location, $fichier->taille, $fichier->mtime, (string) $fichier->enveloppe);

						} else {
							unset($location->path[sizeof($location->path)-1]);
							$this->setError("Ce fichier n'a pas de contenu.");
							$this->calculateSpace();
							$this->readDir();
							$this->sort();
						}
						
					} else {
						unset($location->path[sizeof($location->path)-1]);
						$this->setError("Ce fichier n'existe pas.");
						$this->calculateSpace();
						$this->readDir();
						$this->sort();
						//header('Location: '.$this->makeLink(false, false, null, null, null, $location->getDir(false, false, false, 0),null));
					}
				} else {
					unset($location->path[sizeof($location->path)-1]);
					$this->setError("Ce fichier n'existe pas.");
					$this->calculateSpace();
					$this->readDir();
					$this->sort();
				}
			} catch (Exception $e){
				unset($location->path[sizeof($location->path)-1]);
				$this->setError($e);
				$this->calculateSpace();
				$this->readDir();
				$this->sort();
			}

		} else {
			$this->calculateSpace();
			$this->readDir();
			$this->sort();
		}
		$this->outputHtml();
	}

	public function printLoginBox()
	{
		include('loginBox.php');
	}

	//
	// Printing the actual page
	//
	function outputHtml()
	{
		global $_ERROR;
		global $_SUCCESS;
		global $_START_TIME;

		include("./application/views/outputhtml.php");
?>


<?php
	}
}

?>