<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
  <head>
	<!--- This site was built by Mario Rosasco at the University of Washington. --->

    <!--- JAVASCRIPT FUNCTIONS --->
    <script type="text/javascript">

	
      function toggleShow(areaID) {
          var elementmode = document.getElementById(areaID).style;
          elementmode.display = (!elementmode.display) ? 'none' : '';
      } 

      function clearForms() {
        document.forms[0].elements['filename'].value = '';
        document.forms[0].elements['directory'].value = '';
		document.forms[0].elements['entry'].value = '';
      }

      function setSelectionRange(input, selectionStart, selectionEnd) {
	    if (input.setSelectionRange) {
	        input.focus();
	        input.setSelectionRange(selectionStart, selectionEnd);
	    }
	    else if (input.createTextRange) {
	        var range = input.createTextRange();
	        range.collapse(true);
	        range.moveEnd('character', selectionEnd);
	        range.moveStart('character', selectionStart);
	        range.select();
	    }
	}

	function replaceSelection (input, replaceString) {
	    if (input.setSelectionRange) {
		var selectionStart = input.selectionStart;
		var selectionEnd = input.selectionEnd;

		
		var scrollTop = input.scrollTop; // fix scrolling issue with Firefox
		input.value = input.value.substring(0, selectionStart)+ replaceString + input.value.substring(selectionEnd);
		input.scrollTop = scrollTop;
    
		if (selectionStart != selectionEnd){ 
			setSelectionRange(input, selectionStart, selectionStart + 	replaceString.length);
		}else{
			setSelectionRange(input, selectionStart + replaceString.length, selectionStart + replaceString.length);
		}

	    }else if (document.selection) {
		var range = document.selection.createRange();

		if (range.parentElement() == input) {
			var isCollapsed = range.text == '';
			range.text = replaceString;

			 if (!isCollapsed)  {
				range.moveStart('character', -replaceString.length);
				range.select();
	                 }
	        }
	}
}


      // We are going to catch the TAB key so that we can use it, Hooray!
      function catchTab(item,e){
        if(navigator.userAgent.match("Gecko")){
		c=e.which;
	}else{
		c=e.keyCode;
	}
	if(c==9){
		replaceSelection(item,String.fromCharCode(9));
		setTimeout("document.getElementById('"+item.id+"').focus();",0);	
		return false;
	}
		    
      }


    </script>
	<script type="text/javascript" src="./ckeditor/ckeditor.js">	</script>

