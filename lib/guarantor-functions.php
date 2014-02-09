<?php 
require_once("cg.php");
require_once("common.php");
require_once("image-functions.php");
require_once("city-functions.php");
require_once("bd.php");
		
function listGuarantors(){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}	

function insertGuarantor($name,$address,$city_id,$area,$pincode,$file_id,$customer_id,$contact_no,$human_proof_type_id,$proofno,$proofImg,$scanImg=false) // city_id,file_id,customer_id,name,address,contact compulsory
{
	
	try
	{
		
			if(!validateForNull($pincode))
		    $pincode=0;
			$name=clean_data($name);
			$address=clean_data($address);
			$admin_id=$_SESSION['adminSession']['admin_id'];
			$name = ucwords(strtolower($name));
			$area_id=insertArea($area,$city_id);
			if(checkForNumeric($city_id,$file_id,$customer_id) && $name!=null && $name!="" && $address!=null && $address!="" && $contact_no!=null && !empty($contact_no)  && !checkForDuplicateGuarantor($name, $address, $city_id, $file_id, $customer_id))
			{
				$address=trim($address);
				
				$sql="INSERT INTO 	
				fin_guarantor(guarantor_name, guarantor_address, guarantor_pincode, city_id, area_id, file_id, customer_id, created_by, last_updated_by, date_added, date_modified)				VALUES 
						('$name', '$address', $pincode, $city_id, $area_id, $file_id, $customer_id, $admin_id, $admin_id, NOW(), NOW())";
			
				$result=dbQuery($sql);
				
				$guarantor_id=dbInsertId();		
				addGuarantorContactNo($guarantor_id,$contact_no);
				addGuarantorProof($guarantor_id,$name,$human_proof_type_id,$proofno,$proofImg,$scanImg);
				return "success";
			}
			return "error";
				
	}
	catch(Exception $e)
	{
	}
	
}	

function deletetGuarantor($id){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}	

