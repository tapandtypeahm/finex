<div class="adminContentWrapper wrapper">

<h4 class="headingAlignment">General Settings</h4>

<div class="settingsSection">

<div class="rowOne">

     <div class="package">
     
     <a href="general_settings/ourcompany_settings/">
     <div class="squareBox">
     
         <div class="imageHolder">
         </div>
         
     </div>
     </a>
     
     
     <div class="explanation">
     Manage Our Companies
     </div>
     
     </div>
     
    
     <?php if(isset($_SESSION['adminSession']['admin_rights']) && (in_array(6,$admin_rights) || in_array(7,					$admin_rights)))
			{ ?>
     <div class="package">
     
     <a href="general_settings/adminuser_settings/">
     <div class="squareBox">
         <div class="imageHolder">
         </div>
     </div>
     </a>
     
     <div class="explanation">
     Manage Admin Users
     </div>
     
     </div>
     <?php } ?>
     <div class="package">
     
     <a href="general_settings/bank_settings/">
     <div class="squareBox">
         <div class="imageHolder">
         </div>
     </div>
     </a>
     
     <div class="explanation">
     Manage Bank Settings
     </div>
     
     </div>
     
     <div class="package">
     
     <a href="general_settings/agency_settings/">
     <div class="squareBox">
         <div class="imageHolder">
         </div>
     </div>
     </a>
     
     <div class="explanation">
     Manage Agency Settings
     </div>
     
     </div>
     
      <div class="package">
     
     <a href="general_settings/broker_settings/">
     <div class="squareBox">
         <div class="imageHolder">
         </div>
     </div>
     </a>
     
     <div class="explanation">
     Manage Broker Settings
     </div>
     
     </div>
     
  </div>
     
</div>

<h4 class="headingAlignment">City & Area Settings</h4>

<div class="settingsSection">

<div class="rowOne">
  <div class="package">
     
     <a href="general_settings/city_settings/">
     <div class="squareBox">
     
        <div class="imageHolder">
         </div>
         
     </div>
     </a>
     
     <div class="explanation">
     Manage City Settings
     </div>
     
     
     
     </div>
     <div class="package">
     
     <a href="general_settings/area_settings/">
     <div class="squareBox">
     
        <div class="imageHolder">
         </div>
         
     </div>
     </a>
     
     <div class="explanation">
     Manage Area Settings
     </div>
     
     
     
     </div>
     
     <div class="package">
     
     <a href="general_settings/merge_area_settings/">
     <div class="squareBox">
     
        <div class="imageHolder">
         </div>
         
     </div>
     </a>
     
     <div class="explanation">
     Merge Area Settings
     </div>
     
     
     
     </div>
     <div class="package">
     
     <a href="general_settings/area_group_settings/">
     <div class="squareBox">
     
        <div class="imageHolder">
         </div>
         
     </div>
     </a>
     
     <div class="explanation">
     Manage Area Group Settings
     </div>
     
     
     
     </div>
     
</div>
</div>     

<h4 class="headingAlignment">Vehicle Settings</h4>

<div class="settingsSection">

<div class="rowOne">

     <div class="package">
     
     <a href="vehicle_settings/company_settings/">
     <div class="squareBox">
         <div class="imageHolder">
         </div>
     </div>
     </a>
     
     <div class="explanation">
     Manage Vehicle companies
     </div>
     
     </div>
     
      <div class="package">
     
     <a href="vehicle_settings/model_settings/">
     <div class="squareBox">
         <div class="imageHolder">
         </div>
     </div>
     </a>
     
     <div class="explanation">
     Manage Vehicle models
     </div>
     
     </div>
     
      <div class="package">
     
     <a href="vehicle_settings/type_settings/">
     <div class="squareBox">
         <div class="imageHolder">
         </div>
     </div>
     </a>
     
     <div class="explanation">
    Manage Vehicle Types
     </div>
     
     </div>
     
     <div class="package">
     
     <a href="vehicle_settings/dealer_settings/">
     <div class="squareBox">
         <div class="imageHolder">
         </div>
     </div>
     </a>
     
     <div class="explanation">
     Manage Vehicle Dealers
     </div>
     
     </div>
     
     
     
  </div>
     
</div>

<h4 class="headingAlignment">Insurance Settings</h4>

<div class="settingsSection">

<div class="rowOne">

     <div class="package">
     
     <a href="insurance_settings/company_settings/">
     <div class="squareBox">
         <div class="imageHolder">
         </div>
     </div>
     </a>
     
     <div class="explanation">
     Manage Insurance companies
     </div>
     
     </div>
     
      
  </div>
     
</div>


<h4 class="headingAlignment">Backup & Restore</h4>

<div class="settingsSection">

<div class="rowOne">

     <div class="package">
     
     <a href="backup_restore/backup/">
     <div class="squareBox">
         <div class="imageHolder">
         </div>
     </div>
     </a>
     
     <div class="explanation">
     Backup
     </div>
     
     </div>
     
      <div class="package">
     
     <a href="backup_restore/restore/">
     <div class="squareBox">
         <div class="imageHolder">
         </div>
     </div>
     </a>
     
     <div class="explanation">
     Restore
     </div>
     
     </div>
     
      
  </div>
     
</div>


<!--<h4 class="headingAlignment">General Settings</h4>
    <ul class="secondaryList">
    	<a href="general_settings/ourcompany_settings/"><li>Manage Our Companies</li>
        <a href="general_settings/city_settings/"><li>Manage Cities</li>
        <a href="general_settings/adminuser_settings/"><li>Manage Admin Users</li>
        <a href="general_settings/bank_settings/"><li>Manage Bank Settings</li>
        <a href="general_settings/agency_settings/"><li>Manage Agency Settings</li>
    </ul>
    
    <h4 class="headingAlignment">Vehicle Settings</h4>
    
    <ul class="secondaryList">
    	<a href="vehicle_settings/company_settings/"><li>Manage Vehicle companies</li>
        <a href="vehicle_settings/type_settings/"><li>Manage Vehicle Types</li>
        <a href="vehicle_settings/dealer_settings/"><li>Manage Vehicle Dealers</li>
        
    </ul>-->
</div> 