<?php
require_once "lib/cg.php";
require_once "lib/bd.php"; 
require_once "lib/our-company-function.php"; 
$jsArray=array("js/formSubmit.js");
?>
<?php
$companies=listOurCompaniesNames();
if(isset($_SESSION['adminSession']['admin_id']))
{
	header("Location: ".WEB_ROOT."admin/index.php");
}
require_once "lib/adminuser-functions.php";
if(isset($_GET['action'])){
	if($_GET['action']=="login")
	{
		$username=$_POST['username'];
		$password=$_POST['password'];
		$oc_id=$_POST['ourcompany_id'];
		loginAdmin($username,$password,$oc_id);
	}
	}
 ?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Finex-Admin Section</title>
<link rel="stylesheet" href="<?php echo WEB_ROOT ?>css/adminMain.css" />
<link rel="stylesheet" href="<?php echo WEB_ROOT ?>css/login.css" />
<link rel="stylesheet" href="<?php echo WEB_ROOT ?>css/bp.css" />

</head>

<body onload="document.getElementById('username').focus();">
	<div class="headerBackground"></div>
	<div id="mainDiv">
		<div id="header">
			<a href="<?php echo WEB_ROOT."admin"; ?>"><div id="logo">Finex</div></a>
				<div id="navigation">
            		<ul id="nav">
                	</ul>
				</div>
    	</div>
<div id="content">
<div class="warning">
<?php if(isset($_SESSION["login_error"]))
{
	$warning=$_SESSION["login_error"]["invalid_login"];
	 ?>
<div class="alert alert-error">
<?php echo $warning; ?>
</div>
<?php
}
 ?>
</div>
		<div id="loginWrapper" class="wrapper">
        <h4>Sign in</h4>
        	<form class="form-horizontal" name="loginForm" action="<?php echo WEB_ROOT ?>lib/adminuser-functions.php?action=login" method="post">
  <div class="control-group">
    <label class="control-label" for="ourCompanySelect">Select Company</label>
    <div class="controls">
      <select id="ourCompanySelect" name="ourcompany_id">
      	<?php foreach($companies as $company)
		{
			?>
            <option value="<?php echo $company['our_company_id']; ?>"><?php echo $company['our_company_name'] ?></option>
            <?php
			} ?>
      </select>
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputUsername">Username</label>
    <div class="controls">
      <input type="text" id="inputUsername" placeholder="Username" name="username" autocomplete="off" autofocus="autofocus">
    </div>
  </div>
  <div class="control-group">
    <label class="control-label" for="inputPassword">Password</label>
    <div class="controls">
      <input type="password" id="inputPassword" placeholder="Password" name="password" autocomplete="off">
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <button type="submit" class="btn btn-primary">Sign in</button>
    </div>
  </div>
</form>
        </div> 
 
 <?php 
 require_once "inc/footer.php";
 ?>