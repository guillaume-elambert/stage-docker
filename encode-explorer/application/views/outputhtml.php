<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $this->getConfig('lang'); ?>" lang="<?php print $this->getConfig('lang'); ?>">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php print $this->getConfig('charset'); ?>">
<link rel="stylesheet" type="text/css" href="./css/general.css"/>

<!-- <meta charset="<?php print $this->getConfig('charset'); ?>" /> -->
<?php
if(($this->getConfig('log_file') != null && strlen($this->getConfig('log_file')) > 0)
	|| ($this->getConfig('thumbnails') != null && $this->getConfig('thumbnails') == true && $this->mobile == false)
	|| (GateKeeper::isDeleteAllowed()))
{
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
<?php
	if(GateKeeper::isDeleteAllowed()){
?>
	$('td.del a').click(function(){
		var answer = confirm('Are you sure you want to delete : \'' + $(this).attr("data-name") + "\' ?");
		return answer;
	});
<?php
	}
	if($this->logging == true)
	{
?>
		function logFileClick(path)
		{
			 $.ajax({
					async: false,
					type: "POST",
					data: {log: path},
					contentType: "application/x-www-form-urlencoded; charset=UTF-8",
					cache: false
				});
		}

		/*$("a.file").click(function(){
			logFileClick("<?php print $this->location->getDir(true, false, false, 0);?>" + $(this).html());
			return true;
		});*/
<?php
	}
	if(EncodeExplorer::getConfig("thumbnails") == true && $this->mobile == false)
	{
?>
		function positionThumbnail(e) {
			xOffset = 30;
			yOffset = 10;
			$("#thumb").css("left",(e.clientX + xOffset) + "px");

			diff = 0;
			if(e.clientY + $("#thumb").height() > $(window).height())
				diff = e.clientY + $("#thumb").height() - $(window).height();

			$("#thumb").css("top",(e.pageY - yOffset - diff) + "px");
		}

		$("a.thumb").hover(function(e){
			$("#thumb").remove();
			$("body").append("<div id=\"thumb\"><img src=\""+ $(this).attr("contenu") +"\" alt=\"Preview\" \/><\/div>");
			positionThumbnail(e);
			$("#thumb").fadeIn("medium");
		},
		function(){
			$("#thumb").remove();
		});

		$("a.thumb").mousemove(function(e){
			positionThumbnail(e);
			});

		$("a.thumb").click(function(e){$("#thumb").remove(); return true;});
<?php
	}
?>
	});
//]]>
</script>
<?php
}
?>
<title><?php if(EncodeExplorer::getConfig('main_title') != null) print EncodeExplorer::getConfig('main_title'); ?></title>
</head>
<body class="<?php print ($this->mobile == true?"mobile":"standard");?>">



<?php
//
// Print the error (if there is something to print)
//
if(isset($_SUCCESS) && strlen($_SUCCESS) > 0)
{
	print "<div id=\"success\">".$_SUCCESS."</div>";
}

if(isset($_ERROR) && strlen($_ERROR) > 0)
{
	print "<div id=\"error\">".$_ERROR."</div>";
}

?>
<div id="frame">
<?php
if(EncodeExplorer::getConfig('show_top') == true)
{
?>
<div id="top">
	<a href="<?php print $this->makeLink(false, false, null, null, null, "",null); ?>"><span><?php if(EncodeExplorer::getConfig('main_title') != null) print EncodeExplorer::getConfig('main_title'); ?></span></a>
<?php
if(EncodeExplorer::getConfig("secondary_titles") != null && is_array(EncodeExplorer::getConfig("secondary_titles")) && count(EncodeExplorer::getConfig("secondary_titles")) > 0 && $this->mobile == false)
{
	$secondary_titles = EncodeExplorer::getConfig("secondary_titles");
	print "<div class=\"subtitle\">".$secondary_titles[array_rand($secondary_titles)]."</div>\n";
}
?>
</div>
<?php
}

// Checking if the user is allowed to access the page, otherwise showing the login box
if(!GateKeeper::isAccessAllowed())
{
	include('./application/views/loginBox.php');
}
else
{
if($this->mobile == false && EncodeExplorer::getConfig("show_path") == true)
{
?>
<div class="breadcrumbs">
	<div id="breadcrumbs-left">
		<a href="?dir="><?php print $this->getString("root"); ?></a>
		<?php
			for($i = 0; $i < count($this->location->path); $i++)
			{
				print "&gt; <a href=\"".$this->makeLink(false, false, null, null, null, $this->location->getDir(false, false, false, count($this->location->path) - $i - 1),null)."\">";
				print $this->location->getPathLink($i, true);
				print "</a>\n";
			}
		?>
	</div>


	<?php
	if($this->files && !is_array($this->files))
	{
	?>
		<div id="breadcrumbs-right">
			<a href="data:application/octet-stream;Content-Disposition: attachment; filename='<?php print($this->files->name); ?>';base64,<?php print($this->files->contenu); ?>"  download="<?php print($this->files->name); ?>"><button>Télécharger <?php print($this->files->name); ?></button></a>
		</div>

	<?php
	}
	?>

</div>
<?php
}
include("listTable.php");
?>

</div>

<?php
if(GateKeeper::isAccessAllowed() && GateKeeper::showLoginBox()){
?>
<!-- START: Login area -->
<form enctype="multipart/form-data" method="post">
	<div id="login_bar">
	<?php print $this->getString("username"); ?>:
	<input type="text" name="user_name" value="" id="user_name" />
	<?php print $this->getString("password"); ?>:
	<input type="password" name="user_pass" id="user_pass" />
	<input type="submit" class="submit" value="<?php print $this->getString("log_in"); ?>" />
	<div class="bar"></div>
	</div>
</form>
<!-- END: Login area -->
<?php
include("./application/views/loginBox.php");
}
if(GateKeeper::isAccessAllowed() && $this->location->uploadAllowed() && (GateKeeper::isUploadAllowed() || GateKeeper::isNewdirAllowed()))
{
/*	if($this->files && is_array($this->files))
	{	*/
?>
<!-- START: Upload area -->
<form enctype="multipart/form-data" method="post">
	<div id="upload">
		<?php
		/*if(GateKeeper::isNewdirAllowed()){
		?>
		<div id="newdir_container">
			<input name="userdir" type="text" class="upload_dirname" />
			<input type="submit" value="<?php print $this->getString("make_directory"); ?>" />
		</div>
		<?php
		}*/
		if(GateKeeper::isUploadAllowed()){
		?>
		<div id="upload_container">
			<input name="userfile" type="file" class="upload_file"/>
			<input type="submit" value="<?php print $this->getString("upload"); ?>" class="upload_sumbit" />
		</div>
		<?php
	/*	}	*/
		?>
		<!--div class="bar"></div-->
	</div>
</form>
<!-- END: Upload area -->
<?php
	}
}

include("./application/views/footer.php");
?>

</html>
<?php
}
?>