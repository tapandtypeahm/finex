// JavaScript Document

jQuery("#txtName").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the bank name"
                });
jQuery("#txtName").validate({
                    expression: "if (VAL.match(/^[a-zA-Z ]+$/)) return true; else return false;",
                    message: "Only letters allowed!"
                });	
 jQuery("#txtEmail").validate({
                    expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                    message: "Please enter a valid Email id"
                });
												
jQuery("#txtUsername").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the Username"
                });	
							
	jQuery("#txtUsername").validate({
                    expression: "if (VAL.match(/^[a-zA-Z0-9]+$/)) return true; else return false;",
                    message: "Only letters and digits allowed!"
                });		
				
	 jQuery("#txtPassword").validate({
                    expression: "if (VAL.length > 4 && VAL) return true; else return false;",
                    message: "Please enter a valid Password (5 digits or more)"
                });
                jQuery("#txtConfirmPassword").validate({
                    expression: "if ((VAL == jQuery('#txtPassword').val()) && VAL) return true; else return false;",
                    message: "Confirm password field doesn't match the password field"
                });		
				
function checkContactNo(contact,contactInput)
{
	alert('here');
	if(contact=="" || contact==null)
		{
			$(contactInput).next().next().hide();
			$(contactInput).removeClass("ErrorField");
			res=res && true;
			
			}
		else if(contact.length>6 && contact.length<13 && $.isNumeric(contact))
		{
			
			$(contactInput).next().next().hide();
			$(contactInput).removeClass("ErrorField");
			res=res && true;
			
			}
		else
		{
			
			$(contactInput).next().next().show();
			$(contactInput).addClass("ErrorField");
			res=res && false;
			}	
	
	}					
	