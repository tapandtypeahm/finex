 jQuery("#insurance_company").validate({
                    expression: "if (VAL!=-1) return true; else return false;",
                    message: "Please Select the Insurance Company!"
                });
jQuery("#idv").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please Enter the IDV (Insurance Declared Value)!"
                });
jQuery("#premium").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please Enter premium!"
                });		
jQuery("#idv").validate({
                    expression: "if (!isNaN(VAL)) return true; else return false;",
                    message: "Only digits allowed in Insurance Declared Value!"
                });		
jQuery("#premium").validate({
                    expression: "if (!isNaN(VAL)) return true; else return false;",
                    message: "Only digits allowed in premium!"
                });																											
				
function onChangeDate(date,el)
{
		
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
	
	
	}	
	
function submitInsurance()
{
	var res=true;
	$( ".date" ).each(function( index ) {
		
		
		var date=$(this).val();
		dateFormat=/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/g;
		
		if(dateFormat.test(date))
		{
			$(this).next().hide();
			$(this).removeClass("ErrorField");
			res=res && true;
			
			}
		else
		{
			$(this).next().show();
			$(this).addClass("ErrorField");
			res=res && false;
			}	
		
	});
	
	$('.customerFile').each(function(index, element) {
        
		var proofFileName=$(this).val();
		
		if(!(proofFileName=="" || proofFileName==null))
					{
						
						if((/\.(gif|jpg|jpeg|pdf|png)$/i).test(proofFileName))
						{
							
							$(this).next().next().next().next().hide();
							$(this).removeClass("ErrorField");
							res=res && true;
							}
						else
						{
							
							$(this).next().next().next().next().css('display','inline');
							$(this).addClass("ErrorField");
							res=res && false;
							}	
					}
    });
	
	if(res==true)
	disableSubmitButton();
	return res;
	
	}	
	


function onChangeFile(proofFileName,el)
{
	
	if(!(proofFileName=="" || proofFileName==null))
					{
						
						if((/\.(gif|jpg|jpeg|pdf|png)$/i).test(proofFileName))
						{
							
							$(el).next().next().next().next().hide();
							$(el).removeClass("ErrorField");
							}
						else
						{
							$(el).next().next().next().next().css('display','inline');
							$(el).addClass("ErrorField");
							}	
					}
	
	
	}							