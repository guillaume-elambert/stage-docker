<!-- START: List table -->
<table class="table">
<?php
if($this->mobile == false)
{
	$img="directory";
	$message = "..";

	if($this->files || $this->dirs)
	{
		
		if(is_array($this->files) || $this->dirs){
			$colspan1=4;
			$colspan2=3;
	?>
		<tr class="row one header">
			<td class="icon"> </td>
			<td class="name"><?php print $this->makeArrow("name");?></td>
			<td class="size"><?php print $this->makeArrow("size"); ?></td>
			<td class="changed"><?php print $this->makeArrow("mod"); ?></td>
			<?php if($this->mobile == false && GateKeeper::isDeleteAllowed()){?>
			<td class="del"><?php print EncodeExplorer::getString("del"); ?></td>
			<?php } ?>
		</tr>
		
	<?php
		}
		else {
			$img = "arrow_left";
			$message = "Revenir au dossier";

			$colspan1=2;
			$colspan2=2;

			if(!$this->files->isValidForThumb() && !$this->files->isPdf()){
				$changerTheme ="<td>
					<button style=\"float:right;\" onclick=\"var obj = getElementById('affichageContenu'); if(obj.className == 'themeSombre'){this.innerHTML = 'Passer au thème sombre'; obj.setAttribute('class','themeClaire');} else {this.innerHTML = 'Passer au thème claire'; obj.setAttribute('class','themeSombre');}\">Passer au thème claire</button>
				</td>";
			}
		}
	} 
	?>
	
	<tr class="row two">
		<td class="icon">
			<a class="item" href="<?php print $this->makeLink(false, false, null, null, null, $this->location->getDir(false, false, false, 1),null); ?>">
				<img alt="dir" src="?img=<?php print($img);?>" />
			</a>
		</td>
		<td colspan="<?php print (($this->mobile == true?2:(GateKeeper::isDeleteAllowed()?$colspan1:$colspan1))); ?>" class="long">
			<a class="item" href="<?php print $this->makeLink(false, false, null, null, null, $this->location->getDir(true, false, false, 1),null); ?>"><?php print($message); ?></a>
		</td>
		
		<?php
		if(isset($changerTheme)){
			print $changerTheme;
		}
		?>
	</tr>
	
<?php
}
?>

<?php

