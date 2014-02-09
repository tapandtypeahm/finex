<?php
require_once "../../../../lib/cg.php";
require_once "../../../../lib/bd.php";
require_once "../../../../lib/common.php";
require_once "../../../../lib/broker-functions.php";
require_once "../../../../lib/customer-functions.php";
require_once "../../../../lib/loan-functions.php";
require_once "../../../../lib/guarantor-functions.php";
require_once "../../../../lib/agency-functions.php";
require_once "../../../../lib/our-company-function.php";
require_once "../../../../lib/file-functions.php";
require_once "../../../../lib/vehicle-functions.php";
require_once "../../../../lib/vehicle-insurance-functions.php";
require_once "../../../../lib/vehicle-company-functions.php";
require_once "../../../../lib/insurance-company-functions.php";
require_once "../../../../lib/vehicle-dealer-functions.php";
require_once "../../../../lib/vehicle-model-functions.php";
require_once "../../../../lib/vehicle-type-functions.php";
require_once "../../../../lib/report-functions.php";
require_once "../../../../lib/area-functions.php";


if(isset($_SESSION['adminSession']['admin_rights']))
$admin_rights=$_SESSION['adminSession']['admin_rights'];

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
	if($_GET['action']=='generateReport')
	{
		if(isset($_POST['start_date']))
		{
		$from=$_POST['start_date'];
		}
		else
		$from=null;
		
		if(isset($_POST['end_date']))
		{
		$to=$_POST['end_date'];
		}
		else
		$to=null;	
		
		if(isset($_POST['win_gt']))
		{
		$win_gt=$_POST['win_gt'];
		}
		else
		$win_gt=null;
		
		if(isset($_POST['win_lt']))
		{
		$win_lt=$_POST['win_lt'];
		}
		else
		$win_lt=null;
		
		if(isset($_POST['emi_gt']))
		{
		$emi_gt=$_POST['emi_gt'];
		}
		else
		$emi_gt=null;
		
		if(isset($_POST['emi_lt']))
		{
		$emi_lt=$_POST['emi_lt'];
		}
		else
		$emi_lt=null;
		
		if(isset($_POST['balance_gt']))
		{
		$balance_gt=$_POST['balance_gt'];
		}
		else
		$balance_gt=null;
		
		if(isset($_POST['balance_lt']))
		{
		$balance_lt=$_POST['balance_lt'];
		}
		else
		$balance_lt=null;
		
		if(isset($_POST['city_id']))
		{
		$city_id=$_POST['city_id'];
		}
		else
		$city_id=null;
		
		if(isset($_POST['area']))
		{
			
		$area_array=$_POST['area'];
		
		$area_id_string=implode(',',$area_array);
		}
		else
		$area_id_string=null;
		
		if(isset($_POST['broker']))
		{
			
		$broker_array=$_POST['broker'];
		
		$broker_id_string=implode(',',$broker_array);
		}
		else
		$broker_id_string=null;
		
		if(isset($_POST['agency_id']))
		{
			
		$agency_id=$_POST['agency_id'];
		}
		else
		$agency_id=null;
		
		if(isset($_POST['file_status']))
		{
			
		$file_status=$_POST['file_status'];
		}
		else
		$file_status=null;
		
			if($area_id_string==false)
		$area_id_string=null;
		
		if($city_id==-1)
		$city_id=null;
		
		
		$reportArray=generalEMIReports($from,$to,$win_gt,$win_lt,$emi_gt,$emi_lt,$balance_gt,$balance_lt,$city_id,$area_id_string,$file_status,$agency_id,$broker_id_string);
		
		$_SESSION['cCustomReport']['emi_array']=$reportArray;
		
		$_SESSION['cCustomReport']['from']=$from;
		$_SESSION['cCustomReport']['to']=$to;
		$_SESSION['cCustomReport']['win_gt']=$win_gt;
		$_SESSION['cCustomReport']['win_lt']=$win_lt;
		$_SESSION['cCustomReport']['emi_gt']=$emi_gt;
		$_SESSION['cCustomReport']['emi_lt']=$emi_lt;
		$_SESSION['cCustomReport']['balance_gt']=$balance_gt;
		$_SESSION['cCustomReport']['balance_lt']=$balance_lt;
		$_SESSION['cCustomReport']['city_id']=$city_id;
		$_SESSION['cCustomReport']['area_id']=$area;
		$_SESSION['cCustomReport']['area_id_array']=$area_array;
		$_SESSION['cCustomReport']['broker_id_array']=$broker_array;
		$_SESSION['cCustomReport']['agency_id']=$agency_id;
		$_SESSION['cCustomReport']['file_status']=$file_status;
		$_SESSION['cCustomReport']['fields']=$_POST['fields'];
	
		header("Location: index.php");		
		exit;
	}
	else if($_GET['action']=='fromHomeUpcoming')
	{
		if(isset($_POST['start_date']))
		{
		$from=$_POST['start_date'];
		}
		else
		$from=date('d/m/Y');
		
		if(isset($_POST['end_date']))
		{
		$to=$_POST['end_date'];
		}
		else
		$to=null;	
		
		if(isset($_POST['win']))
		{
		$win=$_POST['win'];
		}
		else
		$win=null;
		
		if(isset($_POST['city_id']))
		{
		$city_id=$_POST['city_id'];
		}
		else
		$city_id=null;
		
		if(isset($_POST['area']))
		{
			
		$area=$_POST['area'];
		$area_id=getAreaIdFromName($area);
		}
		else
		$area_id=null;
		
		if(isset($_POST['area']))
		{
			
		$agency_id=$_POST['agency_id'];
		}
		else
		$agency_id=null;
		
		if(isset($_POST['file_status']))
		{
			
		$file_status=$_POST['file_status'];
		}
		else
		$file_status=null;
		
		if($area_id==false)
		$area_id=null;
		
		if($city_id==-1)
		$city_id=null;
		
		
		
		$reportArray=generalEMIReports($from,$to,$win,$city_id,$area_id,$file_status,$agency_id);
		
		$_SESSION['cCustomReport']['emi_array']=$reportArray;
		
		$_SESSION['cCustomReport']['from']=$from;
		$_SESSION['cCustomReport']['to']=$to;
		$_SESSION['cCustomReport']['win']=$win;
		$_SESSION['cCustomReport']['city_id']=$city_id;
		$_SESSION['cCustomReport']['area_id']=$area;
		$_SESSION['cCustomReport']['agency_id']=$agency_id;
		$_SESSION['cCustomReport']['file_status']=$file_status;
		
	
		header("Location: index.php");		
		exit;
	}
	else if($_GET['action']=='fromHomeExpiredOpen')
	{
		if(isset($_POST['start_date']))
		{
		$from=$_POST['start_date'];
		}
		else
		$from=null;
		
		if(isset($_POST['end_date']))
		{
		$to=$_POST['end_date'];
		}
		else
		$to=date('Y-m-d');	
		
		if(isset($_POST['win']))
		{
		$win=$_POST['win'];
		}
		else
		$win=1;
		
		if(isset($_POST['city_id']))
		{
		$city_id=$_POST['city_id'];
		}
		else
		$city_id=null;
		
		if(isset($_POST['area']))
		{
			
		$area=$_POST['area'];
		$area_id=getAreaIdFromName($area);
		}
		else
		$area_id=null;
		
		if(isset($_POST['area']))
		{
			
		$agency_id=$_POST['agency_id'];
		}
		else
		$agency_id=null;
		
		if(isset($_POST['file_status']))
		{
			
		$file_status=$_POST['file_status'];
		}
		else
		$file_status=1;
		
		if($area_id==false)
		$area_id=null;
		
		if($city_id==-1)
		$city_id=null;
		
		
		
		$reportArray=generalEMIReports($from,$to,$win,$city_id,$area_id,$file_status,$agency_id);
		
		$_SESSION['cCustomReport']['emi_array']=$reportArray;
		
		$_SESSION['cCustomReport']['from']=$from;
		$_SESSION['cCustomReport']['to']=$to;
		$_SESSION['cCustomReport']['win']=$win;
		$_SESSION['cCustomReport']['city_id']=$city_id;
		$_SESSION['cCustomReport']['area_id']=$area;
		$_SESSION['cCustomReport']['agency_id']=$agency_id;
		$_SESSION['cCustomReport']['file_status']=$file_status;
	
		header("Location: index.php");		
		exit;
	}
	else if($_GET['action']=='fromHomeExpiredClosed')
	{
		if(isset($_POST['start_date']))
		{
		$from=$_POST['start_date'];
		}
		else
		$from=null;
		
		if(isset($_POST['end_date']))
		{
		$to=$_POST['end_date'];
		}
		else
		$to=date('Y-m-d');	
		
		if(isset($_POST['win']))
		{
		$win=$_POST['win'];
		}
		else
		$win=1;
		
		if(isset($_POST['city_id']))
		{
		$city_id=$_POST['city_id'];
		}
		else
		$city_id=null;
		
		if(isset($_POST['area']))
		{
			
		$area=$_POST['area'];
		$area_id=getAreaIdFromName($area);
		}
		else
		$area_id=null;
		
		if(isset($_POST['area']))
		{
			
		$agency_id=$_POST['agency_id'];
		}
		else
		$agency_id=null;
		
		if(isset($_POST['file_status']))
		{	
		$file_status=$_POST['file_status'];
		}
		else
		$file_status=2;
		
		if($area_id_string==false)
		$area_id_string=null;
		
		if($city_id==-1)
		$city_id=null;
		
		
		
		$reportArray=generalEMIReports($from,$to,$win,$city_id,$area_id_string,2,$agency_id);
		
		$_SESSION['cCustomReport']['emi_array']=$reportArray;
		
		$_SESSION['cCustomReport']['from']=$from;
		$_SESSION['cCustomReport']['to']=$to;
		$_SESSION['cCustomReport']['win']=$win;
		$_SESSION['cCustomReport']['city_id']=$city_id;
		$_SESSION['cCustomReport']['area_id_array']=$area_array;
		$_SESSION['cCustomReport']['agency_id']=$agency_id;
		$_SESSION['cCustomReport']['file_status']=2;
	
		header("Location: index.php");		
		exit;
	}
}
?>

<?php

$pathLinks=array("Home","Registration Form","Manage Locations");
$selectedLink="reports";
$jsArray=array("jquery.validate.js", "dropDown.js", "jquery-ui/js/jquery-ui.min.js","validators/generalEMIReports.js","customerDatePicker.js");
$cssArray=array("jquery-ui.css");
require_once "../../../../inc/template.php";
 ?>