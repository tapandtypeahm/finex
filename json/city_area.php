<?php require_once '../lib/cg.php';
?>

<?php
if(isset($_GET['city_id']))
{
  $city_id=$_GET['city_id'];
      
$sql = "SELECT area_name FROM fin_city_area WHERE city_id=$city_id AND area_name LIKE '%".$_REQUEST['term']."%' 
       ";
$result=dbQuery($sql);
$resultArray=dbResultToArray($result);
foreach ($resultArray as $r) 
{
    $results[] = array('label' => $r['area_name']);
}


	
echo json_encode($results); 

   
}



?>