if($this->files || $this->dirs)
{

	if(is_array($this->files) || $this->dirs)
	{
		
		//
		// Ready to display folders and files.
		//
		$row = 1;

		//
		// Folders first
		//
		if($this->dirs)
		{
			foreach ($this->dirs as $dir)
			{
				$row_style = ($row ? "one" : "two");
				print "<tr class=\"row ".$row_style."\">\n";
				print "<td class=\"icon\"><a href=\"".$this->makeLink(false, false, null, null, null, $this->location->getDir(false, false, false, 0).$dir->getNameEncoded(),null)."\" class=\"item dir\"><img alt=\"dir\" src=\"?img=directory\" /></a></td>\n";
				print "<td class=\"name\" colspan=\"".($this->mobile == true?2:3)."\">\n";
				print "<a href=\"".$this->makeLink(false, false, null, null, null, $this->location->getDir(false, false, false, 0).$dir->getNameEncoded(),null)."\" class=\"item dir\">";
				//print "<a href=\"".$this->makeLink(false, false, null, null, null, $this->location->getDir(false, false, false, 0),$dir->getNameEncoded())."\" class=\"item dir\">";
				print $dir->getNameHtml();
				print "</a>\n";
				print "</td>\n";
				if($this->mobile == false && GateKeeper::isDeleteAllowed()){
					print "<td class=\"del\"><a data-name=\"".htmlentities($dir->getName())."\" href=\"".$this->makeLink(false, false, null, null, $this->location->getDir(false, false, false, 0).$dir->getNameEncoded(), $this->location->getDir(false, false, false, 0),null)."\"><img src=\"?img=del\" alt=\"Delete\" /></a></td>";
				}
				print "</tr>\n";
				$row =! $row;
			}
		}

		//
		// Now the files
		//
		$count = 0;
		foreach ($this->files as $file)
		{

			$row_style = ($row ? "one" : "two");
			print "<tr class=\"row ".$row_style.(++$count == count($this->files)?" last":"")."\">\n";
			print "<td class=\"icon\">";
			print "<a href=\"".$this->makeLink(false, false, null, null, null, $this->location->getDir(false, false, false, 0),$file->getNameEncoded())."\"";
			
			if(EncodeExplorer::getConfig('open_in_new_window') == true)
				print "target=\"_blank\"";
			print " class=\"item file";
			
			if((EncodeExplorer::getConfig('max_size_download')!="")?($file->size < EncodeExplorer::getConfig('max_size_download')):true && $file->isValidForThumb() && isset($file->contenu) && !is_null($file->contenu) && trim($file->contenu) != "")
				print " thumb";

			print "\"";

			if((EncodeExplorer::getConfig('max_size_download')!="")?($file->size < EncodeExplorer::getConfig('max_size_download')):true && $file->isValidForThumb() && isset($file->contenu) && !is_null($file->contenu) && trim($file->contenu) != ""){
				print "contenu=\"data:image/".File::getFileExtension($file->name).";Content-Disposition: attachment; filename='".$file->name."';base64,".$file->contenu."\"";
			}
			print ">";
			print "<img alt=\"".$file->getType()."\" src=\"".$this->makeIcon($file->getType())."\" /></td>\n";
			print "</a>";
			print "<td class=\"name\"><div style=\"display:flex; justify-content:space-between;\">\n";
			
			print "<a href=\"".$this->makeLink(false, false, null, null, null, $this->location->getDir(false, false, false, 0),$file->getNameEncoded())."\"";
			
			if(EncodeExplorer::getConfig('open_in_new_window') == true)
				print "target=\"_blank\"";
			
			print " class=\"item file\">";
			print $file->getNameHtml();
			
			if($this->mobile == true)
			{
				print "<span class =\"size\">".$this->formatSize($file->getSize())."</span>";
			}
			
			print "</a>\n";
			
			if((EncodeExplorer::getConfig('max_size_download')!="")?($file->size < EncodeExplorer::getConfig('max_size_download')):true && isset($file->contenu) && !is_null($file->contenu)){
				print "<a title=\"Télécharger ".$file->name."\" class=\"telecharger\" href=\"data:application/octet-stream;Content-Disposition: attachment; filename='".$file->name."';base64,".$file->contenu."\"  download=\"".$file->name."\"><button>Télécharger</button></a>";
			}
			
			print "</div></td>\n";
			
			if($this->mobile != true)
			{
				print "<td class=\"size\">".$this->formatSize($file->getSize())."</td>\n";
				print "<td class=\"changed\">".$this->formatModTime($file->getModTime())."</td>\n";
			}
			
			if($this->mobile == false && GateKeeper::isDeleteAllowed()){
				print "<td class=\"del\">
					<a data-name=\"".htmlentities($file->getName())."\" href=\"".$this->makeLink(false, false, null, null, $this->location->getDir(false, false, false, 0).$file->getNameEncoded(), $this->location->getDir(false, false, false, 0),null)."\">
						<img src=\"?img=del\" alt=\"Delete\" />
					</a>
				</td>";
			}
			
			print "</tr>\n";
			$row =! $row;
		}
	}
	else {
		print'<tr><td id="affichageContenu"';
		if(!$this->files->isValidForThumb() && !$this->files->isPdf()){
			print 'class="themeSombre"';
		} else {
			print 'class="themeClaire"';
		}
		
		print 'colspan="'.($this->mobile == true?3:(GateKeeper::isDeleteAllowed()?5:4)).'" style="padding:2%;">';
		

		$contenu = base64_decode($this->files->contenu);
		//$contenu = htmlentities($contenu);
		
		if($this->files->isPdf()){
			print "<embed type=\"application/pdf\" src=\"data:application/pdf;Content-Disposition: attachment;filename='".$this->files->name."';base64,".$this->files->contenu."\" alt=\"".$this->files->name."\" align=\"middle\">";
			print "</embed>";
		} else if($this->files->isValidForThumb()){
			print "<img style=\"max-width:100%; display:block; margin:auto;\" src=\"data:image/".File::getFileExtension($this->files->name).";Content-Disposition: attachment; filename='".$this->files->name."';base64,".$this->files->contenu."\" alt=\"".$this->files->name."\" />";
		} else {
			print '<pre style="overflow: auto;">';
						
			if( strlen( htmlspecialchars($contenu) ) > 0 ) {
				print htmlspecialchars($contenu);
			} else {
				print iconv("ISO-8859-1", "UTF-8",$contenu);
			}
			print '</pre>';
		}
		
		print '</td></tr>';
	}
		
}

	//
	// The files and folders have been displayed
	//
?>
		
	<!-- END: List table -->
	</table>