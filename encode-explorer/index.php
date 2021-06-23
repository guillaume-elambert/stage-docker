<?php
/***************************************************************************
 *
 * Encode Explorer
 *
 * Author : Marek Rei (marek ät marekrei dot com)
 * Version : 6.4.1
 * Homepage : encode-explorer.siineiolekala.net
 *
 *
 * NB!:If you change anything, save with UTF-8! Otherwise you may
 *     encounter problems, especially when displaying images.
 *
 ***************************************************************************/


require_once("./system/libraries/Loader.php");
Loader::requireAllFiles();
/***************************************************************************/
/*   HERE COMES THE CODE.                                                  */
/*   DON'T CHANGE UNLESS YOU KNOW WHAT YOU ARE DOING ;)                    */
/***************************************************************************/

//
// This is where the system is activated.
// We check if the user wants an image and show it. If not, we show the explorer.
//
$encodeExplorer = new EncodeExplorer();
$encodeExplorer->init();


GateKeeper::init();

if(!ImageServer::showImage() && !Logger::logQuery())
{
	$location = new Location();
	$location->init();
	if(GateKeeper::isAccessAllowed())
	{
		$fileManager = new FileManager();
		$fileManager->run($location);
		$encodeExplorer->run($location);
		Logger::logAccess($location->getDir(true, false, false, 0), is_array($encodeExplorer->files)?true:false);
	} else {
		$encodeExplorer->run($location);
	}
}
?>