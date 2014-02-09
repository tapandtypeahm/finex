<?php 
require_once("cg.php");
require_once("bd.php");
require_once("common.php");
		
function listAreas(){
	
	try
	{
		$sql="SELECT area_id,area_name,city_id
		      FROM fin_city_area";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		return $resultArray; 
	}
	catch(Exception $e)
	{
	}
}

function listAreasAlpha(){
	
	try
	{
		$sql="SELECT area_id,city_id,area_name
		      FROM fin_city_area
			  ORDER BY area_name";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		return $resultArray; 
	}
	catch(Exception $e)
	{
	}
}

function listAreasAlphaFromCity($city_id){
	
	try
	{
		$sql="SELECT area_id,area_name
		      FROM fin_city_area
			  ORDER BY area_name
			  WHERE city_id=$city_id";
		$result=dbQuery($sql);	 
		$resultArray=dbResultToArray($result);
		return $resultArray; 
	}
	catch(Exception $e)
	{
	}
}



function insertArea($name,$city_id){
	
	try
	{
		
		$name=clean_data($name);
		$name = ucfirst(strtolower($name));
		$duplicate=checkForDuplicateArea($name,$city_id);
		
		if(checkForNumeric($city_id) && validateForNull($name) && !$duplicate)
		{
			
			$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="INSERT INTO
		      fin_city_area (area_name, city_id)
			  VALUES
			  ('$name', $city_id)";
		$result=dbQuery($sql);	
			
		return dbInsertId();
		
		}
		else if(is_numeric($duplicate))
		{
		 return $duplicate;
		}
		else
		{
			return "error";
			}
	}
	catch(Exception $e)
	{
	}
	
}

function insertAreaExternally($name,$city_id){
	
	try
	{
		$name=clean_data($name);
		$name = ucfirst(strtolower($name));
		$duplicate=checkForDuplicateArea($name,$city_id);
		if(checkForNumeric($city_id) && validateForNull($name) && !$duplicate)
		{
			
			$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="INSERT INTO
		      fin_city_area (area_name, city_id)
			  VALUES
			  ('$name', $city_id)";
		$result=dbQuery($sql);	
			
		return "success";
		
		}
		else
		{
			return "error";
			}
	}
	catch(Exception $e)
	{
	}
	
}

function deleteArea($id){
	
	try
	{
		if(checkForNumeric($id) && !checkIfAreaInUse($id))
		{
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="DELETE FROM
			  fin_city_area
			  WHERE area_id=$id";
		dbQuery($sql);	
		return  "success";
		}
		else
		{
			return "error";
			}
	}
	catch(Exception $e)
	{
	}
	
}

function updateArea($id,$name,$city_id){
	
	try
	{
		$name=clean_data($name);
		$name = ucfirst(strtolower($name));
		$duplicate=checkForDuplicateArea($name,$city_id,$id);
		if(checkForNumeric($city_id) && validateForNull($name) && checkForNumeric($id) && !$duplicate)
		{
			
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="UPDATE fin_city_area
			  SET area_name='$name', city_id=$city_id
			  WHERE area_id=$id";	  
		dbQuery($sql);
		return "success";	
		}
		else
		{
			return "error";
		}
	}
	catch(Exception $e)
	{
	}
	
}

function checkForDuplicateArea($name,$city_id,$id=false)
{
	try{
		$sql="SELECT area_id 
			  FROM 
			  fin_city_area 
			  WHERE area_name='$name'
			  AND city_id=$city_id";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND area_id!=$id";
			  
		$result=dbQuery($sql);	
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			return $resultArray[0][0]; //duplicate found
			} 
		else
		{
			return false;
			}	 
		}
	catch(Exception $e)
	{
		
		}
	
	}

function getAreaByID($id)
{
	$sql="SELECT area_id,city_id, area_name
			  FROM 
			  fin_city_area 
			  WHERE area_id=$id";
		$result=dbQuery($sql);	
		$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0];
		}
	else
	{
		return false;
		}
	}

function getAreaNameByID($id)
{
	$sql="SELECT  area_name
			  FROM 
			  fin_city_area 
			  WHERE area_id=$id";
		$result=dbQuery($sql);	
		$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0][0];
		}
	else
	{
		return false;
		}
	}	
function checkIfAreaInUse($id)
{
	
	
		$sql="SELECT area_id
	      FROM fin_customer
		  WHERE area_id=$id";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		
		return true;
		}
	
	$sql="SELECT area_id
	      FROM fin_guarantor
		  WHERE area_id=$id";
	$result=dbQuery($sql);
	if(dbNumRows($result)>0)
	{
		
		return true;
		}	
		
		return false;
			
		  
	}	

