// JavaScript Document

 jQuery("#txtPassword").validate({
                    expression: "if (VAL.length > 4 && VAL) return true; else return false;",
                    message: "Please enter a valid Password (5 digits or more)"
                });
                jQuery("#txtConfirmPassword").validate({
                    expression: "if ((VAL == jQuery('#txtPassword').val()) && VAL) return true; else return false;",
                    message: "Confirm password field doesn't match the password field"
                });		
	 jQuery("#txtOldPassword").validate({
                    expression: "if (VAL.length > 4 && VAL) return true; else return false;",
                    message: "Please enter a valid Password (5 digits or more)"
                });				
	