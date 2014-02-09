<?php
require_once "../../lib/cg.php";
require_once "../../lib/bd.php";
require_once "../../lib/agency-functions.php";
require_once "../../lib/our-company-function.php";


$id=$_GET['p'];

$type=substr($id,0,2);
$id=substr($id,2);
if($type=="ag")
{
$prefix=getAgencyPrefixFromAgencyId($id);
}
else if($type=="oc")
{
$prefix=getPrefixFromOCId($id);
}
if($type=="ag")
$company_type=2;
else
$company_type=1;

echo "new Array('"."$prefix'".","."'$company_type'".")";

?>