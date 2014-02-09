<?php
require_once 'cg.php';
require_once 'bd.php';

$thisFile1 = str_replace('\\', '/', __FILE__);
$srvRoot1  = str_replace('lib/image-functions.php', '', $thisFile1);

define('IMG_ROOT', $srvRoot1);

function UploadImagee($image, $uploadDir, $max_width=150 , $max_height=150,$prefix=false)
{
    
    $imagePath = '';
    
    // if a file is given
    if (trim($image['tmp_name']) != '') {
        // get the image extension
        $ext = substr(strrchr($image['name'], "."), 1); 
		if($prefix!=false)
		{
			$imagePath =$prefix. md5(rand() * time()) . ".$ext";
			}
			else
			{
        // generate a random new file name to avoid name conflict
        $imagePath = md5(rand() * time()) . ".$ext";
			}
        
		if (($image["type"] == "image/gif") || ($image["type"] == "image/jpeg") || ($image["type"] == "image/jpg") || ($image["type"] == "image/png") || ($image["type"] == "application/pdf"))
		{
			
		$path_parts = pathinfo($image['tmp_name']);
		
	
		
		 move_uploaded_file($image['tmp_name'], $uploadDir.$imagePath);
		return $imagePath;
		}
		// check the image width. if it exceed the maximum
		// width we must resize it
		//$size = getimagesize($image['tmp_name']);
		
		
		//	$imagePath = generateThumbnailAccordingToWidth($image['tmp_name'], $uploadDir . $imagePath, $size[0] , $max_height);
		
			
			
    }


    
    return false;
}


function generateThumbnailAccordingToWidth($srcFile, $destFile, $max_width , $max_height, $quality = 100)
{

	$thumbnail = '';
	
	if (file_exists($srcFile)  && isset($destFile))
	{
		$size        = getimagesize($srcFile);
		$w           = number_format($max_width, 0, ',', '');
		$h           = number_format(($size[1] / $size[0]) * $max_width, 0, ',', '');
		
		
		$thumbnail =  CopyThumbnail($srcFile, $destFile, $w, $h, $quality);
	}
	
	// return the thumbnail file name on sucess or blank on fail
	return basename($thumbnail);
}

function generateThumbnailAccordingToHeight($srcFile, $destFile, $max_width , $max_height, $quality = 100)
{
	$thumbnail = '';
	
	if (file_exists($srcFile)  && isset($destFile))
	{
		$size        = getimagesize($srcFile);
		$h           = number_format($max_height, 0, ',', '');
		$w           = number_format(($size[0] / $size[1]) * $max_height, 0, ',', '');
		
		if($w>$max_width)
		{
			$w=$max_width;
		}
		
		
		$thumbnail =  CopyThumbnail($srcFile, $destFile, $w, $h, $quality);
	}
	
	// return the thumbnail file name on sucess or blank on fail
	return basename($thumbnail);
}

function CopyThumbnail($srcFile, $destFile, $w, $h, $quality = 75)
{
    $tmpSrc     = pathinfo(strtolower($srcFile));
    $tmpDest    = pathinfo(strtolower($destFile));
    $size       = getimagesize($srcFile);

    if ($tmpDest['extension'] == "gif" || $tmpDest['extension'] == "jpg")
    {
       $destFile  = substr_replace($destFile, 'jpg', -3);
       $dest      = imagecreatetruecolor($w, $h);
       imageantialias($dest, TRUE);
    } elseif ($tmpDest['extension'] == "png") {
       $dest = imagecreatetruecolor($w, $h);
       imageantialias($dest, TRUE);
    } else {
      return false;
    }

    switch($size[2])
    {
       case 1:       //GIF
           $src = imagecreatefromgif($srcFile);
           break;
       case 2:       //JPEG
           $src = imagecreatefromjpeg($srcFile);
           break;
       case 3:       //PNG
           $src = imagecreatefrompng($srcFile);
           break;
       default:
           return false;
           break;
    }

    imagecopyresampled($dest, $src, 0, 0, 0, 0, $w, $h, $size[0], $size[1]);

    switch($size[2])
    {
       case 1:
       case 2:
           imagejpeg($dest,$destFile, $quality);
           break;
       case 3:
           imagepng($dest,$destFile);
    }
    return $destFile;

}

function addProofImage($customer_name,$customer_id,$proof_type_id,$image)
{
	
	
    $image_name_prefix= $customer_name."_".$customer_id."_".$proof_type_id;
	$image_name_prefix=str_replace("/","_",$image_name_prefix);
$catImage = UploadImagee($image,IMG_ROOT.'images/customer_proof/',1000,1000,$image_name_prefix);
		
		
   
	    return $catImage;
    
 	
}

function addProofImageGuarantor($customer_name,$customer_id,$proof_type_id,$image)
{
    $image_name_prefix= $customer_name."_".$customer_id."_".$proof_type_id;
	$image_name_prefix=str_replace("/","_",$image_name_prefix);
$catImage = UploadImagee($image,IMG_ROOT.'images/guarantor_proof/',1000,1000,$image_name_prefix);

   
	    return $catImage;
    
 	
}

function addProofImageVehicle($customer_name,$customer_id,$proof_type_id,$image)
{
    $image_name_prefix= $customer_name."_".$customer_id."_".$proof_type_id;
	$image_name_prefix=str_replace("/","_",$image_name_prefix);
$catImage = UploadImagee($image,IMG_ROOT.'images/vehicle_proof/',1000,1000,$image_name_prefix);

   
	    return $catImage;
    
 	
}

function addProofImageInsurance($customer_name,$customer_id,$image)
{
    $image_name_prefix= $customer_name."_".$customer_id;
	$image_name_prefix=str_replace("/","_",$image_name_prefix);
$catImage = UploadImagee($image,IMG_ROOT.'images/insurance_proof/',1000,1000,$image_name_prefix);

   
	    return $catImage;
    
 	
}

function AddImage($image)
{
    
    $catImage = UploadImagee($image, IMG_ROOT.'admin/images/answers/',75,75);
	
	return "admin/images/answers/".$catImage;
    
  
	
}

function AddIcon($image)
{
    
    $catImage = UploadImagee($image, IMG_ROOT.'admin/images/category_icon/',50,50);
	
	return "admin/images/category_icon/".$catImage;
    
  
	
}

function AddBrandLike($image)
{
    
    $catImage = UploadImagee($image, IMG_ROOT.'admin/images/brands_like/',100,50);
	
	return "admin/images/brands_like/".$catImage;
    
  
	
}
?>