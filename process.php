<? 

// get the data from the POST function

$fn = $_POST['filename'];
$dir = $_POST['directory'];
$entry = $_POST['HTMLentry'];

// check if folder exists. if doesn't exist, create folder
if (!is_dir($dir)){
  mkdir($dir);
}

// open file to append. if doesn't exist, create file
$fp = fopen("$dir/$fn", "w+");

// write the contents to the file
fwrite($fp, $entry);

// refresh the webpage
header('location:http://PUT/YOUR/LANDING/PAGE/URL/HERE');

?>