<?php
require_once "../../../../lib/cg.php";
require_once "../../../../lib/bd.php";
require_once "../../../../lib/adminuser-functions.php";

if(isset($_SESSION['adminSession']['admin_rights']))
$admin_rights=$_SESSION['adminSession']['admin_rights'];

if((in_array(4,$admin_rights) || in_array(7,$admin_rights)))
{}
else
{
	header("Location: ".WEB_ROOT."admin/settings");
	exit;
	}

if(isset($_GET['view']))
{
	if($_GET['view']=='add')
	{
		$content="list_add.php";
	}
	else if($_GET['view']=='details')
	{
		$content="details.php";
		}
	else if($_GET['view']=='edit')
	{
		$content="edit.php";
		}
	else
	{
		$content="list_add.php";
	}	
}
else
{
		$content="list_add.php";
}		
if(isset($_GET['action']))
{
	if($_GET['action']=='add')
	{
		$result=insertAdminUser($_POST["name"], $_POST["username"], $_POST["password"], $_POST["email"], $_POST["right"]);
		
		if($result=="success")
			{
			$_SESSION['ack']['msg']="User successfully added!";
			$_SESSION['ack']['type']=1; // 1 for insert
			}
			else{
				
			$_SESSION['ack']['msg']="Invalid Input OR Duplicate Entry!";
			$_SESSION['ack']['type']=4; // 4 for error
			}
		
		header("Location: ".$_SERVER['PHP_SELF']);
		exit;
		}
	if($_GET['action']=='delete')
	{
		$result=deleteAdminUser($_GET["lid"]);
		if($result=="success")
			{
			$_SESSION['ack']['msg']="User deleted Successfuly!";
		$_SESSION['ack']['type']=3; // 3 for delete
			}
			else if($result=="error"){
				
			$_SESSION['ack']['msg']="Cannot Delete User! Minimum One User is Required!";
			$_SESSION['ack']['type']=4; // 4 for error
			}
			else if($result=="error1"){
				
			$_SESSION['ack']['msg']="Cannot Delete User! Self Delete is Not Possible!";
			$_SESSION['ack']['type']=4; // 4 for error
			}
		
		header("Location: ".$_SERVER['PHP_SELF']);
		exit;
		}
	if($_GET['action']=='edit')
	{
		updateAccessRightsForUser($_POST["lid"],$_POST["right"]);
		$_SESSION['ack']['msg']="User updated Successfuly!";
		$_SESSION['ack']['type']=2; // 2 for update
		header("Location: ".$_SERVER['PHP_SELF']);
		exit;
		}			
	}
?>

<?php

$pathLinks=array("Home","Registration Form","Manage Locations");
$selectedLink="settings";
$jsArray=array("jquery.validate.js","validators/adminuser.js");
require_once "../../../../inc/template.php";
 ?>