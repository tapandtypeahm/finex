<?php
require_once "../../../../lib/cg.php";
require_once "../../../../lib/bd.php";
require_once "../../../../lib/bank-functions.php";
if($_POST["branch"]=="" || $_POST["branch"]==null)
{
	$branch=getBranchhById($_POST["lid"]);
	echo $branch;
	}
else{
updateBranch($_POST["lid"],$_POST["branch"]);
echo $_POST["branch"];
}

?>