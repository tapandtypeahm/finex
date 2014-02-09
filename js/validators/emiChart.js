// JavaScript Document

jQuery("#start_amount").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter Starting amount!"
                });
				jQuery("#start_amount").validate({
                    expression: "if (!isNaN(VAL) && VAL>0) return true; else return false;",
                    message: "Only digits allowed in amount!"
                });
				
jQuery("#end_amount").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter Ending amount!"
                });
				jQuery("#end_amount").validate({
                    expression: "if (!isNaN(VAL) && VAL>0) return true; else return false;",
                    message: "Only digits allowed in amount!"
                });	
				
jQuery("#month").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter months interval!"
                });
				jQuery("#month").validate({
                    expression: "if (!isNaN(VAL) && VAL>0) return true; else return false;",
                    message: "Only digits allowed in months interval!"
                });	

jQuery("#amount_interval").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter amount interval!"
                });
				jQuery("#amount_interval").validate({
                    expression: "if (!isNaN(VAL) && VAL>0) return true; else return false;",
                    message: "Only digits allowed in amount interval!"
                });									
				
jQuery("#roi").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter Rate of Interest (annually in %)"
                });
				jQuery("#roi").validate({
                    expression: "if (!isNaN(VAL) && VAL>0) return true; else return false;",
                    message: "Only digits allowed in Rate of Interest!"
                });							