<!--- PHP FUNCTIONS --->
<?php
   // include the excel file reader
   require_once 'xlsReader/reader.php';

   // accepts a handle to a directory and reads all of the files in that
   // directory, returning them as an array.
   function getAllFiles($handle){
       $allFiles;
       $j = 0;

       while (false !== ($file = readdir($handle))) {
           $allFiles[$j] = $file;
           $j++;
       }
       sort($allFiles, SORT_STRING);
       return $allFiles;
   }
   
   // accepts a directory and uses getAllFiles to display a list of files in that directory
   function listFileLinks($dir){
       if ($handle = opendir($dir)) {
           $files = getAllFiles($handle);

           foreach($files as $file){

               // get the date last modified
               $stats = stat("$dir/$file");
               $modTime = $stats[mtime];

               // make a link to the file
               if ($file != "." && $file != ".."){

                   // if the file is a directory, recurse on it
                   if (is_dir("$dir/$file")){
                       echo "<big><a href=\"javascript:toggleShow('$file')\">$file</a></big>+<br>";
                       echo "<span id = \"$file\" style = \"display: none;\">";
                       listFileLinks("$dir/$file");
                       echo "</span>";
                   }


                   else{
                       // display an icon linking to the raw data file
                       echo "<a href=\"$dir/$file\">
			     <img src=\"http://www.newwestpublishing.com/images/page-icon.gif\"
				  width=\"10px\" height=\"10px\">
			     </a>";
                       echo "<a href=\"index.php?function=displayFileInDiv&file=$file&dir=$dir\">
			    $file</a> <sub>" . date("ymd,g:ia", $modTime) . "</sub><br>";

                       
                   }
               }
           }
       closedir($handle);
       }
   }

   // accepts a handle, and returns an array of all dirs in the handle dir besides hidden directories
   function getDirs($handle){
	   $hiddenDirs = array(".", "..", "ckeditor", "xlsReader");
	   $auth_username = $_SERVER["REMOTE_USER"];
	   // To restrict user access to certain directories, uncomment this line and change usernames and restricted directory name appropriately
	   //if (($auth_username != "user1")&&($auth_username != "user2")){ array_push($hiddenDirs, "Restricted Directory Name");}
       $allDirs;
       $j = 0;

       while (false !== ($file = readdir($handle))) {
           //if (is_dir($file) && $file != "." && $file != ".."){
		   if (is_dir($file) && !(in_array($file, $hiddenDirs))){
               $allDirs[$j] = $file;
               $j++;
           }
       }
       sort($allDirs, SORT_STRING);
       return $allDirs;
   }

   // takes no arguments and makes a list of dirs with links that display their files
   function listDirLinks(){
       if ($handle = opendir('.')) {
           $dirs = getDirs($handle);

           // MAKE ROW 1: MAIN LINKS
           echo "<table width=\"100%\" align=\"center\" valign=\"top\">";
           echo "<tr valign=\"top\" align=\"center\">";

	   $dir_width = 100/(count($dirs));
	   $auth_username = $_SERVER["REMOTE_USER"];
	       
	   foreach($dirs as $dir){
			// make a link to the dir
			echo "<td width=\"".$dir_width."%\"><a href=\"javascript:toggleShow('$dir')\"><big>$dir</big></a></td>";
       }

           // MAKE ROW 2: SUB LINKS
           echo "</tr>";
           echo "<tr align=\"center\" valign=\"top\">";

           foreach($dirs as $dir){
               // make a link to files contained in the dir
               echo "<td> <span id = \"$dir\" style=\"display: none; font-size: small;\">";
		listFileLinks($dir);
		echo "</span> </td>";
           }
           echo "</tr>";
           echo "</table>";
       closedir($handle);
       }
   }

?>
    
    <link href="style.css" rel="stylesheet" type="text/css">
    <title>Lab Notebook</title>
  </head>

