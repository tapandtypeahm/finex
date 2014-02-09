<?php
require_once('cg.php');
require_once('bd.php');
require_once('common.php');

if(isset($_SESSION['adminSession']['admin_rights']))
{
	$admin_rights=$_SESSION['adminSession']['admin_rights'];
	
}
if(isset($_GET['action']))
{
if($_GET['action']=='listAdmin')
		{
				if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(1,$admin_rights) || in_array(7,					$admin_rights)))
				{	
							listAdminUsers();
				}
				else
				{
				$_SESSION['error']['submit_error']="Authentication Failed! Not enough access rights!";
				}
				
		}
		else if($_GET['action']=='insertAdmin')
		{
				if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(2,$admin_rights) || in_array(7,					$admin_rights)))
				{	
							insertAdminUser($_POST["name"], $_POST["username"], $_POST["password"], $_POST["email"], $_POST["access"]);
				}
				else
				{
			$_SESSION['error']['submit_error']="Authentication Failed! Not enough access rights!";
				}
				
		}
		else if($_GET['action']=="updateAdmin")
		{
			if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(3,$admin_rights) || in_array(7,					$admin_rights)))
				{	
					updateAdminUser($_POST["name"], $_POST["username"], $_POST["email"], $_POST["access"]);
				}
				else
				{
					$_SESSION['error']['submit_error']="Authentication Failed! Not enough access rights!";
				}
		}	
		else if($_GET['action']=="deleteAdmin")
		{
			if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(4,$admin_rights) || in_array(7,					$admin_rights)))
				{	
					deleteAdminUser($_POST["id"]);
				}
				else
				{
					$_SESSION['error']['submit_error']="Authentication Failed! Not enough access rights!";
				}
		}	
		else if($_GET['action']=="login")
		{
			$oc_id=$_POST['ourcompany_id'];
			loginAdmin($_POST["username"],$_POST["password"],$oc_id);
			}
		else if($_GET['action']=="logout")
		{
			logoutAdmin();
		}	
		else if($_GET['action']=="changePassword")
		{
			changePassword();
		}	
		else if($_GET['action']=="resetPassword")
		{
			resetPassword();
		}		
}
function listAdminUsers()

{
		   $sql = "SELECT * 
		   		   FROM fin_admin 
		   			WHERE is_active=1";  
		   $result = dbQuery($sql);
		   $resultArray = dbResultToArray($result);
		   return $resultArray;
}

function insertAdminUser($name, $username, $pass, $email, $access)
{
	try{
		
		
			 $algo = '2a';  
			// cost parameter  
			 $cost = '10';  
			// mainly for internal use 
				
			 function unique_salt() {  
				return substr(sha1(mt_rand()),0,22);  
				}  
			$name=clean_data($name);
			$pass=clean_data($pass);
			$username=clean_data($username);
			$email=clean_data($email);
			if(validateForNull($name,$pass,$email,$username) && !checkDuplicateAdminUser($username,$email))
			{
			if($access==null || empty($access))
			$access=array(1);	
			else
			$access[]=1;
			$email=trim($email);
			$name=trim($name);
			$uniqueHash=$algo.$cost.unique_salt();	
			
			$safePassword=crypt($pass , $uniqueHash); 
			
			
			$sql = "INSERT INTO 
					fin_admin 
					(admin_email, admin_name, admin_username , admin_password, last_login, date_added, date_modified, admin_hash, is_active) 
					VALUES 
					('$email', '$name', '$username', '$safePassword', NOW(), NOW(), NOW(), '$uniqueHash', 1)";
			$result = dbQuery($sql); 
			$admin_id=dbInsertId();
			
			insertAccessRightsForAdmin($admin_id,$access);
			
			
			return "success";
			
		}
		else
		{
			return "error";	
		}
		
	}
	catch(Exception $e)
	{
		return "error";
		exit;
	}
	
}

function insertAccessRightsForAdmin($admin_id,$access)
{
	if(checkForNumeric($admin_id))
	{
	if(is_array($access) && !empty($access))
	{
		foreach($access as $right)
		{
					
					$sql="INSERT INTO
						  fin_rel_admin_right(admin_id,admin_right_id)
						  VALUES
						  ($admin_id,$right)";
					$result=dbQuery($sql);	  
		}	
	}
	else if($access!=null && $access!="")
	{
		$sql="INSERT INTO
						  fin_rel_admin_right(admin_id,admin_right_id)
						  VALUES
						  ($admin_id,$access)";
					$result=dbQuery($sql);
		
		}
	}
}

