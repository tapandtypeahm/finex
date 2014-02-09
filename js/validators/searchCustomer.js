// JavaScript Document

 jQuery("#agreementNo").validate({
                    expression: "if (!VAL || VAL.match(/^[a-z0-9]+$/i)) return true; else return false;",
                    message: "Only letters and numbers allowed!"
                });
				jQuery("#agreementNo").validate({
                    expression: "if (VAL.length < 40) return true; else return false;",
                    message: "Agreement number should 4 characters or less!"
                });

 jQuery("#fileNumber").validate({
                    expression: "if (!VAL || VAL.match(/^[a-z0-9/]+$/i)) return true; else return false;",
                    message: "Only letters and numbers and / allowed!"
                });
				jQuery("#fileNumber").validate({
                    expression: "if (VAL.length < 40) return true; else return false;",
                    message: "Agreement number should 4 characters or less!"
                });				
				
jQuery("#reg_no").validate({
                    expression: "if (!VAL || VAL.match(/^[a-z0-9]+$/i)) return true; else return false;",
                    message: "Only letters and numbers allowed!"
                });
				jQuery("#reg_no").validate({
                    expression: "if (VAL.length < 40) return true; else return false;",
                    message: "Agreement number should 4 characters or less!"
                });				

jQuery("#mobile_no").validate({
                    expression: "if (!VAL || (!isNaN(VAL) && VAL.length>6)) return true; else return false;",
                    message: "Only digits allowed in contact no (min 7 digits)!"
                });
				 		
				 jQuery("#name").validate({
                     expression: "if (!VAL || VAL.match(/^[a-zA-Z. ]+$/)) return true; else return false;",
                    message: "Only letters and Dot(.) allowed!"
                });		
  				