<!--- DEFAULT PAGE STYLE SETTINGS --->
  <body style="background-repeat: repeat-x; background-attachment:fixed; background-size:100%;" alink="#000099" link="#000099" vlink="#990099">

    <font class="Arial">
      <div style="text-align: center;">

	<!--- HEADING --->
	<DIV class="header">
	  <a href="."><h1>Lab Notebook</h1></a>
	</DIV>
	<br>

	<!--- LINKS --->
	<DIV class="linksHolder">

	  <?php listDirLinks(); ?>

	</DIV>
	<br>
      </div>

      <!--- DISPLAY AREA --->
      <div class="entry">

	<!--- PAGE LOADING PHP SCRIPTS --->
	<?php
	  // Use GET to call functions from links
	if( isset($_GET['function']) ) {
		switch( $_GET['function'] ) {
            case 'displayFileInDiv': 
				$file = $_GET['file'];
				$dir = $_GET['dir'];
				$data = displayFileInDiv($file,$dir);
				break;
			case 'search':
				$searchstring = $_POST['searchstring'];
				search($searchstring);
				break;
            }
    }
    else {
        echo "This page maintained by _______________ (user [at] mailserver.com)";
    }
	
	
	
	function search_recursive($searchdir, $searchstring){
		$filenames;
		$search_results=array();
		$data="";
		$strlocation=0;
		$numbers=array(5,10,6,8,15,20);
		$i=0;
		
		if($searchstring!="")
		{
			static $folderDepth=0;
		
			if ($handle = opendir($searchdir)) 
			{ 
				// Get all the files 
				$filenames = getAllFiles($handle);
				closedir($handle); 
			} 

			foreach($filenames as $value)
			{
				if ("$value" != "." && "$value" != ".."){
					if (is_dir("$searchdir/$value")) {
					
						$folderDepth++;
						if (folderDepth >= 20){ die("Could not safely complete search. Too many nested folders, suspected infinite loop"); }
						$more_results = search_recursive("$searchdir/$value", $searchstring);
						if (!empty($more_results)){
							$search_results = array_merge($search_results, $more_results); 
						}
						$folderDepth--;
					}
				
					else{
						//$data = strip_tags(file_get_contents("$searchdir/$value")) or die ("Could not open file: $searchdir/$value");
						if ($data = strip_tags(file_get_contents("$searchdir/$value")))
						{
							if ($data == false){ die ("File read failed at: $searchdir/$value"); }
							if(($strlocation = stripos($data, $searchstring))!=false)
							{
								$substring = substr($data, $strlocation-50, 100);
								$substring = str_ireplace($searchstring, "<b><u>$searchstring</u></b>", $substring);
								$search_results["$searchdir/$value"] = $substring;
							}
						}
					}
				}
			}
		}
		return $search_results;
	}
	
	// A wrapper function to make the first call to the recursive search, then display the results
	function search($search_string){
		echo "<h2>Search for: $search_string</h2>";
		$myresults = search_recursive(".", $search_string);
		if (empty($myresults)) {
			echo "No search results found<br><a href=\".\">Return home<a>";
		}
		else{
			foreach ($myresults as $key => $value){
				echo "<a href=\"$key\">$key</a> - \"...$value...\"<br>";
			}
		}
	}
	
	   
   function displayFileInDiv($file, $dir){
       // an array of the file extensions that we'll consider displayable images
       $imageExtensions = array("jpg","jpeg", "gif", "bmp", "tif", "tiff", "png", "swf", "psd", "jp2", "iff", "ico");
       $fpath = "./$dir/$file";
       $data = "";

       // figure out what file type we're working with
       $ext = pathinfo($file, PATHINFO_EXTENSION);

	   // find links to the next and previous files
	   $handle=opendir("./$dir");
	   $allf = getAllFiles($handle);
	   $i = array_search($file, $allf);
	   $nextfile = $allf[$i+1];
	   $prevfile = $allf[$i-1];

       // display a header with the file name and links to the previous and next files
	   echo "<p><p><p><table width=\"100%\"><tr>";
	   if ($prevfile != null && !is_dir("./$dir/$prevfile")){
		echo "<td style =\font-size: x-small; text-align: left;\"><a href=\"index.php?function=displayFileInDiv&file=$prevfile&dir=$dir\">&lt;&lt;&lt;</a></td>";
	   }
	   else { echo "<td></td>"; }
       echo "<td style=\"font-size: x-small; text-align: center;\">$dir/$file</td>";
	   
	   if ($nextfile != null && !is_dir("./$dir/$nextfile")){
		echo "<td style =\font-size: x-small; text-align: right;\"><a href=\"index.php?function=displayFileInDiv&file=$nextfile&dir=$dir\">&gt;&gt;&gt;</a></td>";
	   }
	   else { echo "<td></td>"; }
	   
	   echo "</tr></table>";
	   
	   
       // CASE1: HTML FILE OR TEXT FILE- display file as written
       if ($ext == "html" || $ext == "htm" || $ext == "txt"){
	   $fh = fopen("./$dir/$file", 'r');
	   $data = fread($fh, filesize("./$dir/$file"));
	   fclose($fh);
       }
       
       // CASE2: EXCEL FILE - read and display file
       else if($ext == "xls"){
           // new data object
           $raw = new Spreadsheet_Excel_Reader();
	   
	   // set the encoding
	   $raw->setOutputEncoding('CP1251');

	   // read file
	   $raw->read("$dir/$file");

	   // display file
	   for ($s = 0; $s < count($raw->sheets); $s++){
	   $data .= "<div class=\"entry\">";
	   $data .= "<table border=1>";
	       for ($i = 1; $i <= $raw->sheets[$s]['numRows']; $i++){
		   $data .= "<tr>";
	           for ($j = 1; $j <= $raw->sheets[$s]['numCols']; $j++){
		       $data .= "<td>";
		       $data .= $raw->sheets[$s]['cells'][$i][$j];
		       $data .= "</td>";
		   }
		   $data .= "</tr>";
	       }
	   $data .= "</table>";
	   $data .= "</div>";
	   $data .= "<p>";
	   }
       }

       // CASE 3: IMAGE FILE - make an HTML wrapper and display the image file
       else if (in_array($ext, $imageExtensions)){
           echo "<img src=\"./$dir/$file\">";
       }

       echo $data;
	   
       return $data;
       }
