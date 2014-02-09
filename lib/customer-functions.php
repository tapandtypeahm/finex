<?php 
require_once("cg.php");
require_once("common.php");
require_once("city-functions.php");
require_once("area-functions.php");
require_once("image-functions.php");
require_once("common.php");
require_once("bd.php");

			
function listCustomers(){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}	

function insertCustomer($name,$address,$city_id,$area,$pincode,$file_id,$contact_no,$human_proof_type_id,$proofno,$proofImg,$scanImg){	
	try
	{
		$name=clean_data($name);
		$address=clean_data($address);
		$admin_id=$_SESSION['adminSession']['admin_id'];
		$area_id=insertArea($area,$city_id);
			$name = ucwords(strtolower($name));
			if(validateForNull($name,$address) && checkForNumeric($city_id,$pincode,$file_id) && $contact_no!=null && !empty($contact_no))
			{
				$address=trim($address);
			
				$sql="INSERT INTO 	fin_customer(customer_name,customer_address,customer_pincode,city_id,area_id,file_id,created_by,last_updated_by,date_added,date_modified)				VALUES 
						('$name', '$address', $pincode, $city_id, $area_id, $file_id, $admin_id, $admin_id, NOW(), NOW())";
				$result=dbQuery($sql);
				
				$customer_id=dbInsertId();		

				addCutomerContactNo($customer_id,$contact_no);
				
				addCustomerProof($customer_id,$name,$human_proof_type_id,$proofno,$proofImg,$scanImg);
				return $customer_id;
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

function deletetCustomer($id){
	
	try
	{
		$sql="DELETE FROM fin_customer 
		      WHERE customer_id=$id";
		dbQuery($sql);	  
	}
	catch(Exception $e)
	{
	}
	
}	

function updateCustomer($id,$name,$address,$city_id,$area,$pincode,$contact_no,$human_proof_type_id,$proofno,$proofImg,$scanImg,$paid_by="NA"){
	
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
				
				$sql="UPDATE fin_customer
				     SET customer_name = '$name', customer_address = '$address', customer_pincode = $pincode , paid_by='$paid_by', city_id = $city_id, area_id = $area_id, last_updated_by = $admin_id, date_modified = NOW()
					 WHERE customer_id=$id";
				$result=dbQuery($sql);
				
				deleteAllContactNoCustomer($id);
				addCutomerContactNo($id,$contact_no);
				
				addCustomerProof($id,$name,$human_proof_type_id,$proofno,$proofImg,$scanImg);
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

function setOpeningBalanceForCustomer($customer_id,$opening_balnce,$opening_balance_cd)
{
	
	if(checkForNumeric($customer_id,$opening_balnce,$opening_balance_cd))
	{
		
		$sql="UPDATE fin_customer SET opening_balance = $opening_balnce , opening_cd = $opening_balance_cd
		      WHERE customer_id=$customer_id";
		dbQuery($sql);
		return "success";	  
	}
	else return "error";	
}
function getOpeningBalanceForCustomer($customer_id)
{
	if(checkForNumeric($customer_id))
	{
		$sql="SELECT opening_balance, opening_balance_cd FROM fin_customer WHERE customer_id=$customer_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray[0];
		else 
		return false;
		}
	return false;	
	}
function getCustomerById($id){
	
	try
	{
	}
	catch(Exception $e)
	{
	}
	
}	

function checkForDuplicateCustomer($name,$address,$city_id,$file_id,$id=false)
{
	try
	{
		
		$sql="SELECT customer_id
		      FROM fin_customer
			  WHERE 
			  file_id=$file_id";
		if($id==false)
		$sql=$sql."";
		else
		$sql=$sql." AND customer_id!=$id";	  	  
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
	
function addCutomerContactNo($customer_id,$contact_no)
{
	try
	{
		if(is_array($contact_no))
		{
			foreach($contact_no as $no)
			{
				if(checkForNumeric($no))
				{
				insertContactNoCustomer($customer_id,$no); 
				}
			}
		}
		else
		{
			
			if(checkForNumeric($contact_no))
				{
				insertContactNoCustomer($customer_id,$contact_no); 
				}
			
		}
	}
	catch(Exception $e)
	{
	}
}

function insertContactNoCustomer($id,$contact_no)
{
	try
	{
		
		if(checkForNumeric($id)==true && checkForNumeric($contact_no))
		{
			
		$sql="INSERT INTO fin_customer_contact_no
				      (customer_contact_no, customer_id)
					  VALUES
					  ('$contact_no', $id)";
				dbQuery($sql);	  
		}
	}
	catch(Exception $e)
	{}
	
	
}
function deleteContactNoCustomer($id)
{
	try
	{
		$sql="DELETE FROM fin_customer_contact_no
			  WHERE customer_contact_no_id=$id";
		dbQuery($sql);	  
	}
	catch(Exception $e)
	{}
	
	
	
	}
function deleteAllContactNoCustomer($id)
{
	try
	{
		$sql="DELETE FROM fin_customer_contact_no
			  WHERE customer_id=$id";
		dbQuery($sql);
	}
	catch(Exception $e)
	{}
	
	
	
	}	
function updateContactNoCustomer($id,$contact_no)
{
	try
	{
		deleteAllContactNoCustomer($id);
		addCutomerContactNo($id,$contact_no);
	}
	catch(Exception $e)
	{}
	
	
	
	}

function getCustomerContactNo($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT customer_contact_no FROM fin_customer_contact_no
				WHERE customer_id=$id";
				$result=dbQuery($sql);	  
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)>0)
		return $resultArray;
		else
		return false;
		}
	}


function addCustomerProof($customer_id,$customer_name,$human_proof_type_id_array,$proof_no_array,$proof_img_array,$scanImgArray)
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
									if($proof_no==null || $proof_no=="")
									$proof_no="NA";
								$sql="INSERT INTO fin_customer_proof
								     (human_proof_type_id, customer_proof_no, customer_id, created_by, last_updated_by, date_added, date_modified)
									 VALUES
									 ($human_proof_type_id, '$proof_no', $customer_id, $admin_id, $admin_id, NOW(), NOW() )";
								  
								  $result=dbQuery($sql);
								  $proof_id=dbInsertId();
								  
							    addImagesToCustomerProof($customer_id,$customer_name,$human_proof_type_id,$proof_id,$proof_img_array,$i);
								
								if($scanImgArray!=false && isset($scanImgArray[$i]) && is_array($scanImgArray[$i]))
								{
									
									foreach($scanImgArray[$i] as $scanImage)
									{
										
									
										insertImageToCustomerProof($scanImage,$proof_id);
										
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
			$sql="INSERT INTO fin_customer_proof
								     (human_proof_type_id, customer_proof_no, customer_id, created_by, last_updated_by, date_added, date_modified)
									 VALUES
									 ($human_proof_type_id_array, '$proof_no_array', $customer_id, $admin_id, $admin_id, NOW(), NOW())";
								  
								  $result=dbQuery($sql);
								  $proof_id=dbInsertId();
								  addImagesToCustomerProof($customer_id,$customer_name,$human_proof_type_id_array,$proof_id,$proof_img_array,0);
								}
		}
	}
	catch(Exception $e)
	{
		
	}
	
}	

function addImagesToCustomerProof($customer_id,$customer_name,$human_proof_type_id,$proof_id,$proof_img_array,$i){
	
	
	
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
										   
										   $imageName=addProofImage($customer_name,$customer_id,$human_proof_type_id,$imagee);
							   
							    			insertImageToCustomerProof($imageName,$proof_id);
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
										   
										   $imageName=addProofImage($customer_name,$customer_id,$human_proof_type_id,$imagee);
							   
							  				insertImageToCustomerProof($imageName,$proof_id);
										  }
									  
								  }
	
	}
	
