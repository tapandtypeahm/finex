<?php
if(!isset($_GET['id']))
header("Location: index.php");

$file_id=$_GET['id'];
$customer=getCustomerDetailsByFileId($file_id);

$file=getFileDetailsByFileId($file_id);

$guarantor=getGuarantorDetailsByFileId($file_id);
$loan=getLoanDetailsByFileId($file_id);

?>
<div class="insideCoreContent adminContentWrapper wrapper">

<?php 
if(isset($_SESSION['ack']['msg']) && isset($_SESSION['ack']['type']))
{
	
	$msg=$_SESSION['ack']['msg'];
	$type=$_SESSION['ack']['type'];
	
	
		if($msg!=null && $msg!="" && $type>0)
		{
?>
<div class="alert no_print  <?php if(isset($type) && $type>0 && $type<4) echo "alert-success"; else echo "alert-error" ?>">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <?php if(isset($type)  && $type>0 && $type<4) { ?> <strong>Success!</strong> <?php } else if(isset($type) && $type>3) { ?> <strong>Warning!</strong> <?php } ?> <?php echo $msg; ?>
</div>
<?php
		
		
		}
	if(isset($type) && $type>0)
		$_SESSION['ack']['type']=0;
	if($msg!="")
		$_SESSION['ack']['msg']=="";
}

?>


<div class="detailStyling">

<h4 class="headingAlignment">File Details</h4>

<table class="insertTableStyling detailStylingTable">

<tr>
<td>Agency Name : </td>
				<td>
				
                             <?php
							 
							  $id =  $file['agency_id']; 
							 if($id!=null)
							 {
							        $agencyDetails=getAgencyById($id);
									echo $agencyDetails['agency_name'];
							 }
							 else
							 {
								 $id=$file['oc_id'];
								 echo getOurCompanyNameByID($id);
								 }
							 ?>					
                             
                </td>
                    
                    
                  
</tr>

<tr>
<td>
File Agreement No : 
</td>
<td>
 
                             <?php echo $file['file_agreement_no'] ?>					
                             

</td>
</tr>

<tr>
<td>File Number : </td>
				<td>
				
                             <?php echo $file['file_number']; ?>					
                            

                 </td>
</tr>


</table>

</div>



<div class="detailStyling">

<h4 class="headingAlignment">Customer's Details</h4>


<table id="insertCustomerTable" class="insertTableStyling detailStylingTable">


<tr>

<td class="firstColumnStyling">
Customer's Name : 
</td>

<td>

                             <?php echo $customer['customer_name']; ?>					
                            
</td>
</tr>

<tr>
<td>
Address : 
</td>

<td>

                             <?php echo $customer['customer_address']; ?>					
                            
</td>
</tr>


<tr>
<td>City : </td>
				<td>

                             <?php $cid = $customer['city_id'];
							 		
							       $cityDetails = getCityByID($cid);
								   echo $cityDetails;
							?>
                            </td>
</tr>

<tr>
<td>Pincode : </td>
<td>

                             <?php echo $customer['customer_pincode']; ?>					
                          
</td>
</tr>



 <tr id="addcontactTrCustomer">
                <td>
                Contact No : 
                </td>
                
                <td id="addcontactTd">
                <?php
                            $contactNumbers = $customer['contact_no'];
							
                            foreach($contactNumbers as $c)
                              {
                       ?>
                             
                             <?php echo $c[0]." | "; ?>					
                             <?php } ?>
                </td>
            </tr>


</table>
</div>

<div class="detailStyling">

<h4 class="headingAlignment">Loan Details </h4>


<table class="insertTableStyling detailStylingTable">


<tr>

<td class="firstColumnStyling">
Total Loan Amount : 
</td>

<td>

                             <?php echo $loan['loan_amount']; ?>					
                           
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Loan Duration (In months) : 
</td>

<td>
                             <?php echo $loan['loan_duration']; ?>					
                          
</td>
</tr>

<tr>

<td class="firstColumnStyling">
Rate of Interest (annually in %) : 
</td>

<td>

                             <?php echo $loan['roi']; ?>					
                            
</td>
</tr>


<tr>

<td class="firstColumnStyling">
EMI : 
</td>

<td>

                             <?php echo $loan['emi']; ?>					
                            </td>
</tr>



<tr>

<td class="firstColumnStyling">
Loan Starting Date : 
</td>

