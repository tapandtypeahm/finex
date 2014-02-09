<div class="insideCoreContent adminContentWrapper wrapper">
<h4 class="headingAlignment no_print  addBtn">Add a New Company</h4>
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
  <?php if(isset($type)  && $type>0 && $type<4) { ?> <strong>Success!</strong> <?php } else if(isset($type)   && $type>3) { ?> <strong>Warning!</strong> <?php } ?> <?php echo $msg; ?>
</div>
<?php
		}
	if(isset($type) && $type>0)
		$_SESSION['ack']['type']=0;
	if($msg!="")
		$_SESSION['ack']['msg']=="";
}

?>
<div id="insertDiv">
    <form id="addOurCompanyForm" action="<?php echo $_SERVER['PHP_SELF'].'?action=add'; ?>" method="post" onsubmit="return submitOurCompany()">
    	<table id="insertTable" class="insertTableStyling no_print">
    
        	<tr>

				<td class="firstColumnStyling">
                Company Name<span class="requiredField">* </span> : 
                </td>
                
                <td>
                <input type="text" name="name" id="txtname"/>
                </td>
			</tr>

            <tr>
                <td>
                Address<span class="requiredField">* </span> : 
                </td>
                
                <td>
                <textarea name="address" id="txtaddress" cols="5" rows="6"></textarea>
                </td>
            </tr>

            <tr id="addcontactTr">
                <td>
                Contact No : 
                </td>
                
                <td id="addcontactTd">
                <input type="text" class="contact" name="contact[]"  /> <span class="addContactSpan"><input type="button" title="add more contact no" value="+" class="btn btn-success addContactbtn"/></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
            </tr>

            <!-- for regenreation purpose Please donot delete -->
            
            <tr id="addcontactTrGenerated">
            <td>
            Contact No : 
            </td>
            
            <td id="addcontactTd">
            <input type="text" class="contact" name="contact[]"  />  <span class="deleteContactSpan"><input type="button" value="-" title="delete this entry"  class="btn btn-danger deleteContactbtn" onclick="deleteContactTr(this)"/></span><span class="ValidationErrors contactNoError">Please enter a valid Phone No (only numbers)</span>
                </td>
            </td>
            </tr>
            
            <!-- end for regenreation purpose -->

            <tr>
            <td>Pincode<span class="requiredField">* </span> : </td>
            <td><input type="text" id="txtpincode" name="pincode"/></td>
            </tr>

            <tr>
            <td>City<span class="requiredField">* </span> : </td>
                            <td>
                                <select id="txtcity" name="city">
                                    <option value="-1" >--Please Select--</option>
                                    <?php
                                        $cities = listCitiesAlpha();
                                        foreach($cities as $super)
                                          {
                                         ?>
                                         
                                         <option value="<?php echo $super['city_id'] ?>"><?php echo $super['city_name'] ?></option					>
                                         <?php } ?>
                                          
                                     
                                        </select> 
                             </td>
            </tr>
            
            <tr>
            <td> Prefix<span class="requiredField">* </span> : </td>
            <td> <input id="txtprefix" type="text" name="prefix"/> </td>
            </tr>
            
             <tr>
            <td> Subheading<span class="requiredField">* </span> : </td>
            <td> <input id="txtsubheading" type="text" name="sub_heading"/> </td>
            </tr>
            
            
            <tr>
            <td></td>
            <td>
            <input type="submit" value="Add Our Company" class="btn btn-warning">
            <a href="<?php echo WEB_ROOT ?>admin/settings/"><input type="button" value="back" class="btn btn-success" /></a>
            </td>
            </tr>
</table>
</form>	
</div>
    <hr class="firstTableFinishing" />

<h4 class="headingAlignment">List of Our Companies</h4>
<div class="printBtnDiv no_print"> <a href="<?php echo 'index.php?action=resetCounters'; ?>" onclick="return confirm('Are you sure that you want to reset Rasid No to 1?')"><input type="button" class="btn btn-danger"  value="Reset Radid No" /></a><button class="printBtn btn"><i class="icon-print"></i> Print</button></div>
    <div class="no_print">
   
    <table id="adminContentTable" class="adminContentTable no_print">
    <thead>
    	<tr>
        	<th class="heading">No</th>
            <th class="heading">Company Name</th>
            <th class="heading">Prefix</th>
            <th class="heading">Address</th>
            <th class="heading">Pincode</th>
            <th class="heading">City</th>
             <th class="heading">Contact No</th>
              <th class="heading">Rasid Counter</th>
              <th class="heading no_print btnCol"></th>
            <th class="heading no_print btnCol"></th>
            <th class="heading no_print btnCol"></th>
        </tr>
    </thead>
    <tbody>
        
        <?php
		$ourCompanies=listOurCompanies();
		$i=0;
		foreach($ourCompanies as $ourCompanyDetails)
		{
		 ?>
         <tr class="resultRow">
        	<td><?php echo ++$i; ?></td>
            <td><?php echo $ourCompanyDetails['our_company_name']; ?>
            </td>
              <td><?php echo $ourCompanyDetails['our_company_prefix']; ?>
            </td>
            <td><?php echo $ourCompanyDetails['our_company_address']; ?>
            </td>
            <td><?php echo $ourCompanyDetails['our_company_pincode']; ?>
            </td>
            <td><?php   $city=getCityByID($ourCompanyDetails['city_id']); echo $city['city_name']; ?>
            </td>
           <td><?php $contact_nos=getContactNoForOurCompany($ourCompanyDetails['our_company_id']); 
		   			if($contact_nos!=false)
					{ 	
						for($i=0;$i<count($contact_nos);$i++)
						{ 
							$no=$contact_nos[$i]; 
							if($i==(count($contact_nos)-1)) 
							echo $no[1]; 
							else
							echo $no[1]." | "; 
						}
					}
					else
					echo "NA";  ?>
            </td>
            <td><?php echo getRasidCounterForOCID($ourCompanyDetails['our_company_id']); ?></td>
             <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=details&lid='.$ourCompanyDetails['our_company_id'] ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
            <td class="no_print"> <a href="<?php echo $_SERVER['PHP_SELF'].'?view=edit&lid='.$ourCompanyDetails['our_company_id'] ?>"><button title="Edit this entry" class="btn editBtn"><span class="delete">E</span></button></a>
            </td>
            <td class="no_print"> 
            <a href="<?php echo $_SERVER['PHP_SELF'].'?action=delete&lid='.$ourCompanyDetails['our_company_id'] ?>"><button title="Delete this entry" class="btn delBtn"><span class="delete">X</span></button></a>
            </td>
            
          
  
        </tr>
         <?php }?>
         </tbody>
    </table>
    </div>
   <table id="to_print" class="to_print adminContentTable"></table> 
</div>
<div class="clearfix"></div>