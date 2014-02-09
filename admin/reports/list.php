<div class="adminContentWrapper wrapper">
<?php
if(isset($_SESSION['adminSession']['report_rights']))
{
	$report_rights=$_SESSION['adminSession']['report_rights'];
	}
 if(isset($_SESSION['adminSession']['report_rights']) && (in_array(101,$report_rights) || in_array(199,$report_rights)))
			{ ?>
<h4 class="headingAlignment">EMI Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="EMI_reports/daily">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Daily EMI Reports
         </div>
     
     </div>
     
      <div class="package">
     
         <a href="EMI_reports/weekly">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Next 15 Days EMI Reports
         </div>
     
     </div>
     
     <div class="package">
     
         <a href="EMI_reports/expired">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Expired EMI Reports
         </div>
     
     </div>
     
     <div class="package">
     
         <a href="EMI_reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Custom Emi Reports
         </div>
     
     </div>
     
      <div class="package">
     
         <a href="EMI_reports/custom_payment_date">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Emi Reports By Payment Date
         </div>
     
     </div>
     
      <div class="package">
     
         <a href="EMI_reports/loan_starter">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Non Starter Reports
         </div>
     
     </div>
     
</div>
</div> 
<?php } ?>


<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(102,$report_rights) || in_array(199,$report_rights)))
			{ ?>
            
<h4 class="headingAlignment">Cheque Return Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="cheque_reports/return">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Cheque Return Reports
         </div>
     
     </div>
</div>
</div>     
<?php } ?> 


<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(103,$report_rights) || in_array(199,$report_rights)))
			{ ?>

<h4 class="headingAlignment">Insurance Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="Insurance_reports/daily">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Daily Insurance Reports
         </div>
     
     </div>
     
<!--      <div class="package">
     
         <a href="EMI_reports/weekly">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Next 15 Days Insurance Reports
         </div>
     
     </div> -->
     
     <div class="package">
     
         <a href="Insurance_reports/expired">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Expired Insurance Reports
         </div>
     
     </div>
     
     <div class="package">
     
         <a href="Insurance_reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Custom Insurance Reports
         </div>
     
     </div>
     
</div>
</div>  

<?php } ?>


<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(104,$report_rights) || in_array(199,$report_rights)))
			{ ?>

<h4 class="headingAlignment">Rasid Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="RasidReports/daily">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Daily Rasid Reports
         </div>
     
     </div>
     
      
     
   
     
     <div class="package">
     
         <a href="RasidReports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Custom Rasid Reports
         </div>
     
     </div>
     
      <div class="package">
     
         <a href="RasidReports/Custom_entry">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Custom Rasid Reports By Entry Date
         </div>
     
     </div>
     
      <div class="package">
     
         <a href="RasidReports/stfc">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         STFC Reports
         </div>
     
     </div>
     
     
</div>
</div> 

<?php  } ?>


<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(105,$report_rights) || in_array(199,$report_rights)))
			{ ?>

<h4 class="headingAlignment">Reminder Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="remainder_reports/daily">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Daily Reminder Reports
         </div>
     
     </div>
     
<!--      <div class="package">
     
         <a href="EMI_reports/weekly">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Next 15 Days EMI Reports
         </div>
     
     </div> -->
     
     <div class="package">
     
         <a href="remainder_reports/expired">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Expired Reminder Reports
         </div>
     
     </div>
     
     <div class="package">
     
         <a href="remainder_reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Custom Reminder Reports
         </div>
     
     </div>
</div>
</div>  



<?php } ?>   




<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(106,$report_rights) || in_array(199,$report_rights)))
			{ ?>

<h4 class="headingAlignment">File Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="file_reports/daily">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Daily File Reports
         </div>
     
     </div>
     
<!--      <div class="package">
     
         <a href="EMI_reports/weekly">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Next 15 Days EMI Reports
         </div>
     
     </div> -->
     
     <div class="package">
     
         <a href="file_reports/closed">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Closed File Reports
         </div>
     
     </div> 
     
     <div class="package">
     
         <a href="file_reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Custom File Reports
         </div>
     
     </div>
     
</div>
</div>


<?php } ?>


<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(107,$report_rights) || in_array(199,$report_rights)))
			{ ?>

<h4 class="headingAlignment">Loan Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="loan_reports/daily">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Daily Loan Reports
         </div>
     
     </div>
     
<!--      <div class="package">
     
         <a href="EMI_reports/weekly">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Next 15 Days EMI Reports
         </div>
     
     </div> 
     
     <div class="package">
     
         <a href="remainder_reports/expired">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Expired Remainder Reports
         </div>
     
     </div> -->
     
     <div class="package">
     
         <a href="loan_reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Custom Loan Reports
         </div>
     
     </div>

</div> 
</div>  



<?php  } ?>


<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(108,$report_rights) || in_array(199,$report_rights)))
			{ ?>

<h4 class="headingAlignment">Account Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="account_reports/daily">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Daily Account Reports
         </div>
     
     </div>
     
<!--      <div class="package">
     
         <a href="EMI_reports/weekly">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Next 15 Days EMI Reports
         </div>
     
     </div> -->
     
     <div class="package">
     
         <a href="account_reports/closed">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
        Closed File Account Reports
         </div>
     
     </div> 
     
     <div class="package">
     
         <a href="account_reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Custom Account Reports
         </div>
     
     </div>

</div> 
</div>  


<?php } ?>


<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(109,$report_rights) || in_array(199,$report_rights)))
			{ ?>

<h4 class="headingAlignment">Collection Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="collection_reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Collection Reports
         </div>
     
     </div>
</div>
</div>  


<?php  } ?>  

<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(115,$report_rights) || in_array(199,$report_rights)))
			{ ?>    

<h4 class="headingAlignment">Vehicle Seize Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="vehicle_seize_reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Vehicle Seize Reports
         </div>
     
     </div>
</div>
</div>     

<?php } ?>


<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(113,$report_rights) || in_array(199,$report_rights)))
			{ ?>

<h4 class="headingAlignment">Capital And Interest Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="capital_reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Capital And Interest Reports
         </div>
     
     </div>


	 <div class="package">
     
         <a href="interest_gained_reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Interest Gained Reports
         </div>
     
     </div>
</div>     

</div>  


<?php  } ?>  


<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(114,$report_rights) || in_array(199,$report_rights)))
			{ ?>

<h4 class="headingAlignment">Loan Starting & Ending Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="loan_starting_ending/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Loan Starting & Ending Reports
         </div>
     
     </div>
</div>
</div>  


<?php  } ?>  


<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(110,$report_rights) || in_array(199,$report_rights)))
			{ ?>

<h4 class="headingAlignment">Company Paid Date Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="Company_paid_Reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Company Paid Date Reports
         </div>
     
     </div>
</div>
</div>  


<?php } ?>


<?php if(isset($_SESSION['adminSession']['report_rights']) && (in_array(111,$report_rights) || in_array(199,$report_rights)))
			{ ?>    

<h4 class="headingAlignment">Full Custom Reports</h4>

<div class="settingsSection">

<div class="rowOne">

	 <div class="package">
     
         <a href="custom_reports/custom">
         <div class="squareBox">
         
             <div class="imageHolder">
             </div>
             
         </div>
         </a>
     
     
         <div class="explanation">
         Full Custom Reports
         </div>
     
     </div>
</div>
</div>     

<?php } ?>

</div> 