function insertImageToCustomerProof($imageName,$proof_id)
{
	$admin_id=$_SESSION['adminSession']['admin_id'];
	if(validateForNull($imageName) && checkForNumeric($proof_id))
	{
		$imageName=clean_data($imageName);
	 $sql="INSERT INTO fin_customer_proof_img
							   		 (customer_proof_img_href, customer_proof_id, created_by, last_updated_by, date_added, date_modified)
									 VALUES
									 ('$imageName', $proof_id, $admin_id, $admin_id, NOW(), NOW())";
									 
									 dbQuery($sql);
	}
	
}	

function deleteCustomerProof($proof_id)
{
	$sql="DELETE FROM fin_customer_proof
			WHERE customer_proof_id=$proof_id";
	dbQuery($sql);	
	return "success";	
	}

function deleteCustomerProofImage($proof_image_id)
{
	$sql="DELETE FROM fin_customer_proof_img
		  WHERE customer_proof_img_id=$proof_id";
	dbQuery($sql);	
	}

function listProofTypes()
{
	$sql="SELECT human_proof_type_id, proof_type
	      FROM fin_human_proof_type";
	$result=dbQuery($sql);
	$resultArray=dbResultToArray($result);
	return $resultArray;	  
}

function getCustomerProofByFileId($file_id)
{
	if(checkForNumeric($file_id))
	{
		$sql="SELECT fin_customer_proof.customer_proof_id,fin_customer_proof.human_proof_type_id,proof_type,customer_proof_no
		      FROM fin_customer,fin_customer_proof,fin_human_proof_type
			  WHERE file_id=$file_id
			  AND fin_customer.customer_id=fin_customer_proof.customer_id
			  AND fin_customer_proof.human_proof_type_id=fin_human_proof_type.human_proof_type_id";
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

function getCustomerProofimgByProofId($id)
{
	if(checkForNumeric($id))
	{
		$sql="SELECT customer_proof_img_id,customer_proof_img_href FROM fin_customer_proof_img WHERE customer_proof_id=$id";
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

function getCustomerDetailsByFileId($file_id)
{
	if(checkForNumeric($file_id))
	{
		$sql="SELECT customer_id,customer_name,customer_address,customer_pincode,city_id,area_id,file_id,paid_by, opening_balance, opening_cd
		      FROM fin_customer
			  WHERE file_id=$file_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		$contactNoArray=getCustomerContactNo($resultArray[0]['customer_id']);
		
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
function getCustomerIdByFileId($file_id)
{
	if(checkForNumeric($file_id))
	{
		$sql="SELECT customer_id
		      FROM fin_customer
			  WHERE file_id=$file_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		
		
		if(dbNumRows($result)>0)
		{
			return $resultArray[0][0];
			}	  
		else
		{
			return "error";
			}		  
			  
		
		}
	
	}		

function getCustomerNameANDCoByFileId($file_id)
{
	if(checkForNumeric($file_id))
	{
		$sql="SELECT customer_id,customer_name
		      FROM fin_customer
			  WHERE file_id=$file_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		$contactNoArray=getCustomerContactNo($resultArray[0]['customer_id']);
		
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

function getCustomerDetailsByCustomerId($customer_id)
{
	if(checkForNumeric($customer_id))
	{
		$sql="SELECT customer_id,customer_name,customer_address,customer_pincode,city_id,file_id
		      FROM fin_customer
			  WHERE customer_id=$customer_id";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		$contactNoArray=getCustomerContactNo($customer_id);
	
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

function getFileIdFromCustomerName($name)
{
	
	if(validateForNull($name))
	{
		$oc_id=$_SESSION['adminSession']['oc_id'];
		$name=clean_data($name);
		$sql="SELECT fin_file.file_id
		      FROM fin_customer,fin_file
			  WHERE our_company_id=$oc_id
			  AND fin_customer.file_id=fin_file.file_id
			  AND customer_name ";
		$cond="='$name' AND file_status!=3";
		$sq=$sql.$cond;
		$result=dbQuery($sq);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)==1)
		{
			
			return $resultArray[0][0];
		}
		else if(dbNumRows($result)>1)
		{
			return $resultArray;
		}
		else
		{ 
			
			$sql=$sql." LIKE '%".$name."%'";
			$result=dbQuery($sql);
			$resultArray=dbResultToArray($result);
			if(dbNumRows($result)==1)
			{
			$resultArray['nameType']="like";
			return $resultArray;
			}
			else if(dbNumRows($result)>1)
			{
			$resultArray['nameType']="like";
			return $resultArray;
			}	
			else
			return "error";
		}		  
		}
	}		

function getFileIdFromCustomerNo($no)
{
	
	if(checkForNumeric($no))
	{
		
		$oc_id=$_SESSION['adminSession']['oc_id'];
		$sql="SELECT fin_file.file_id
		      FROM fin_customer,fin_customer_contact_no,fin_file
			  WHERE customer_contact_no=$no
			  AND our_company_id=$oc_id
			  AND fin_customer.file_id=fin_file.file_id
			  AND fin_customer.customer_id=fin_customer_contact_no.customer_id
			  AND file_status!=3";
		$result=dbQuery($sql);
		$resultArray=dbResultToArray($result);
		if(dbNumRows($result)==1)
		{
			
			return $resultArray[0][0];
		}
		else if(dbNumRows($result)>1)
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