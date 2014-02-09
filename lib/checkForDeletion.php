<?php require_once('cg.php');
require_once('bd.php');
require_once('adminuser-functions.php');

$admin_id=$_SESSION['adminSession']['admin_id'];
$password=$_POST['p'];
$delLink=$_POST['delLink'];
$result=checkPasswordForDeletion($admin_id,$password);

if($result=="success")
{
	header("Location: ".$delLink);
	exit;
	}
else if($result=="error")
{
	$_SESSION['ack']['msg']="Authentication Failed! Incorrect Password!";
					$_SESSION['ack']['type']=5; // 5 for access
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
	}
 ?>