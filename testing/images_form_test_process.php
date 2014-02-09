<?php

print_r($_FILES['file']['tmp_name'][0]);

  echo "Upload: " . $_FILES["file"][1]["name"] . "<br>";
  echo "Type: " . $_FILES["file"][1]["type"] . "<br>";
  echo "Size: " . ($_FILES["file"][1]["size"] / 1024) . " kB<br>";
  echo "Stored in: " . $_FILES["file"][1]["tmp_name"];
  
?>

