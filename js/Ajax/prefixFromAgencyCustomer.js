// JavaScript Document

function getPrefixFromAgency(id)
{
if (id==-1)
  {
  document.getElementById('agencyPrefix').innerHTML="";
  return false;
  } 
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {	
    var res=eval(xmlhttp.responseText);
	document.getElementById('agencyPrefix').innerHTML=res[0];
	document.agency_type=res[1];
	if(res[1]==2)
	{
		
		document.getElementById('agencyParticipationDetails').style.display="block";		
	}
	else if(res[1]==1)
	{
		document.getElementById('agencyParticipationDetails').style.display="none";	
		}
    }
  }
xmlhttp.open("GET","getPrefixFromAgency.php?p="+id,true);
xmlhttp.send();

	elem=document.getElementById('agreementNo');
	elemValue=document.getElementById('agreementNo').value;
	url="ajax/agreementNo.php?id=";
	url=url+id+"&value="+elemValue;	
	

		var xmlhttp2;
	
	  xmlhttp2 = new XMLHttpRequest();
	 
	
	 
	  

	  xmlhttp2.onreadystatechange=function()                        
	  {
		if (xmlhttp2.readyState==4 && xmlhttp2.status==200)
		{   
		
		var myarray=eval(xmlhttp2.responseText);
		
		if(myarray!=0)
		{
			$(elem).addClass("ErrorField");
			$(elem).next(".availError").show();
		}
		else
		{
			$(elem).removeClass("ErrorField");
			$(elem).next(".availError").hide();
			}	
		}
	  }
  
  xmlhttp2.open('GET', url, true );    
  xmlhttp2.send(null);
	
	}

function checkAgreementFromAgencyDropDown()
{
	}	