?>
      </div>

      <p>

      <!-- EXPERIMENTAL DIV - WYSIWYG HTML editing area -->
      <p>

      <!-- Div where you can create or append to a file -->
      <?php
      $auth_username = $_SERVER["REMOTE_USER"];
	  // To restrict access to editing the site, uncomment the following line and change the usernames accordingly
      //if (($auth_username == "admin1") || ($auth_username == "admin2"))
      {
		echo("<div class=\"entry\">");
		echo("<a href=\"javascript:toggleShow('entry')\">Edit Source</a>");
		echo("<span id=\"entry\" style=\"display: none;\">");
		echo("<p>");
		echo("<form action=\"process.php\" method=\"post\" name=\"inputRegion\">");
	       echo "Filename: <input type=\"text\" name=\"filename\" value=\"$file\"
		 style=\"width:70%;\"><br>";
  	       echo "Directory: <input type=\"text\" name=\"directory\" value=\"$dir\"
		 style=\"width:70%;\"><br>";
		 
	       echo "<textarea name=\"HTMLentry\" id=\"HTMLentry\" style=\"width:90%; height:600px;\" 
		 onkeydown=\"return catchTab(this,event)\">$data</textarea><br>";			 
	    
	       echo("<input type=\"submit\" value=\"Submit\">");
	       echo("<input type=\"button\" value=\"Clear\" onClick=\"clearForms()\"><br>");
	       echo("</form>");
	       echo("</span>");
	       echo("</div>");
      
	       echo("<p>");
      }
	?>
	
	<script type="text/javascript">
	// set up the WYSIWYG editor
	window.onload = function()
	{
		CKEDITOR.replace( 'HTMLentry' );
	};
	</script>

      <!-- Div where you can upload a file-->
	<?php   
	$auth_username = $_SERVER["REMOTE_USER"];
	// To restrict access to editing the site, uncomment the following line and change the usernames accordingly
    //if (($auth_username == "admin1") || ($auth_username == "admin2"))
	{
		echo("<div class = \"entry\">");
		echo("<a href=\"javascript:toggleShow('upload')\">Upload Files</a>");
		echo("<span id=\"upload\" style=\"display: none;\">");
		echo("<p>");
		echo("<form enctype=\"multipart/form-data\" action=\"upload.php\" method=\"post\" name=\"inputRegion\">");
	    echo("Choose a file to upload: <input name=\"uploadedFile\" type=\"file\"/><br/>");
	    echo("Choose a folder: <input name=\"folder\" type=\"text\" width=\"75%\"/><br>");

	    echo("<p>");
	    echo("<input type=\"submit\" value=\"Submit\" ><br>");
		echo("</form>");
		echo("</span>");
		echo("</div>");
		echo("<p>");
    }
    ?>
	<p>
	
		<!--- SEARCH --->
	<?php
	echo("<div class = \"searchbar\">");
	echo("<form enctype=\"multipart/form-data\" action=\"index.php?function=search\" method=\"post\" name=\"inputRegion\">");
	echo("Search: <input name=\"searchstring\" type=\"text\" width=\"75%\"/>");
	echo("<input type=\"submit\" value=\"Search\"><br>");
	echo("</form>");
	echo("</span>");
	echo("</div>");
	echo("<br>");
	?>
	<br>
      
    </font>
    <p></p>
  </body>
</html>
