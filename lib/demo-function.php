<?php 
require_once("cg.php");
require_once("city-functions.php");
require_once("common.php");
require_once("bd.php");

if(isset($_SESSION['adminSession']['admin_rights']))
{
	$admin_rights=$_SESSION['adminSession']['admin_rights'];
	}


if(!isset($_GET['action']))
{
	$_GET['action']='listFiles';
}

		if($_GET['action']=='listFiles')
		{
				if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(1,$admin_rights) || in_array(7,					$admin_rights)))
				{	
							listFiles();
				}
				else
				{
				$_SESSION['error']['submit_error']="Authentication Failed! Not enough access rights!";
				}
				
		}
		else if($_GET['action']=='insertFile')
		{
				if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(2,$admin_rights) || in_array(7,					$admin_rights)))
				{	
							insertFile($_POST["name"], $_POST["address"], $_POST["pincode"], $_POST["city_id"], $_POST["company_prefix"], $_POST["contact_no"]);
				}
				else
				{
			$_SESSION['error']['submit_error']="Authentication Failed! Not enough access rights!";
				}
				
		}
		else if($_GET['action']=="updateFile")
		{
			if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,					$admin_rights)))
				{	
					updateFile($_POST["id"], $_POST["name"], $_POST["address"], $_POST["pincode"], $_POST["city_id"], $_POST["company_prefix"], $_POST["contact_no"]);
				}
				else
				{
					$_SESSION['error']['submit_error']="Authentication Failed! Not enough access rights!";
				}
		}	
		else if($_GET['action']=="deleteFile")
		{
			if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
				{	
					deleteFile($_POST["id"]);
				}
				else
				{
					$_SESSION['error']['submit_error']="Authentication Failed! Not enough access rights!";
				}
		}	
		
function listFiles(){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}	

function insertFile(){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}	

function deleteFile($id){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}	

function updateFile(){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}	

function getFileById($id){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}		
?>		