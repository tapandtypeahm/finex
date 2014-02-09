<?php
require_once "../../lib/cg.php";
require_once "../../lib/bd.php";

if(isset($_SESSION['adminSession']['admin_rights']))
$admin_rights=$_SESSION['adminSession']['admin_rights'];


	$content="list.php";
	
$pathLinks=array("Home","Registration Form");
$selectedLink="reports";
require_once "../../inc/template.php";	
 ?>


 