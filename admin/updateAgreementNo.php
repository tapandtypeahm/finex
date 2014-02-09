<?php
require_once "../lib/cg.php";
require_once "../lib/bd.php";
require_once "../lib/file-functions.php";


?>

<?php 


if(isset($_GET['action']))
{
	if($_GET['action']=='add')
	{
		$agreement_nos_array = $_POST['agreement_no'];
		$file_id_array = $_POST['file_id'];
		
		updateAgreementNosForFileIds($file_id_array, $agreement_nos_array);
		
	}
	}
	
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title> Update Agreement No. </title>



</head>

<body>

<h3> Update Agreement Numbers of Shri Ram Files</h3>

<form name="updateAgreementNo"  action="<?php echo $_SERVER['PHP_SELF'].'?action=add'; ?>" method="post">

 <table>

<?php
    $list = updateAgreementNos();
	
	foreach($list as $l)
	  {
	 ?>
    
     
     <tr>
     <td> File Number : </td> 
	 <td> <?php echo $l['file_number'] ?> </td> 
     </tr>
     
     <tr>
     <td> File Agreement No : </td>
     <td> <input type="text" value="<?php echo $l['file_agreement_no'] ?>" name="agreement_no[]" /> </td>
     </tr>
     
     <tr>
     <td></td>
     <td><input type="hidden" value="<?php echo $l['file_id'] ?>"  name="file_id[]"/> </td>
     </tr>
     
     <?php } ?>
      
 	 <tr>
     <td></td>
     <td><input type="submit" value="Update All Files"/></td></tr>
</table>
 </form>
</div>
</body>
</html>