<?php
require_once("../lib/cg.php");
require_once("../lib/bd.php");
require_once("../lib/common.php");
require_once("../lib/guarantor-functions.php");
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form method="post" enctype="multipart/form-data" action="../lib/guarantor-functions.php?action=insertGuarantor">

name :<input type="text" name="name" />

address :<textarea name="address"></textarea>

pincode :<input type="number" name="pincode" />

city_id (1 for ahmedabad) :<input type="number" name="city_id" />

file_id (1 valid) :<input type="number" name="file_id" />

customer_id (21 valid) :<input type="number" name="customer_id" />

contact no: <input type="text" name="contact_no[]" />

contact no: <input type="text" name="contact_no[]" />

human proof type id: <input type="number" name="human_proof_type_id[]" />

proof no :<input type="text" name="proof_no[]" />

proof Image 1 :<input type="file" name="proofImg[0][]" />

proof Image 2 :<input type="file" name="proofImg[0][]" />

human proof type id: <input type="number" name="human_proof_type_id[]" />

proof no :<input type="text" name="proof_no[]" />

proof Image 1 :<input type="file" name="proofImg[1][]" />

proof Image 2 :<input type="file" name="proofImg[1][]" />

<input type="submit" />
</form>
</body>
</html>