<td>
 
                             <?php echo $loan['loan_starting_date']; ?>					
                           
</td>
</tr>

</table>
</div>


<div class="detailStyling">

<h4 class="headingAlignment">Guranteer's Details</h4>


<table id="insertGuarantorTable" class="insertTableStyling detailStylingTable">


<tr>

<td class="firstColumnStyling">
Guranteer's Name : 
</td>

<td>
                             <?php echo $guarantor['guarantor_name']; ?>					
                             
</td>
</tr>

<tr>
<td>
Guranteer's Address : 
</td>

<td>

                             <?php echo $guarantor['guarantor_address']; ?>					
                             </td>
</tr>


<tr>
<td>City : </td>
				<td>
   
                             <?php $gid =  $guarantor['city_id']; 
                             $gCityDetails = getCityByID($gid);
								   echo $gCityDetails['city_name'];	
?>
                            </td>
</tr>

<tr>
<td>Pincode : </td>
<td>
   
                             <?php echo $guarantor['guarantor_pincode']; ?>					
                           

</td>
</tr>



 <tr id="addcontactTrGuarantor">
                <td>
                Contact No : 
                </td>
                
                <td id="addcontactTd">
                	<?php
                            $contactNos = $guarantor['contact_no'];
							
                            foreach($contactNos as $c)
                              {
                       ?>
                             
                             <?php echo $c[0]." | "; ?>					
                             <?php } ?>
                </td>
            </tr>
            
 </table>
</div>


<!--

<hr class="firstTableFinishing" />

<h4 class="headingAlignment"> Vehicle Details </h4>


<table class="insertTableStyling no_print">




<tr>
<td>Vehicle Dealer : </td>
				<td>
					<select id="dealer" name="dealer">
                        <option value="-1" >--Please Select the Dealer--</option>
                        <?php
                            $dealers = listVehicleDealers();
                            foreach($dealers as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['dealer_id'] ?>"><?php echo $super['dealer_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>

<tr>
<td>Vehicle Company : </td>
				<td>
					<select id="dealer" name="dealer">
                        <option value="-1" >--Please Select the Company--</option>
                        <?php
                            $companies = listVehicleCompanies();
                            foreach($companies as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['vehicle_company_id'] ?>"><?php echo $super['company_name'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>

<tr>
       <td >Vehicle Condition :</td>
           
           
        <td>
              <table>
               <tr><td><input type="radio"   name="condition"  value="New" checked="checked"></td><td>New</td></tr>
            <tr><td><input type="radio"  name="condition"  value="used" ></td><td>Used</td>
               </tr> 
            </table>
        </td>
 </tr>
 
 <tr>
<td>Vehicle Model : </td>
				<td>
					<select id="model" name="model">
                        <option value="-1" >--Please Select the Model Year--</option>
                        <option value="0" >2008</option>
                        <option value="1" >2009</option>
                        <option value="2" >2010</option>
                        <option value="3" >2011</option>
                        <option value="4" >2012</option>
                     </select> 
                            </td>
</tr>

<tr>
<td>Vehicle Type : </td>
				<td>
					<select id="type" name="type">
                        <option value="-1" >--Please Select the Type--</option>
                        <?php
                            $types = listVehicleTypes();
                            foreach($types as $super)
                              {
                             ?>
                             
                             <option value="<?php echo $super['vehicle_type_id'] ?>"><?php echo $super['vehicle_type'] ?></option					>
                             <?php } ?>
                              
                         
                            </select> 
                            </td>
</tr>
 

<td class="firstColumnStyling">
Registration Number : 
</td>

<td>
<input type="text" name="rNo"/>
</td>
</tr>

<tr>

<td class="firstColumnStyling">
 Registration Date : 
</td>

<td>
 <input type="text" size="12"  name="rDate" />
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Engine Number : 
</td>

<td>
<input type="text" name="engineNo"/>
</td>
</tr>

<tr>
<td class="firstColumnStyling">
Chasis Number : 
</td>

<td>
<input type="text" name="chasisNo"/>
</td>
</tr>


<tr>
<td>
Add Document : 
</td>
<td>
<input type="file" name="doc"  /><input type="button" value="+" class="btn btn-primary addContactbtn"/>
</td>
</tr>




<tr>
<td></td>
<td>
<input type="submit" value="Add Vehicle Details" class="btn btn-warning">
</td>
</tr>






</table>








</form>-->

</div>
<div class="clearfix"></div>