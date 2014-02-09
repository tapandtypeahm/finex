// JavaScript Document

function scanCustomerProof(elem,ipname)
{
	$(elem).attr("disabled","disabled");
	ipname='customerProofImg['+ipname+'][]';
		
	var xmlhttp;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp = new XMLHttpRequest();
	 
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  }

	  xmlhttp.onreadystatechange=function()                        
	  {
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{   
		
		var myarray=eval(xmlhttp.responseText);
		
		
		if(myarray!="0" || myarray!="1")
		{
			
			alert(myarray);
		}
		else if(myarray=="1")
		{
			
			alert("Scanner In Use!");
			}
		else
		{
			
			alert("Scanning Eroor! Restart Computer to use it!");
			}		
		}
	  }
  
  xmlhttp.open('GET', "/finex/lib/scanLib/customerScan.php", true );    
  xmlhttp.send(null);
	
}
function scanGuarantorProof(elem,ipname)
{
	$(elem).attr("disabled","disabled");
	ipname='guarantorProofImg['+ipname+'][]';
		
	$(elem).after("<scan class='scanningSpan'>Scanning...</scan>");
	
	var xmlhttp;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp = new XMLHttpRequest();
	 
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  }

	  xmlhttp.onreadystatechange=function()                        
	  {
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{   
		
		var myarray=eval(xmlhttp.responseText);
		alert(myarray);
		
		if(myarray!="0" || myarray!="1")
		{
			
			$(elem).after("<input type='hidden' name='"+ipname+"' value='"+myarray+"'/>");
			$('.scanningSpan').html()="DONE";
		}
		else if(myarray=="1")
		{
			
			alert("Scanner In Use!");
			}
		else
		{
			
			alert("Scanning Eroor! Restart Computer to use it!");
			}		
		}
	  }
  
  xmlhttp.open('GET', "/finex/lib/scanLib/guarantorScan.php", true );    
  xmlhttp.send(null);
	
}
function scanVehicleProof(elem,ipname)
{
	$(elem).attr("disabled","disabled");
	ipname='vehicleProofImg['+ipname+'][]';
		
	$(elem).after("<scan class='scanningSpan'>Scanning...</scan>");
	
	var xmlhttp;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp = new XMLHttpRequest();
	 
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  }

	  xmlhttp.onreadystatechange=function()                        
	  {
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{   
		
		var myarray=eval(xmlhttp.responseText);
		alert(myarray);
		
		if(myarray!="0" || myarray!="1")
		{
			
			$(elem).after("<input type='hidden' name='"+ipname+"' value='"+myarray+"'/>");
			$('.scanningSpan').html()="DONE";
		}
		else if(myarray=="1")
		{
			
			alert("Scanner In Use!");
			}
		else
		{
			
			alert("Scanning Eroor! Restart Computer to use it!");
			}		
		}
	  }
  
  xmlhttp.open('GET', "/finex/lib/scanLib/vehicleScan.php", true );    
  xmlhttp.send(null);
	
}
function scanInsuranceProof(elem,ipname)
{
	$(elem).attr("disabled","disabled");
	ipname='insuranceImg['+ipname+'][]';
		
	$(elem).after("<scan class='scanningSpan'>Scanning...</scan>");
	
	var xmlhttp;
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp = new XMLHttpRequest();
	 
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	  }

	  xmlhttp.onreadystatechange=function()                        
	  {
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{   
		
		var myarray=eval(xmlhttp.responseText);
		alert(myarray);
		
		if(myarray!="0" || myarray!="1")
		{
			
			$(elem).after("<input type='hidden' name='"+ipname+"' value='"+myarray+"'/>");
			$('.scanningSpan').html()="DONE";
		}
		else if(myarray=="1")
		{
			
			alert("Scanner In Use!");
			}
		else
		{
			
			alert("Scanning Eroor! Restart Computer to use it!");
			}		
		}
	  }
  
  xmlhttp.open('GET', "/finex/lib/scanLib/insuranceScan.php", true );    
  xmlhttp.send(null);
	
}