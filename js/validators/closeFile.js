// JavaScript Document
if($('#mode').is(':checked'))
{
	$('#chequePaymentTable').show();
}
$('.chequePaymentTable').hide();
	jQuery("#amount").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter amount!"
                });
				jQuery("#amount").validate({
                    expression: "if (!isNaN(VAL) && VAL>=0) return true; else return false;",
                    message: "Only digits allowed in amount!"
                });
jQuery("#rasid_no").validate({
                    expression: "if ((!VAL) || (VAL>0 && !isNaN(VAL))) return true; else return false;",
                    message: "Please Enter a valid Rasid No!"
                });				
			
				 jQuery("#remarks").validate({
                     expression: "if (VAL=='' || VAL.match(/^[a-zA-Z0-9().:;,*@#& ]+$/)) return true; else return false;",
                    message: "Omly Letters, Space and Dot(.) Allowed!"
                });
				jQuery("#bank").validate({
                    expression: "if (!$('#mode').is(':checked') || (VAL)) return true; else return false;",
                    message: "Please Enter the bank name!"
                });
				jQuery("#branch").validate({
                    expression: "if (!$('#mode').is(':checked') || (VAL)) return true; else return false;",
                    message: "Please Enter the branch name!"
                });
				jQuery("#cheque_no").validate({
                    expression: "if (!$('#mode').is(':checked') || (VAL>0 && !isNaN(VAL) && VAL.length==6)) return true; else return false;",
                    message: "Please Enter the Cheque Number(Only 6 Digits)!"
                });
				
				
	function onChangeDate(date,el)
{
		var elName=$(el).attr('name');
		dateFormat=/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/g;
		if(dateFormat.test(date))
		{
			$(el).next().hide();
			$(el).removeClass("ErrorField");
			
			}
		else
		{
			
			$(el).next().show();
			$(el).addClass("ErrorField");
			
		}	
		
		if(elName=="cheque_date")
		{
			var loan_in_cash=document.getElementById('loan_amount_type_cash');
			if(loan_in_cash.checked)
			{
				$(el).next().hide();
			$(el).removeClass("ErrorField");
				}
		}
		
		if(elName=="approvalDate")
		{
			var starting_date=$('#startingDate').val();
			var cheque_date=$('#chequeDate').val();
			if(starting_date==null || starting_date=="")
			{
			document.getElementById('startingDate').value=$(el).val();
			}
			if(cheque_date==null || cheque_date=="")
			{
			document.getElementById('chequeDate').value=$(el).val();
			}
		}
	
	
	}	
	
function submitClosure()
{
	
			
	var res=true;
	var date1=$(".datepicker1").val();
	var date3=$(".datepicker3").val();
	
	
		dateFormat=/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/g;
		
		if(dateFormat.test(date1))
		{
			
			$(".datepicker1").next().hide();
			$(".datepicker1").removeClass("ErrorField");
			res=res && true;
			
			}
		else
		{
			$(".datepicker1").next().show();
			$(".datepicker1").addClass("ErrorField");
			res=res && false;
			}
		
		if(document.getElementById('mode').checked)		
		{
			dateFormat=/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/g;
		
			if(dateFormat.test(date3))
			{
			
			$(".datepicker3").next().hide();
			$(".datepicker3").removeClass("ErrorField");
			res=res && true;
			
			}
		else
		{
			$(".datepicker3").next().show();
			$(".datepicker3").addClass("ErrorField");
			res=res && false;
			}
			
			
		}	
		
		if(res==true)
		disableSubmitButton();
		return res;
	}	
	
$(".availError").hide();


function checkRasidNo()
{
	var rasid_no = document.getElementById('rasid_no').value;
	var elem=document.getElementById('rasid_no');
	
	elemValue=rasid_no;
	if(elemValue!=null && elemValue!="")
	{
	var agency_id = document.getElementById('agency_id').value;
	var oc_id = document.getElementById('oc_id').value;
	var old_rasid_no = document.getElementById('old_rasid_no').value;
	var file_id = document.getElementById('file_id').value;
	var url="ajax/rasidNo.php?value="+rasid_no+"&agency_id="+agency_id+"&oc_id="+oc_id+"&old="+old_rasid_no+"&file_id="+file_id;
	
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
function checkMode(mode)
{
	if(mode==2)
	{
		$('#chequePaymentTable').show();
		}
	else
	{
			$('#chequePaymentTable').hide();
		}	
	}					