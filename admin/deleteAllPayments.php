<?php
require_once('../lib/cg.php');
require_once('../lib/bd.php');
require_once('../lib/loan-functions.php');
$file_id=$_POST['file_id'];
deleteAllPaymentsForFile($file_id);
 ?>
 <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
 <input type="text" name="file_id" />
 <input type="submit"/>
 </form>