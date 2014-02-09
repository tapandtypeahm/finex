// JavaScript Document

jQuery("#name").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the Vehicle Dealer name"
                });
				
				 jQuery("#city").validate({
                     expression: "if (!isNaN(VAL) && VAL!=-1) return true; else return false;",
                    message: "Please Select City!"
                });
				
				
function checkCheckBox()
{
	var checked=false;
	$(".company").each(function(index, element) {
       
	   if(element.checked)
	   checked=true;
	    
    });
	if(checked==false)
	alert("Please Check atleast One Company!");
	return checked;
	}				