// JavaScript Document
$(".availError").hide();
function checkAvailibilty(elem,showId,url,dependId)
{
	elemValue=elem.value;
	if(elemValue!=null && elemValue!="")
	{
	if(dependId!=null && dependId!="")
	url=url+$('#'+dependId+' :selected').val()+"&value="+elemValue;
	else if(url.indexOf("?")!=-1)
	url=url+"&value="+elemValue;	
	else
	url=url+"?value="+elemValue;	
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
		
		var myarray=xmlhttp.responseText;
		
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
  
  xmlhttp.open('GET', url, true );    
  xmlhttp.send(null);
	}
}