function listAreasFromCityId($city_id)
{
	
	$sql="SELECT area_id, area_name
			  FROM 
			  fin_city_area 
			  WHERE city_id=$city_id
			  ORDER BY area_name";
		$result=dbQuery($sql);	
		$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray;
		}
	else
	{
		return false;
		}
	
	}
	
function listAreasFromCityIdWithGroups($city_id)
{
	
	$sql="SELECT area_id, area_name
			  FROM 
			  fin_city_area 
			  WHERE city_id=$city_id
			  ORDER BY area_name DESC";
		$result=dbQuery($sql);	
		$resultArray=dbResultToArray($result);
		$Groups=listAreaGroupsWithRest();
		$j=count($resultArray);
		foreach($Groups as $grp)
		{
			$resultArray[$j]['area_id']=$grp['areas_id'];
			$resultArray[$j]['area_id']=$resultArray[$j]['area_id'].",0";
			$resultArray[$j]['area_name']=$grp['grp_name'];
			$j++;
			}
	if(count($resultArray)>0)
	{
		$resultArray=array_reverse($resultArray);
		return $resultArray;
		}
	else
	{
		return false;
		}
	
	}	

function getAreaIdFromName($name)
{
	$sql="SELECT area_id
			  FROM 
			  fin_city_area 
			  WHERE area_name='$name'";
		$result=dbQuery($sql);	
		$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		return $resultArray[0][0];
		}
	else
	{
		return false;
		}
	}	

function insertAreaGroup($name,$area_array)
{
	
	$group_id=insertAreaGroupName($name);
	
	if(checkForNumeric($group_id))
	{
	insertAreasToGroup($area_array,$group_id);
	return "success";
	}
	else 
	return "error";
}	
		
function insertAreaGroupName($name)
{
	if(validateForNull($name) && strlen($name)<255 && !checkForDuplicateAreaGroup($name))
	{
		$name=trim($name);
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="INSERT INTO fin_city_area_grp (grp_name,created_by,last_updated_by,date_added,date_modified)
		       VALUES('$name',$admin_id,$admin_id,NOW(),NOW())";
		dbQuery($sql);
		return dbInsertId();	   
		
		}
	return "error";	
	}		

function checkForDuplicateAreaGroup($name,$id=false)
{
	$sql="SELECT grp_id 
			  FROM 
			  fin_city_area_grp 
			  WHERE grp_name='$name'";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND grp_id!=$id";		  
		$result=dbQuery($sql);	
		
		if(dbNumRows($result)>0)
		{
			return true; //duplicate found
			} 
		else
		{
			return false;
			}	 
	
	
	}	
	

function editAreaGroupName($id,$name)
{
	
	if(validateForNull($name) && strlen($name)<255 && !checkForDuplicateAreaGroup($name,$id) && checkForNumeric($id))
	{
		$name=trim($name);
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$sql="UPDATE  fin_city_area_grp
		      SET grp_name='$name',last_updated_by=$admin_id,date_modified=NOW()
		      WHERE grp_id=$id";
		dbQuery($sql);
		return "success";	   
		
		}
	return "error";	
	
	}	

function deleteAreaGroup($id)
{
	if(checkForNumeric($id))
	{
		$sql="DELETE FROM fin_city_area_grp WHERE grp_id=$id";
		dbQuery($sql);
		return "success";
		}
	return "error";	
}	

function insertAreasToGroup($area_array,$grp_id)
{
	if(count($area_array)>0 && is_numeric($grp_id))
	{
	foreach($area_array as $area_id)
	{	
	$sql="INSERT INTO fin_rel_area_grp (grp_id,area_id)
	      VALUES($grp_id,$area_id)";
	dbQuery($sql);
	  
	}
	return "success";	
	
	}
	return "error";
	}

function deleteRelAreaGroupByAreaID($area_id)
{
	if(checkForNumeric($area_id))
	{
		$sql="DELETE FROM fin_rel_area_grp WHERE area_id=$area_id";
		dbQuery($sql);
		return "success";
		}
	return "error";	
	
	}

function deleteRelAreaGroupByGrpID($grp_id)
{
	if(checkForNumeric($grp_id))
	{
		$sql="DELETE FROM fin_rel_area_grp WHERE grp_id=$grp_id";
		dbQuery($sql);
		return "success";
		}
	return "error";	
	
	}
	
function editAreaGroup($id,$name,$area_array)
{
	editAreaGroupName($id,$name);
	$result1=deleteRelAreaGroupByGrpID($id);
	$result2=insertAreasToGroup($area_array,$id);
	if($result1=="success" && $result2=="success")
	return "success";
	else
	return "error";
	}		
	