function updateGuarantor($id,$name,$address,$city_id,$area,$pincode,$contact_no,$human_proof_type_id,$proofno,$proofImg,$scanImg){
	
	try
	{
		if(!validateForNull($pincode))
		$pincode=0;
		$name=clean_data($name);
		$address=clean_data($address);
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$area_id=insertArea($area,$city_id);
			$name = ucwords(strtolower($name));
			if(validateForNull($name,$address) && checkForNumeric($city_id) && $contact_no!=null && !empty($contact_no))
			{
				$address=trim($address);
			
				$sql="UPDATE fin_guarantor
				     SET guarantor_name = '$name', guarantor_address = '$address', guarantor_pincode = $pincode , city_id = $city_id, area_id = $area_id, last_updated_by = $admin_id, date_modified = NOW()
					 WHERE guarantor_id=$id";
				$result=dbQuery($sql);
				
				deleteAllContactNoGuarantor($id);
				addGuarantorContactNo($id,$contact_no);
				addGuarantorProof($id,$name,$human_proof_type_id,$proofno,$proofImg,$scanImg);
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

function getGuarantorById($id){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}	

function checkForDuplicateGuarantor($name,$address,$city_id,$file_id,$customer_id,$id=false)
{
	try
	{
		
		$sql="SELECT guarantor_id
		      FROM fin_guarantor
			  WHERE  file_id=$file_id";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND guarantor_id!=$id";	  	  
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		{
			$_SESSION['error']['submit_error']="Duplicate Entry!";
			return true;
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
	
function addGuarantorContactNo($guarantor_id,$contact_no)
{
	try
	{
		if(is_array($contact_no))
		{
			foreach($contact_no as $no)
			{
				if($no!="" && $no!=null)
				{
				insertContactNoGuarantor($guarantor_id,$no); 
				}
			}
		}
		else
		{
			if($contact_no!="" && $contact_no!=null)
				{
				insertContactNoGuarantor($guarantor_id,$contact_no); 
				}
			
		}
	}
	catch(Exception $e)
	{
	}
}

function insertContactNoGuarantor($id,$contact_no)
{
	try
	{
		if(checkForNumeric($id,$contact_no)==true)
		{
		$sql="INSERT INTO fin_guarantor_contact_no
				      (guarantor_contact_no, guarantor_id)
					  VALUES
					  ('$contact_no', $id)";
				dbQuery($sql);	  
		}
	}
	catch(Exception $e)
	{}
	
	
}
function deleteContactNoGuarantor($id)
{
	try
	{
		$sql="DELETE FROM fin_guarantor_contact_no
			  WHERE guarantor_contact_no_id=$id";
		dbQuery($sql);	  
	}
	catch(Exception $e)
	{}
	
	
	
	}
function deleteAllContactNoGuarantor($id)
{
	try
	{
		$sql="DELETE FROM fin_guarantor_contact_no
			  WHERE guarantor_id=$id";
		dbQuery($sql);
	}
	catch(Exception $e)
	{}
	
	
	
	}	
function updateContactNoGuarantor($id,$contact_no)
{
	try
	{
		deleteAllContactNoGuarantor($id);
		addGuarantorContactNo($id,$contact_no);
	}
	catch(Exception $e)
	{}
	
	
	
	}
	
function getGuarantorContactNo($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT guarantor_contact_no FROM fin_guarantor_contact_no
				WHERE guarantor_id=$id";
				$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray;
		else
		return false;
		}
	}	
	
function addGuarantorProof($guarantor_id,$guarantor_name,$human_proof_type_id_array,$proof_no_array,$proof_img_array,$scanImgArray)
{
	try
	{
		
		$admin_id=$_SESSION['adminSession']['admin_id'];
		if(is_array($human_proof_type_id_array)) // if more than one proof submitted
		{
			$len=count($human_proof_type_id_array);
			for($i=0;$i<$len;$i++)
			{
								
								$human_proof_type_id=$human_proof_type_id_array[$i];
								
								if($human_proof_type_id>0 && (checkForImagesInArray($proof_img_array['name'][$i]) || ($proof_no_array[$i]!=null && $proof_no_array[$i]!="")))
								{
								
								
								$proof_no=$proof_no_array[$i];
								$proof_no=clean_data($proof_no);
								
								$sql="INSERT INTO fin_guarantor_proof
								     (human_proof_type_id, guarantor_proof_no, guarantor_id, created_by, last_updated_by, date_added, date_modified)
									 VALUES
									 ($human_proof_type_id, '$proof_no', $guarantor_id, $admin_id, $admin_id, NOW(), NOW() )";
								  
								  $result=dbQuery($sql);
								  $proof_id=dbInsertId();
								  
							    addImagesToGuarantorProof($guarantor_id,$guarantor_name,$human_proof_type_id,$proof_id,$proof_img_array,$i);
								if($scanImgArray!=false && isset($scanImgArray[$i]) && is_array($scanImgArray[$i]))
								{
									
									foreach($scanImgArray[$i] as $scanImage)
									{
										insertImageToGuarantorProof($scanImage,$proof_id);
										}
									}
									
									
								
								}
							   
				
			}
			
		}
		else // if only one proof submitted
		{
			if($human_proof_type_id_array>0 && (checkForImagesInArray($proof_img_array['name'][$i]) || ($proof_no_array[$i]!=null && $proof_no_array[$i]!="")))
								{
			$proof_no_array=clean_data($proof_no_array);							
			$sql="INSERT INTO fin_guarantor_proof
								     (human_proof_type_id, guarantor_proof_no, guarantor_id, created_by, last_updated_by, date_added, date_modified)
									 VALUES
									 ($human_proof_type_id_array, '$proof_no_array', $guarantor_id, $admin_id, $admin_id, NOW(), NOW() )";
								  
								  $result=dbQuery($sql);
								  $proof_id=dbInsertId();
								  addImagesToGuarantorProof($guarantor_id,$guarantor_name,$human_proof_type_id_array,$proof_id,$proof_img_array,0);
								}
		}
	}
	catch(Exception $e)
	{
		
	}
	
}	

function addImagesToGuarantorProof($guarantor_id,$guarantor_name,$human_proof_type_id,$proof_id,$proof_img_array,$i){
	
	if(is_array($proof_img_array['name'][$i])) // if proof has more than one image
								  {
									  $images_for_a_proof=count($proof_img_array['name'][$i]);
									  for($j=0;$j<$images_for_a_proof;$j++)
									  {
										  if($proof_img_array['name'][$i][$j]!="" &&  $proof_img_array['name'][$i][$j]!=null)
										  {
										   $imagee['name'] = $proof_img_array['name'][$i][$j];
										   $imagee['type'] = $proof_img_array['type'][$i][$j];
										   $imagee['tmp_name'] = $proof_img_array['tmp_name'][$i][$j];
										   $imagee['error'] = $proof_img_array['error'][$i][$j];
										   $imagee['size'] = $proof_img_array['size'][$i][$j];
										   
										   $imageName=addProofImageGuarantor($guarantor_name,$guarantor_id,$human_proof_type_id,$imagee);
							   
							    			insertImageToGuarantorProof($imageName,$proof_id);
										  }
										   
									  }
								  }
								  else // if proof has only one image
								  {
									  		if($proof_img_array['name'][$i]!="" &&  $proof_img_array['name'][$i]!=null)
										  {
									       $imagee['name'] = $proof_img_array['name'][$i];
										   $imagee['type'] = $proof_img_array['type'][$i];
										   $imagee['tmp_name'] = $proof_img_array['tmp_name'][$i];
										   $imagee['error'] = $proof_img_array['error'][$i];
										   $imagee['size'] = $proof_img_array['size'][$i];
										   
										   $imageName=addProofImageGuarantor($guarantor_name,$guarantor_id,$human_proof_type_id,$imagee);
							   
							  				insertImageToGuarantorProof($imageName,$proof_id);
										  }
									  
								  }
	
	}
	
function insertImageToGuarantorProof($imageName,$proof_id)
{
	 $admin_id=$_SESSION['adminSession']['admin_id'];
	
	if(validateForNull($imageName) && checkForNumeric($proof_id))
	{
		
	 $imageName=clean_data($imageName);
	 $sql="INSERT INTO fin_guarantor_proof_img
							   		 (guarantor_proof_img_href, guarantor_proof_id, created_by, last_updated_by, date_added, date_modified)
									 VALUES
									 ('$imageName', $proof_id, $admin_id, $admin_id, NOW(), NOW())";
									 
									 dbQuery($sql);
									
									
	}
	
}	

function deleteGuarantorProof($proof_id)
{
	$sql="DELETE FROM fin_guarantor_proof
			WHERE guarantor_proof_id=$proof_id";
	dbQuery($sql);	
	return "success";		
	}

function deleteGuarantorProofImage($proof_image_id)
{
	$sql="DELETE FROM fin_guarantor_proof_img
		  WHERE guarantor_proof_img_id=$proof_id";
	dbQuery($sql);	
	}	

function getGuarantorDetailsByFileId($file_id)
{
	if(checkForNumeric($file_id))
	{
		$sql="SELECT guarantor_id, guarantor_name, guarantor_address, guarantor_pincode, city_id,area_id, file_id, customer_id
		      FROM fin_guarantor
			  WHERE file_id=$file_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		
		if(dbNumRows($result)>0)
		{
			$contactNoArray=getGuarantorContactNo($resultArray[0]['guarantor_id']);
		$resultArray[0][]=$contactNoArray;
		$resultArray[0]['contact_no']=$contactNoArray;
		
			return $resultArray[0];
			}	  
		else
		{
			return "error";
			}		  
			  
		
		}
	
	}	

function getGuarantorDetailsByCustomerId($customer_id)
{
	if(checkForNumeric($customer_id))
	{
		$sql="SELECT guarantor_id, guarantor_name, guarantor_address, guarantor_pincode, city_id, area_id, file_id, customer_id
		      FROM fin_guarantor
			  WHERE customer_id=$customer_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		$contactNoArray=getGuarantorContactNo($resultArray[0]['guarantor_id']);
		if(dbNumRows($result)>0)
		{
		$resultArray[0][]=$contactNoArray;
		$resultArray[0]['contact_no']=$contactNoArray;
		
			return $resultArray[0];
			}	  
		else
		{
			return "error";
			}		  
			  
		
		}
	
	}	

function getGuarantorProofByFileId($file_id)
{
	if(checkForNumeric($file_id))
	{
		$sql="SELECT fin_guarantor_proof.guarantor_proof_id,fin_guarantor_proof.human_proof_type_id,proof_type,guarantor_proof_no
		      FROM fin_guarantor,fin_guarantor_proof,fin_human_proof_type
			  WHERE file_id=$file_id
			  AND fin_guarantor.guarantor_id=fin_guarantor_proof.guarantor_id
			  AND fin_guarantor_proof.human_proof_type_id=fin_human_proof_type.human_proof_type_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		
		
		if(dbNumRows($result)>0)
		{
		
		
			return $resultArray;
			}	  
		else
		{
			return "error";
			}		  
			  
		
		}
	
	
	}

function getGuarantorProofimgByProofId($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT guarantor_proof_img_id,guarantor_proof_img_href FROM fin_guarantor_proof_img WHERE guarantor_proof_id=$id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		
		if(dbNumRows($result)>0)
		{
		
		
			return $resultArray;
			}	  
		else
		{
			return "error";
			}		  
		}
	
	}					
?>