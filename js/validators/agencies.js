// JavaScript Document

				 jQuery("#txtName").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the Agency name"
                });
				 jQuery("#txtName").validate({
                     expression: "if (VAL.match(/^[a-zA-Z0-9().:;,*@#& ]+$/)) return true; else return false;",
                    message: "Only letters and Dot(.) allowed!"
                });
				jQuery("#txtPrefix").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter a valid Prefix"
                });
                jQuery("#txtPrefix").validate({
                    expression: "if (VAL.match(/^[a-z]+$/i)) return true; else return false;",
                    message: "Only letters allowed in prefix!"
                });
				jQuery("#txtPrefix").validate({
                    expression: "if (VAL.length < 5) return true; else return false;",
                    message: "Prefix should 4 characters or less!"
                });

function submitOurAgency()
{
	
	var res=true;
	var contactPerson=$('#txtPersonName').val();
	var phone=$('#txtPhone').val();
	var regexLetter = /^[a-zA-Z ]*$/;
	if(contactPerson=="" || contactPerson==null)
		{
			
			$('#txtPersonName').next().hide();
			$('#txtPersonName').removeClass("ErrorField");
			res=res && true;
			
			}
		else if(regexLetter.test(contactPerson))
		{
			
			$('#txtPersonName').next().hide();
			$('#txtPersonName').removeClass("ErrorField");
			res=res && true;
			
			}
		else
		{
			
			$('#txtPersonName').next().show();
			$('#txtPersonName').addClass("ErrorField");
			res=res && false;
			}	
	
	if(phone=="" || phone==null)
		{
			
			$('#txtPhone').next().hide();
			$('#txtPhone').removeClass("ErrorField");
			res=res && true;
			
			}
		else if($.isNumeric(phone))
		{
			
			$('#txtPhone').next().hide();
			$('#txtPhone').removeClass("ErrorField");
			res=res && true;
			
			}
		else
		{
			
			$('#txtPhone').next().show();
			$('#txtPhone').addClass("ErrorField");
			res=res && false;
			}	
	
	
	return res;
	
	}				