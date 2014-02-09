// JavaScript Document

function calculatePenalty()
{
	

  var penalty_mode=document.getElementById('penalty_mode').value;
 var amunt_interest=document.getElementById('amount_interest').value;
  var loan_id=document.getElementById('loan_id').value;
 var uptoDate=document.getElementById('raminderDate').value;
 var penalty_paid=document.getElementById('penalty_paid').innerHTML;

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
    var res=xmlhttp.responseText;
	var penalty_left=parseInt(res)-parseInt(penalty_paid);
	document.getElementById('total_penalty').innerHTML=res;
	document.getElementById('penalty_left').innerHTML=penalty_left;
    }
  }
xmlhttp.open("GET","calculatePenalty.php?mode="+penalty_mode+"&amount_interest="+amunt_interest+"&lid="+loan_id+"&uptoDate="+uptoDate,true);
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
