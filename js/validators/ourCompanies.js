
                jQuery("#txtname").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the Company name"
                });
				 jQuery("#txtname").validate({
                     expression: "if (VAL.match(/^[a-zA-Z0-9&().:;,*@# ]+$/)) return true; else return false;",
                    message: "Only letters and dot(.) allowed!"
                });
                jQuery("#txtpincode").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter a pincode"
                });
				jQuery("#txtpincode").validate({
                    expression: "if (!isNaN(VAL)) return true; else return false;",
                    message: "Only digits allowed in pincode!"
                });
				 jQuery("#txtpincode").validate({
                    expression: "if (VAL.length==6) return true; else return false;",
                    message: "Pincode should be 6 digits long!"
                });
				
                jQuery("#txtaddress").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter address"
                });
				jQuery("#txtprefix").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter a valid Prefix"
                });
                jQuery("#txtprefix").validate({
                    expression: "if (VAL.match(/^[a-z0-9]+$/i)) return true; else return false;",
                    message: "Only letters and numbers allowed in prefix!"
                });
				
				jQuery("#txtsubheading").validate({
                    expression: "if (VAL.match(/^[a-zA-Z0-9(),.%&: ]+$/i) || (!VAL)) return true; else return false;",
                    message: "Only letters and numbers allowed in Subheading!"
                });
				
				jQuery("#txtprefix").validate({
                    expression: "if (VAL.length < 5) return true; else return false;",
                    message: "Prefix should 4 characters or less!"
                });
                jQuery("#txtcity").validate({
                    expression: "if (VAL > 0 && VAL) return true; else return false;",
                    message: "Please select a city"
                });

function submitOurCompany()
{
	var res=true;
	var i=0;
	$( ".contact" ).each(function( index ) {
  	  	
		var contact=$(this).val();
		if(contact=="" || contact==null)
		{
			$(this).next().next().hide();
			$(this).removeClass("ErrorField");
			res=res && true;
			
			}
		else if(contact.length>=10 && contact.length<=13 && $.isNumeric(contact))
		{
			$(this).next().next().hide();
			$(this).removeClass("ErrorField");
			res=res && true;
			
			}
		else
		{
			
			$(this).next().next().show();
			$(this).addClass("ErrorField");
			res=res && false;
			}	
	  
	});
	return res;
}