function mergeArea($merge_id_array,$name,$city_id)
{
	
	$name=clean_data($name);
		$name = ucfirst(strtolower($name));
		$duplicate=true;
		foreach($merge_id_array as $duplicate_id)
		{
		$duplicate1=checkForDuplicateArea($name,$duplicate_id);
		$duplicate=$duplicate && $duplicate1;
		}
		if(checkForNumeric($city_id) && validateForNull($name) && !$duplicate)
		{
			
	$merge_id_string=implode(",",$merge_id_array);
	$area_id=insertArea($name,$city_id);
	$sql="UPDATE fin_customer SET area_id=$area_id
	       WHERE area_id IN ($merge_id_string)";
	dbQuery($sql);
	$sql="UPDATE fin_guarantor SET area_id=$area_id
	       WHERE area_id IN ($merge_id_string)";
	dbQuery($sql);
	
	$sql="UPDATE fin_rel_area_grp SET area_id=$area_id
	       WHERE area_id IN ($merge_id_string)";
	dbQuery($sql);
	
	foreach($merge_id_array as $merge_id)
	{
		deleteArea($merge_id);
		}
	return "success";	
		}
	return "error";	
		   
}

function listAreaGroups()
{
	$sql="SELECT fin_city_area_grp.grp_id,grp_name,GROUP_CONCAT(area_id) as areas_id
	      FROM fin_city_area_grp
		  LEFT JOIN fin_rel_area_grp
		  ON fin_city_area_grp.grp_id=fin_rel_area_grp.grp_id
		  GROUP BY fin_city_area_grp.grp_id";
	$result=dbQuery($sql);	 
	if(dbNumRows($result)>0)
	return dbResultToArray($result);
	else 
	return "error"; 
	}		
function listAreaGroupsWithRest()
{
	$sql="SELECT fin_city_area_grp.grp_id,grp_name,GROUP_CONCAT(area_id) as areas_id
	      FROM fin_city_area_grp
		  LEFT JOIN fin_rel_area_grp
		  ON fin_city_area_grp.grp_id=fin_rel_area_grp.grp_id
		  GROUP BY fin_city_area_grp.grp_id";
	$result=dbQuery($sql);	
	$resultArray=dbResultToArray($result); 
	if(dbNumRows($result)>0)
	{
		$j=count($resultArray);
		$restAreas=getRestAreasFromGroup();
		if($restAreas!="error")
		{
		$resultArray[$j]['grp_id']=0;
		$resultArray[$j]['grp_name']='Rest Areas';
		$resultArray[$j]['areas_id']=$restAreas;
		}
		return $resultArray;
		}
	else 
	return "error"; 
	}		

function getAreaGroupByID($id)
{
	$sql="SELECT fin_city_area_grp.grp_id,grp_name,GROUP_CONCAT(area_id) as areas_id
	      FROM fin_city_area_grp
		  LEFT JOIN fin_rel_area_grp
		  ON fin_city_area_grp.grp_id=fin_rel_area_grp.grp_id
		  WHERE fin_city_area_grp.grp_id=$id
		  GROUP BY fin_city_area_grp.grp_id";
	$result=dbQuery($sql);	
	$resultArray=dbResultToArray($result); 
	if(dbNumRows($result)>0)
	return $resultArray[0];
	else 
	return "error"; 
}

function getRestAreasFromGroup()
{
	$sql="SELECT area_id FROM fin_rel_area_grp";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	if(dbNumRows($result)>0)
	{
		$area_id_array=array();
		foreach($resultArray as $re)
		{
			$area_id=null;
			$area_id=$re[0];
			if(!in_array($area_id,$area_id_array))
			{
				$area_id_array[]=$area_id;
				}
			}
		}
	
	if(is_array($area_id_array) && !empty($area_id_array))	
	{
	 $areas_id=implode(",",$area_id_array);
	}
	 else 
	 $areas_id="0";
	
	if(validateForNull($areas_id))
	{
	$sql="SELECT area_id,area_name  FROM fin_city_area WHERE area_id NOT IN ($areas_id) ORDER BY area_name";
	$result2=dbQuery($sql);
	$result2Array=dbResultToArray($result2);
	if(dbNumRows($result)>0)
	{
		$returnArray=array();
		foreach($result2Array as $re2)
		{
			if(is_numeric($re2[0]))
			$returnArray[]=$re2[0];
			}
		return implode(",",$returnArray);
		}
	else
	return "error";	
	}
	}		
?>