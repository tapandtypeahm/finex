 jQuery("#vehicle_company").validate({
                    expression: "if (VAL!=-1) return true; else return false;",
                    message: "Please Select the Vehicle Company!"
                });
				
jQuery("#vehicle_model").validate({
                    expression: "if (VAL!=-1) return true; else return false;",
                    message: "Please Select the Vehicle Model!"
                });
				
jQuery("#dealer").validate({
                    expression: "if (VAL!=-1) return true; else return false;",
                    message: "Please Select the Vehicle Dealer!"
                });
				
jQuery("#model").validate({
                    expression: "if (VAL!=-1) return true; else return false;",
                    message: "Please Select the Model Year!"
                });

jQuery("#type").validate({
                    expression: "if (VAL!=-1) return true; else return false;",
                    message: "Please Select the Vehicle Type!"
                });	
jQuery("#vehicle_reg_no").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please Enter the Vehicle Registration Number!"
                });
jQuery("#vehicle_chasis_no").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please Enter the Vehicle Chasis Number!"
                });		
jQuery("#vehicle_engine_no").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please Enter the Vehicle Engine Number!"
                });								
jQuery("#vehicle_reg_no").validate({
                    expression: "if (VAL.match(/^[a-z0-9]+$/i)) return true; else return false;",
                    message: "Only letters and numbers allowed! eg: GJ1AB1234 (No dashes and spaces) "
                });
jQuery("#vehicle_chasis_no").validate({
                    expression: "if (VAL.match(/^[a-z0-9]+$/i)) return true; else return false;",
                    message: "Only letters and numbers allowed! (No dashes and spaces)"
                });
jQuery("#vehicle_engine_no").validate({
                    expression: "if (VAL.match(/^[a-z0-9]+$/i)) return true; else return false;",
                    message: "Only letters and numbers allowed! (No dashes and spaces)"
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
	
function submitOurVehicle()
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
	
	$(".vehicleProofId").each(function(index, element) {
		var proofno=$(".vehicleProofNo").eq(index).val();
        var proofid=$(this).val();
		
		
		if(proofid!=-1)
		{
		
					var proofnoEl=$(".vehicleProofId").eq(index);
					proofnoEl.next().hide();
					proofnoEl.removeClass("ErrorField");
					res=res && true;
			if((proofno=="" || proofno==null))
			{
				if(proofno=="" || proofno==null)
				{
					var proofnoEl=$(".vehicleProofNo").eq(index);
					proofnoEl.next().show();
					proofnoEl.addClass("ErrorField");
					res=res && false;
					
				}
				
			}
			else if(!(proofno=="" || proofno==null))
			{
				
					var letters = /^[0-9a-zA-Z]+$/;
					if(proofno.match(letters))
					{
						
					var proofnoEl=$(".vehicleProofNo").eq(index);
					proofnoEl.next().hide();
					proofnoEl.removeClass("ErrorField");
					res=res && true;
					}
					else
					{
						
						var proofnoEl=$(".vehicleProofNo").eq(index);
						proofnoEl.next().show();
						proofnoEl.addClass("ErrorField");
						res=res && false;
						}
			}
			else
			{
				
					var proofnoEl=$(".vehicleProofNo").eq(index);
					proofnoEl.next().hide();
					proofnoEl.removeClass("ErrorField");
					res=res && true;
				
			}
		}
		else if(proofid==-1 && !((proofno=="" || proofno==null)))
		{
			
						var proofIdEl=$(".vehicleProofId").eq(index);
						proofIdEl.next().show();
						proofIdEl.addClass("ErrorField");
						res=res && false;
						
					if(!(proofno=="" || proofno==null))
					{
						
						var letters = /^[0-9a-zA-Z]+$/;
						if(proofno.match(letters))
						{
							var proofnoEl=$(".vehicleProofNo").eq(index);
							proofnoEl.next().hide();
							proofnoEl.removeClass("ErrorField");
							res=res && true;
						}
						else
						{
							
								var proofnoEl=$(".vehicleProofNo").eq(index);
								proofnoEl.next().show();
								proofnoEl.addClass("ErrorField");
								res=res && false;
						}
					}
			}
    });
	if(res==true)
	disableSubmitButton();
	return res;
	
	}	
	
function checkProofNo(proofno,el)
{
	
	var letters = /^[0-9a-zA-Z]+$/;
					
	if(proofno=="" || proofno==null)
		{
			$(el).next().hide();
			$(el).removeClass("ErrorField");
			var proofIdEl=$(el).parent().parent().prev().find(".vehicleProofId");
			
					$(proofIdEl).next().hide();
					$(proofIdEl).removeClass("ErrorField");
					
			
			}
		else if(proofno.match(letters))
		{
			$(el).next().hide();
			$(el).removeClass("ErrorField");
			var proofIdEl=$(el).parent().parent().prev().find(".vehicleProofId");
			var proofId=$(el).parent().parent().prev().find(".vehicleProofId").val();
					 if(proofId!="-1")
					{
					$(proofIdEl).next().hide();
					$(proofIdEl).removeClass("ErrorField");
					}
					else
					{
					$(proofIdEl).next().show();
					$(proofIdEl).addClass("ErrorField");
					}	
			
			}
		else
		{
			
			$(el).next().show();
			$(el).addClass("ErrorField");
			
			}	
	
	}	
function checkProofId(proofId,el)
{
						
	if(proofId!="-1")
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

function onChangeFile(proofFileName,el)
{
	
	if(!(proofFileName=="" || proofFileName==null))
					{
						
						if((/\.(gif|jpg|jpeg|pdf|png)$/i).test(proofFileName))
						{
							
							$(el).next().next().next().next().next().css('display','none');
							$(el).removeClass("ErrorField");
							res=res && true;
							}
						else
						{
							$(el).next().next().next().next().next().css('display','inline');
							$(el).addClass("ErrorField");
							res=res && false;
							
							}	
					}
	
	
	}							