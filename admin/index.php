<?php
require_once("../lib/cg.php");
require_once("../lib/bd.php");
require_once("../lib/report-functions.php");
require_once("../lib/loan-functions.php");
$selectedLink="home";
require_once("../inc/header.php");
 ?>
<div class="insideCoreContent adminContentWrapper wrapper"> 
 <div class="widgetContainer">
 
   <div class="notificationCenter">
       Notification Center
   </div>
   
   
<?php 
if(isset($_SESSION['adminSession']['report_rights']))
{
	$report_rights=$_SESSION['adminSession']['report_rights'];
	}
if(isset($_SESSION['adminSession']['report_rights']) && (in_array(105,$report_rights) || in_array(199,$report_rights)))
			{ ?>
   
    <div class="Column">
     
        
        <h4 class="widgetTitle"> 1.) Upcoming Reminders </h4>
         
         <table class="adminContentTable">
    <thead>
    	<tr>
            <th class="heading">Type</th>
        	<th class="heading">Date</th>
            <th class="heading">Remarks</th>
            <th class="heading">Name</th>
            <th class="heading">Contact No.</th>
             <th class="heading">Vehicle No.</th>
              <th class="heading">File No.</th>
            <th class="heading btnCol no_print"></th>
        </tr>
    </thead>
    <tbody>
         <?php
		 $upcomingEmis=generalRemianderReportsWidget(date('d/m/Y'));
			$mj=0;
			if(is_array($upcomingEmis) && count($upcomingEmis)>0)
			{
		    foreach($upcomingEmis as $upEmi)
			{
			
		?>
        
         <tr class="resultRow">
        	
            <td><?php echo ucwords($upEmi['type']); ?>
            </td>
            <td><?php echo date('d/m/Y',strtotime($upEmi['date'])); ?>
            </td>
            
            <td><?php echo $upEmi['remarks']; ?>
            </td>
            
             <td><?php echo $upEmi['customer']['customer_name']; ?>
            </td>
            
            
            <td><?php echo $upEmi['customer']['contact_no'][0][0]; ?>
            </td>
            
            
             <td><?php if($upEmi['reg_no']=="" || $upEmi['reg_no']==null) echo "NA"; else echo $upEmi['reg_no']; ?>
            </td>
            
            <td><?php echo $upEmi['file_no']; ?>
            </td>
          
            
             	<td class="no_print"><?php if($upEmi['type']=='general') { ?> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=addRemainder&id=<?php echo $upEmi['file_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a><?php } ?>
                					<?php if($upEmi['type']=='payment') { ?> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=EMIdetails&id=<?php echo $upEmi['file_id']; ?>&state=<?php  echo getEMIIDFromPaymentId($upEmi['id']); ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a><?php } ?>
            </td>
            
            
  
        </tr>
        <?php
		$mj++;
		if($mj==5) break;
			}
		}
		?>
         
         </tbody>
    </table>

<a href="<?php echo WEB_ROOT ?>admin/reports/remainder_reports/custom/index.php?action=fromHomeUpcoming"><div class="more">View all Remainders..</div></a>

<div style="clear:both"></div>
</div>

 <div class="Column">
     
        
        <h4 class="widgetTitle"> 2.) Expired Reminders </h4>
         
         <table class="adminContentTable">
    <thead>
    	<tr>
            <th class="heading">Type</th>
        	<th class="heading">Date</th>
            <th class="heading">Remarks</th>
            <th class="heading">Name</th>
            <th class="heading">Contact No.</th>
             <th class="heading">Vehicle No.</th>
             <th class="heading">File No.</th>
            <th class="heading btnCol no_print"></th>
        </tr>
    </thead>
    <tbody>
         <?php
		 
		 $upcomingEmis=generalRemianderReportsWidget(null,date('d/m/Y'));
		 
			$mj=0;
			if(is_array($upcomingEmis) && count($upcomingEmis)>0)
			{
		    foreach($upcomingEmis as $upEmi)
			{
			
		?>
        
         <tr class="resultRow">
        	
            <td><?php echo ucwords($upEmi['type']); ?>
            </td>
            <td><?php  if(date('d/m/Y',strtotime($upEmi['date']))=='01/01/1970') echo "NA"; else echo date('d/m/Y',strtotime($upEmi['date'])); ?>
            </td>
            
            <td><?php echo $upEmi['remarks']; ?>
            </td>
            
             <td><?php echo $upEmi['customer']['customer_name']; ?>
            </td>
            
            
            <td><?php echo $upEmi['customer']['contact_no'][0][0]; ?>
            </td>
            
            
             <td><?php if($upEmi['reg_no']=="" || $upEmi['reg_no']==null) echo "NA"; else echo $upEmi['reg_no']; ?>
            </td>
            
            <td><?php echo $upEmi['file_no']; ?>
            </td>
            
            
             	<td class="no_print"><?php if($upEmi['type']=='general') { ?> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=addRemainder&id=<?php echo $upEmi['file_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a><?php } ?>
                					<?php if($upEmi['type']=='payment') { ?> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=EMIdetails&id=<?php echo $upEmi['file_id']; ?>&state=<?php  echo getEMIIDFromPaymentId($upEmi['id']); ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a><?php } ?>
            </td>
            
             
          
  
        </tr>
        <?php
		$mj++;
		if($mj==5) break;
			}
			}
		?>
         
         </tbody>
    </table>

<a href="<?php echo WEB_ROOT ?>admin/reports/remainder_reports/custom/index.php?action=fromHomeExpired"><div class="more">View all Remainders..</div></a>

<div style="clear:both"></div>
</div>
<?php }
if(isset($_SESSION['adminSession']['report_rights']) && (in_array(101,$report_rights) || in_array(199,$report_rights)))
			{ ?>
      
     <div class="Column">
     
        
        <h4 class="widgetTitle"> 3.) Upcoming EMI Collection </h4>
         
         <table class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">Date</th>
            <th class="heading">Amount</th>
            <th class="heading">Name</th>
            <th class="heading">Contact No.</th>
             <th class="heading">Vehicle No.</th>
              <th class="heading">File No.</th>
            <th class="heading btnCol no_print"></th>
        </tr>
    </thead>
    <tbody>
         <?php
		 $today=date('Y-m-d');
	
	$to = new DateTime(date('Y-m-d'));
	$to->add(new DateInterval('P15D'));
	$to=$to->format('Y-m-d');
		 $upcomingEmis=generalEMIReportsWidget(date('d/m/Y'),$to,1,null,null,1,null);
		 uasort($upcomingEmis,'EMIDatesComparatorForEmiReportsUpcomingDate');
			$mj=0;
			if(is_array($upcomingEmis) && count($upcomingEmis)>0)
			{
		    foreach($upcomingEmis as $upEmi)
			{
			
		?>
        
         <tr class="resultRow">
        	
            <td><?php echo date('d/m/Y',strtotime($upEmi['upcoming_emi_date'])); ?>
            </td>
            
            <td><?php echo $upEmi['emi']; ?>
            </td>
            
             <td><?php echo $upEmi['customer']['customer_name']; ?>
            </td>
            
            
            <td><?php echo $upEmi['customer']['contact_no'][0][0]; ?>
            </td>
            
            
             <td><?php if($upEmi['reg_no']=="" || $upEmi['reg_no']==null) echo "NA"; else echo $upEmi['reg_no']; ?>
            </td>
            
            <td><?php echo $upEmi['file_no']; ?>
            </td>
          
            
             	<td class="no_print"> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=details&id=<?php echo $upEmi['file_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
            
            
          
  
        </tr>
        <?php
		$mj++;
		if($mj==5) break;
			}
			}
		?>
         
         </tbody>
    </table>

<a href="<?php echo WEB_ROOT ?>admin/reports/EMI_reports/custom/index.php?action=fromHomeUpcoming"><div class="more">View all Files..</div></a>

<div style="clear:both"></div>
</div>
       
        
        <div class="Column">
        <h4 class="widgetTitle"> 4.) Unpaid EMI of Open Files </h4>
        
        <table class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">Date</th>
            <th class="heading">Amount</th>
            <th class="heading">Name</th>
            <th class="heading">Contact No.</th>
             <th class="heading">Vehicle No.</th>
              <th class="heading">File No.</th>
            <th class="heading btnCol no_print"></th>
        </tr>
    </thead>
     <tbody>
         <?php
		 $upcomingEmis=generalEMIReportsWidget(null,date('d/m/Y'),0.1,null,null,1,null);
			$v=0;
			if(is_array($upcomingEmis) && count($upcomingEmis)>0)
			{
		    foreach($upcomingEmis as $upEmi)
			{
			
			if(isset($upEmi['emi']))
			{ $v++;
			if($v==6) break; 
		?>
        
         <tr class="resultRow">
        	
            <td><?php echo date('d/m/Y',strtotime($upEmi['emi_date'])); ?>
            </td>
            
            <td><?php echo $upEmi['emi']; ?>
            </td>
            
             <td><?php echo $upEmi['customer']['customer_name']; ?>
            </td>
            
            
            <td><?php echo $upEmi['customer']['contact_no'][0][0]; ?>
            </td>
            
            
             <td><?php if($upEmi['reg_no']=="" || $upEmi['reg_no']==null) echo "NA"; else echo $upEmi['reg_no']; ?>
            </td>
            
            <td><?php echo $upEmi['file_no']; ?>
            </td>
          
            
             	<td class="no_print"> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=details&id=<?php echo $upEmi['file_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
            
            
          
  
        </tr>
        <?php
			}}
			}
		?>
         
         </tbody>
    </table>

<a href="<?php echo WEB_ROOT ?>admin/reports/EMI_reports/custom/index.php?action=fromHomeExpiredOpen"><div class="more">View all Files..</div></a>

<div style="clear:both"></div>
</div>
       
        
        
        <div class="Column">
        <h4 class="widgetTitle"> 5.) Unpaid EMI of closed Files </h4>
        
         <table class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">Date</th>
            <th class="heading">Amount</th>
            <th class="heading">Name</th>
            <th class="heading">Contact No.</th>
             <th class="heading">Vehicle No.</th>
             <th class="heading">File No.</th> 
            <th class="heading btnCol no_print"></th>
        </tr>
    </thead>
    <tbody>
         <?php
		 $upcomingEmis=generalEMIReportsWidget(null,date('d/m/Y'),0.1,null,null,2,null);
		$u=0;
			if(is_array($upcomingEmis) && count($upcomingEmis)>0)
			{
		    foreach($upcomingEmis as $upEmi)
			{
			
			if(isset($upEmi['emi']))
			{ $u++;
			if($u==6) break; 
		?>
        
         <tr class="resultRow">
        	
            <td><?php echo date('d/m/Y',strtotime($upEmi['emi_date'])); ?>
            </td>
            
            <td><?php echo $upEmi['emi']; ?>
            </td>
            
             <td><?php echo $upEmi['customer']['customer_name']; ?>
            </td>
            
            
            <td><?php echo $upEmi['customer']['contact_no'][0][0]; ?>
            </td>
            
            
             <td><?php if($upEmi['reg_no']=="" || $upEmi['reg_no']==null) echo "NA"; else echo $upEmi['reg_no']; ?>
            </td>
            
            <td><?php echo $upEmi['file_no']; ?>
            </td>
          
            
             	<td class="no_print"> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=details&id=<?php echo $upEmi['file_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
            
            
          
  
        </tr>
        <?php
			}}
			}
		?>
    </table>

<a href="<?php echo WEB_ROOT ?>admin/reports/EMI_reports/custom/index.php?action=fromHomeExpiredClosed"><div class="more">View all Files..</div></a>

<div style="clear:both"></div>
</div>
       
 <?php } ?> 
 <?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(103,$report_rights) || in_array(199,$report_rights)))
			{ ?>      
        
        <div class="Column">
        <h4 class="widgetTitle"> 6.) Soon to be expired Insurance </h4>
        
         <table class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">Expiry Date</th>
            <th class="heading">Insurance Company</th>
            <th class="heading">Name</th>
            <th class="heading">Contact No.</th>
             <th class="heading">Vehicle No.</th>
              <th class="heading">File No.</th>
            <th class="heading btnCol no_print"></th>
        </tr>
    </thead>
     <tbody>
         <?php
		 $soonInsurances=generalInsuranceReportsWidget();
		 if(is_array($soonInsurances) && count($soonInsurances)>0)
			{
		    for($i=0; $i<count($soonInsurances); $i++)
			{
			$insurance=$soonInsurances[$i];
		?>
        
         <tr class="resultRow">
        	
            <td><?php echo $insurance['insurance_expiry_date']; ?>
            </td>
            
            <td><?php  $comp = getInsuranceCompanyById($insurance['insurance']['insurance_company_id']); echo $comp[1]; ?>
            </td>
            
             <td><?php echo $insurance['customer']['customer_name']; ?>
            </td>
            
            
            <td><?php echo $insurance['customer']['contact_no'][0][0]; ?>
            </td>
            
            
             <td><?php if($insurance['reg_no']=="" || $insurance['reg_no']==null) echo "NA"; else echo $insurance['reg_no']; ?>
            </td>
            
             <td><?php echo $upEmi['file_no']; ?>
            </td>
          
           
            
             <td class="no_print"> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=details&id=<?php echo $insurance['file_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
            
            
          
  
        </tr>
        <?php
			}
			}
		?>
         
         </tbody>
    </table>

<a href="<?php echo WEB_ROOT ?>admin/reports/Insurance_reports/custom/index.php?action=fromHomeUpcoming"><div class="more">View all Files..</div></a>

<div style="clear:both"></div>
</div>
       
        
        
        <div class="Column">
        <h4 class="widgetTitle"> 7.) Already Expired Insurance </h3>
        
         <table class="adminContentTable">
    <thead>
    	<tr>
        	<th class="heading">Expiry Date</th>
            <th class="heading">Insurance Company</th>
            <th class="heading">Name</th>
            <th class="heading">Contact No.</th>
             <th class="heading">Vehicle No.</th>
              <th class="heading">File No.</th>
            <th class="heading btnCol no_print"></th>
        </tr>
    </thead>
    <tbody>
         <?php
		     $expiredInsurances=expiredInsuranceReportsWidget();
			if(is_array($expiredInsurances) && count($expiredInsurances)>0)
			{
		    for($i=0; $i<count($expiredInsurances); $i++)
			{
			$eInsurance=$expiredInsurances[$i];
			
		?>
        
         <tr class="resultRow">
        	
            <td><?php echo $eInsurance['insurance_expiry_date']; ?>
            </td>
            
            <td><?php  $comp = getInsuranceCompanyById($eInsurance['insurance']['insurance_company_id']); echo $comp[1]; ?>
            </td>
            
             <td><?php echo $eInsurance['customer']['customer_name']; ?>
            </td>
            
            
            <td><?php echo $eInsurance['customer']['contact_no'][0][0]; ?>
            </td>
            
            
             <td><?php if($eInsurance['reg_no']=="" || $eInsurance['reg_no']==null) echo "NA"; else echo $eInsurance['reg_no']; ?>
            </td>
            
            <td><?php echo $upEmi['file_no']; ?>
            </td>
          
            
             	<td class="no_print"> <a href="<?php echo WEB_ROOT ?>admin/customer/index.php?view=details&id=<?php echo $eInsurance['file_id']; ?>"><button title="View this entry" class="btn viewBtn"><span class="view">V</span></button></a>
            </td>
            
            
          
  
        </tr>
        <?php
			}
			}
		?>
         
         </tbody>
    </table>

<a href="<?php echo WEB_ROOT ?>admin/reports/Insurance_reports/expired/index.php?action=fromHomeExpired"><div class="more">View all Files..</div></a>

<div style="clear:both"></div>
</div> 
       
<?php } ?>        
    
 </div>
 </div>
 <div class="clearfix"></div>
<?php
require_once("../inc/footer.php");
 ?> 