<? 

// get the data from the POST function

$file = $_POST['uploadedFile'];
$dir = $_POST['folder'];

if (!is_dir($dir)){
  mkdir($dir);
}

$targetPath = "$dir/".basename($_FILES['uploadedFile']['name']);

if (move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $targetPath)){
   // refresh the webpage
   header('location:http://PUT/YOUR/LANDING/PAGE/URL/HERE');
}

else {
   echo "Tried to upload ".$file." to ".$dir."<p>";
   echo "There was a problem uploading. Please go back and try again.";
}

?>