function deleteAccessRightsForAdminWithoutRead($admin_id)
{
	if(checkForNumeric($admin_id))
	{
	$sql="DELETE FROM fin_rel_admin_right
			WHERE admin_id=$admin_id
			AND admin_right_id!=1";
	dbQuery($sql);	
	}
}

function deleteAdminUser($id)
{
	$admins=getAllActiveAdmin();
	
	if($admins>1 && $_SESSION['adminSession']['admin_id']!=$id)
	{
	$sql = "UPDATE fin_admin
			SET is_active=0 
			WHERE admin_id = '$id'";
	$result = dbQuery($sql);
	return "success";	
	}
	else if($admins==1)
	return "error";
	else
	return "error1";
	
}

function getAllActiveAdmin()
{
	$sql="SELECT count(admin_id) FROM fin_admin WHERE is_active=1";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(isset($resultArray[0][0]))
	return $resultArray[0][0];
	else return 0;
	}

function loginAdmin($username,$password,$oc_id=1){
	
	
	if(isset($_SESSION["login_error"]))	
	{
	unset($_SESSION["login_error"]);	
	}
	
	$username=clean_data($username);
	$password=clean_data($password);
	$sql="SELECT 
		  admin_id, admin_hash, admin_name, admin_password
		  FROM 
		  fin_admin
		  WHERE 
		  admin_username='$username'
		  AND is_active=1";
	$result=dbQuery($sql);
	$adminArray=dbResultToArray($result);
	$result=dbQuery($sql);
	
	$all=getAllActiveAdmin();
	if($all==0)
	{
		$_SESSION["login_error"]["invalid_login"]="LICENCE EXPIRED! CALL 09824143009 OR 09428592016!";
		  header("Location: ".WEB_ROOT."login.php");
			exit;
		
		}
	if(dbNumRows($result)>0)
	{
		
		$admin=$adminArray[0];
		$admin_id=$admin['admin_id'];
		$admin_name=$admin['admin_name'];
		$admin_hash=$admin['admin_hash'];
		$admin_pass=$admin['admin_password'];
		
		$Password=crypt($password,$admin_hash); 
		
		$resultt=strcasecmp($admin_pass,$Password); /* returns 0 if both string are equal */
		
		if($resultt==0)
		{
		
		$_SESSION['adminSession']['admin_name']=$adminArray[0]['admin_name'];
		$_SESSION['adminSession']['admin_id']=$adminArray[0]['admin_id'];
		$_SESSION['adminSession']['admin_rights']=getAdminRightsForAdminId($admin_id);
		$_SESSION['adminSession']['report_rights']=getReportRightsForAdminId($admin_id);
	
		$_SESSION['adminSession']['admin_logged_in']=true;
		$_SESSION['adminSession']['reset_date']=getRasidResetDateAgnecy();
	    $_SESSION['adminSession']['oc_id']=$oc_id;
	   
	
		$sql="UPDATE 
		      fin_admin
			  SET 
			  last_login=NOW()
			  WHERE admin_id=$admin_id";
		$result=dbQuery($sql);	 
		if(isset($_GET['r']))
		{
		$return_url=$_GET['r'];	
		header("Location: ".$return_url);
		exit;
			}
		
		header("Location: ".WEB_ROOT."admin/");
		exit;
		}
		else
		{
			$_SESSION["login_error"]["invalid_login"]="INCORRECT USERNAME/PASSWORD. PLEASE TRY AGAIN.";
		    header("Location: ".WEB_ROOT."login.php");
			exit;
		}
	}
	else
	{
		$_SESSION["login_error"]["invalid_login"]="INCORRECT USERNAME/PASSWORD. PLEASE TRY AGAIN.";
		header("Location: ".WEB_ROOT."login.php");
		exit;
	}	
} 

function logoutAdmin(){
	session_destroy();
	header("Location: ".WEB_ROOT);
	exit;
}

function getAdminRightsDetailsForAdminId($id)
{
	$sql="SELECT fin_rel_admin_right.admin_right_id,admin_right
		      FROM fin_rel_admin_right,fin_admin_right
			  WHERE admin_id=$id
			  AND fin_rel_admin_right.admin_right_id=fin_admin_right.admin_right_id";
		$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		return $resultArray;	  
	}
	
