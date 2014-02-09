<?php
require_once "../../../../lib/cg.php";
require_once "../../../../lib/bd.php";
require_once "../../../../lib/account-head-functions.php";
if($_POST["subhead"]=="" || $_POST["subhead"]==null)
{
	$branch=getHeadById($_POST["lid"]);
	echo $branch['head_name'];
	}
else{
$head=getHeadById($_POST["lid"]);	
$parent_id=$head['parent_id'];
updateHead($_POST["lid"],$_POST["subhead"],$parent_id);
echo $_POST["subhead"];
}

?>