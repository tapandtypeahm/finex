<?php
require_once "../../lib/cg.php";
require_once "../../lib/bd.php";
require_once "../../lib/common.php";
require_once "../../lib/adminuser-functions.php";

if(isset($_SESSION['adminSession']['admin_rights']))
$admin_rights=$_SESSION['adminSession']['admin_rights'];

if(isset($_GET['view']))
{
	if($_GET['view']=='add')
	{
		$content="changePassword.php";
	}
	else if($_GET['view']=='details')
	{
		$content="changePassword.php";
		}
	else
	{
		$content="changePassword.php";
	}	
}
else
{
		$content="changePassword.php";
}		
if(isset($_GET['action']))
{
	if($_GET['action']=='change')
	{
		if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(2,$admin_rights) || in_array(7,$admin_rights)))
			{
			
				$result=changePassword($_POST["id"],$_POST['oldPassword'],$_POST['newPassword']);
				
				if($result=="success")
				{
				$_SESSION['ack']['msg']="Password successfully changed!";
				$_SESSION['ack']['type']=1; // 1 for insert
				}
				else{
					
				$_SESSION['ack']['msg']="Unable to change Password!";
				$_SESSION['ack']['type']=4; // 4 for error
				}
				
				header("Location: ".$_SERVER['PHP_SELF']);
				exit;
			}
			else
			{	
					$_SESSION['ack']['msg']="Authentication Failed! Not enough access rights!";
					$_SESSION['ack']['type']=5; // 5 for access
					header("Location: ".$_SERVER['PHP_SELF']);
			exit;
			}
		}
			
	}
?>

<?php

$pathLinks=array("Home","Registration Form","Manage Locations");
$selectedLink="settings";
$jsArray=array("jquery.validate.js","jquery-ui/js/jquery-ui.min.js","validators/chagePassword.js");
$cssArray=array("jquery-ui.css");
require_once "../../inc/template.php";
 ?>