function getAdminRightsForAdminId($id){
	
	try{
		$sql="SELECT admin_right_id
		      FROM fin_rel_admin_right
			  WHERE admin_id=$id";
		$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		$returnArray=array();
		foreach($resultArray as $adminRight)
		{
			$returnArray[]=$adminRight['admin_right_id'];
			
		}
		return $returnArray;
		}
	catch(Exception $e)
	{}
	
	}	
function getReportRightsForAdminId($id){
	
	try{
		$sql="SELECT admin_right_id
		      FROM fin_rel_admin_right
			  WHERE admin_id=$id
			  AND admin_right_id>100";
		$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		$returnArray=array();
		foreach($resultArray as $adminRight)
		{
			$returnArray[]=$adminRight['admin_right_id'];
			
		}
		return $returnArray;
		}
	catch(Exception $e)
	{}
	
	}		
function getAllAdminRights()
{
	try{
		$sql="SELECT admin_right_id, admin_right
		      FROM fin_admin_right WHERE admin_right_id<100";
		$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		
		return $resultArray;
		}
	catch(Exception $e)
	{}
}

function getAllAdminReportRights()
{
	try{
		$sql="SELECT admin_right_id, admin_right
		      FROM fin_admin_right WHERE admin_right_id>100";
		$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		
		return $resultArray;
		}
	catch(Exception $e)
	{}
}

function checkDuplicateAdminUser($username,$email)
{
	$sql="SELECT 
		  admin_id
		  FROM 
		  fin_admin
		  WHERE 
		  (admin_username='$username'
		  OR admin_email='email')
		  AND is_active=1
		  LIMIT 0, 1";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);	  
	if(dbNumRows($result)>0)
	return $resultArray[0][0];
	else
	return false;
	}

function getAdminUserByID($id)
{
	if(checkForNumeric($id))
	{
	$sql="SELECT fin_admin.admin_id,admin_name,admin_username,admin_email,last_login,date_added
	      FROM fin_admin,fin_rel_admin_right
		  WHERE fin_admin.admin_id=fin_rel_admin_right.admin_id
		  AND fin_admin.admin_id=$id
		  AND is_active=1";
	$result=dbQuery($sql);	
	$resultArray=dbResultToArray($result);
	return $resultArray[0];
	}
}

function getAdminUserNameByID($id)
{
	if(checkForNumeric($id))
	{
	$sql="SELECT admin_name
	      FROM fin_admin
		  WHERE  fin_admin.admin_id=$id
		  AND is_active=1";
	$result=dbQuery($sql);	
	$resultArray=dbResultToArray($result);
	return $resultArray[0][0];
	}
}

function updateAccessRightsForUser($id,$access)
{
	deleteAccessRightsForAdminWithoutRead($id);
	insertAccessRightsForAdmin($id,$access);
	}
	
function checkPasswordForDeletion($id,$password)
{
	
	$sql="SELECT 
		  admin_hash, admin_password
		  FROM 
		  fin_admin
		  WHERE 
		  admin_id=$id
		  AND is_active=1";
	$result=dbQuery($sql);
	$adminArray=dbResultToArray($result);
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		
		$admin=$adminArray[0];
		$admin_hash=$admin['admin_hash'];
		$admin_pass=$admin['admin_password'];
		
		$Password=crypt($password,$admin_hash); 
		
		$resultt=strcasecmp($admin_pass,$Password); /* returns 0 if both string are equal */
		
		if($resultt==0)
		{
		return "success";
		}
		else
		return "error";
	}
	
	}	
function changePassword($id,$oldpassword,$newPassword)
{
	if(checkForNumeric($id) && validateForNull($oldpassword,$newPassword))
	{
		$oldpassword=clean_data($oldpassword);
		$newPassword=clean_data($newPassword);
		$admin_hash=getHashForAdmin($id);
		if(validateForNull($admin_hash) && checkPasswordForDeletion($id,$oldpassword)=="success")
		{
		
		$safePassword=crypt($newPassword , $admin_hash); 	
		
		$sql="UPDATE  fin_admin SET admin_password='$safePassword' WHERE admin_id=$id";
		dbQuery($sql);
		return "success";
		}
	}
	return "error";
	
}	
function getHashForAdmin($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT 
		  admin_hash
		  FROM 
		  fin_admin
		  WHERE 
		  admin_id=$id
		  AND is_active=1";
	$result=dbQuery($sql);
	$adminArray=dbResultToArray($result);
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		
	 return $adminArray[0][0];
		
		
		
		}
	else return null;	
	}
}	
?>