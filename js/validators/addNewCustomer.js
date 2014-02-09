
// JavaScript Document

				 jQuery("#agency_id").validate({
                    expression: "if (VAL!=-1) return true; else return false;",
                    message: "Please Select the Agency!"
                });
				 jQuery("#broker_id").validate({
                    expression: "if (VAL!=-1) return true; else return false;",
                    message: "Please Select the Broker!"
                });
				 jQuery(".city").validate({
                    expression: "if (VAL>0) return true; else return false;",
                    message: "Please Select the City!"
                });
				 jQuery(".city_area").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please Enter the Area!"
                });
				 jQuery(".city_area").validate({
                   expression: "if (VAL.match(/^[a-zA-Z. ]+$/)) return true; else return false;",
                    message: "Only letters allowed!"
                });
				 jQuery(".person_name").validate({
                     expression: "if (VAL) return true; else return false;",
                    message: "Please enter name!"
                });
				 jQuery(".person_name").validate({
                     expression: "if (VAL.match(/^[a-zA-Z.()-,: ]+$/)) return true; else return false;",
                    message: "Only letters and Dot(.) allowed!"
                });
				 jQuery(".address").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter address!"
                });
				jQuery("#guarantor_city_id").validate({
                    expression: "if (VAL>0 || ((jQuery('#guarantor_name').val()=='') && VAL<0)) return true; else return false;",
                    message: "Please Select the City!"
                });
				 jQuery("#city_area2").validate({
                    expression: "if (((jQuery('#guarantor_name').val()=='') && !VAL) || VAL) return true; else return false;",
                    message: "Please Enter the Area!"
                });
				 jQuery("#city_area2").validate({
                   expression: "if (((jQuery('#guarantor_name').val()=='') && !VAL) || VAL.match(/^[a-zA-Z. ]+$/)) return true; else return false;",
                    message: "Only letters allowed!"
                });
				 jQuery("#guarantor_name").validate({
                     expression: "if ((!VAL && (jQuery('#guarantor_address').val()=='' && jQuery('#city_area2').val()=='' && jQuery('#guarantor_city_id').val()<0 && jQuery('#guarantor_pincode').val()=='' && jQuery('#guarantorContact').val()=='')) || VAL) return true; else return false;",
                    message: "Please Enter Name!"
                });
				 jQuery("#guarantor_name").validate({
                     expression: "if (((!VAL || VAL.match(/^[a-zA-Z.()-,: ]+$/)) && (jQuery('#guarantor_address').val()=='' && jQuery('#city_area2').val()=='' && jQuery('#guarantor_city_id').val()<0 && jQuery('#guarantor_pincode').val()=='' && jQuery('#guarantorContact').val()=='')) || VAL.match(/^[a-zA-Z.()-,: ]+$/)) return true; else return false;",
                    message: "Only letters and Dot(.) allowed!"
                });
				 jQuery("#guarantor_address").validate({
                    expression: "if (((jQuery('#guarantor_name').val()=='') && !VAL) || VAL) return true; else return false;",
                    message: "Please enter address!"
                });
				jQuery("#guarantorContact").validate({
                    expression: "if (((jQuery('#guarantor_name').val()=='') && !VAL) || VAL.match(/^[0-9]+$/i)) return true; else return false;",
                    message: "Only digits allowed in contact no!"
                });
				 jQuery("#guarantorContact").validate({
                    expression: "if (((jQuery('#guarantor_name').val()=='') && !VAL) || VAL.length==10) return true; else return false;",
                    message: "contact no should be 10 digits long!"
                });
				jQuery(".pincode").validate({
                    expression: "if (!VAL || (!isNaN(VAL) && VAL>0)) return true; else return false;",
                    message: "Only digits allowed in pincode!"
                });
				 jQuery(".pincode").validate({
                    expression: "if (!VAL || VAL.length==6) return true; else return false;",
                    message: "Pincode should be 6 digits long!"
                });
				jQuery("#agreementNo").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter an Agreement number!"
                });
                jQuery("#agreementNo").validate({
                    expression: "if (VAL.match(/^[a-z0-9]+$/i)) return true; else return false;",
                    message: "Only letters and numbers allowed!"
                });
				jQuery("#agreementNo").validate({
                    expression: "if (VAL.length < 40) return true; else return false;",
                    message: "Agreement number should 4 characters or less!"
                });
				jQuery("#fileNumber").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter a File Number!"
                });
                jQuery("#fileNumber").validate({
                    expression: "if (VAL.match(/^[0-9/]+$/i)) return true; else return false;",
                    message: "Only  Digits and / allowed!"
                });
				jQuery("#fileNumber").validate({
                    expression: "if (VAL.length < 40) return true; else return false;",
                    message: "File number should 40 characters or less!"
                });
				 jQuery("#customerContact").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter cusomter's contact no!"
                });
				jQuery("#customerContact").validate({
                    expression: "if (!isNaN(VAL) && VAL>0 && VAL.match(/^[0-9]+$/i)) return true; else return false;",
                    message: "Only digits allowed in contact no!"
                });
				 jQuery("#customerContact").validate({
                    expression: "if (VAL.length==10) return true; else return false;",
                    message: "contact no should be 10 digits long!"
                });
				
				jQuery("#amount").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter Loan amount!"
                });
				jQuery("#amount").validate({
                    expression: "if (!isNaN(VAL) && VAL>0) return true; else return false;",
                    message: "Only digits allowed in Loan amount!"
                });
				
				jQuery("#duration").validate({
                    expression: "if ((($('input[name=loan_scheme]:checked').val()==1) && VAL) || ($('input[name=loan_scheme]:checked').val()==2)) return true; else return false;",
                    message: "Please enter Loan duration (months)!"
                });
				jQuery("#duration").validate({
                    expression: "if ((($('input[name=loan_scheme]:checked').val()==1) && !isNaN(VAL) && VAL>0 && VAL<=120) || ($('input[name=loan_scheme]:checked').val()==2)) return true; else return false;",
                    message: "Only digits allowed in loan duration (less than 120 months)!"
                });
				
				jQuery("#roi").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter Rate of Interest (annually in %)"
                });
				jQuery("#roi").validate({
                    expression: "if (!isNaN(VAL) && VAL>=0) return true; else return false;",
                    message: "Only digits allowed in Rate of Interest!"
                });
				
				jQuery("#emi").validate({
                    expression: "if ((($('input[name=loan_scheme]:checked').val()==1) && VAL) || ($('input[name=loan_scheme]:checked').val()==2)) return true; else return false;",
                    message: "Please enter EMI!"
                });
				jQuery("#emi").validate({
                    expression: "if ((($('input[name=loan_scheme]:checked').val()==1) && !isNaN(VAL) && VAL>0) || ($('input[name=loan_scheme]:checked').val()==2)) return true; else return false;",
                    message: "Only digits allowed in EMI!"
                });
				
				jQuery("#agency_amount").validate({
                    expression: "if ((document.agency_type==2 && VAL) || document.agency_type==1) return true; else return false;",
                    message: "Please enter Loan amount!"
                });
				jQuery("#agency_amount").validate({
                    expression: "if ((document.agency_type==2 && !isNaN(VAL) && VAL>0) || document.agency_type==1) return true; else return false;",
                    message: "Only digits allowed in Loan amount!"
                });
				
				jQuery("#agency_duration").validate({
                    expression: "if ((document.agency_type==2 && VAL) || document.agency_type==1) return true; else return false;",
                    message: "Please enter Loan duration (months)!"
                });
				jQuery("#agency_duration").validate({
                    expression: "if ((document.agency_type==2 && !isNaN(VAL) && VAL>0) || document.agency_type==1) return true; else return false;",
                    message: "Only digits allowed in loan duration!"
                });
				
				jQuery("#agency_emi").validate({
                    expression: "if ((document.agency_type==2 && VAL) || document.agency_type==1) return true; else return false;",
                    message: "Please enter EMI!"
                });
				jQuery("#agency_emi").validate({
                    expression: "if ((document.agency_type==2 && !isNaN(VAL) && VAL>0) || document.agency_type==1) return true; else return false;",
                    message: "Only digits allowed in EMI!"
                });
				
					jQuery("#bank_name").validate({
                    expression: "if ((($('input[name=loan_amount_type]:checked').val()==2) && VAL) || ($('input[name=loan_amount_type]:checked').val()==1)) return true; else return false;",
                    message: "Please enter the bank name"
                });
jQuery("#branch_name").validate({
                    expression: "if ((($('input[name=loan_amount_type]:checked').val()==2) && VAL) || ($('input[name=loan_amount_type]:checked').val()==1)) return true; else return false;",
                    message: "Please enter the branch name"
                });	
	jQuery("#bank_name").validate({
                    expression: "if ((($('input[name=loan_amount_type]:checked').val()==2) && VAL.match(/^[a-zA-Z ]+$/)) || ($('input[name=loan_amount_type]:checked').val()==1)) return true; else return false;",
                    message: "Only letters allowed!"
                });								
	jQuery("#branch_name").validate({
                    expression: "if ((($('input[name=loan_amount_type]:checked').val()==2) && VAL.match(/^[a-zA-Z ]+$/)) || ($('input[name=loan_amount_type]:checked').val()==1)) return true; else return false;",
                    message: "Only letters allowed!"
                });		
				
				 jQuery("#cheque_amount").validate({
                    expression: "if (( ($('input[name=loan_amount_type]:checked').val()==2) && VAL) || ($('input[name=loan_amount_type]:checked').val()==1)) return true; else return false;",
                    message: "Please enter Cheque Amount"
                });
				jQuery("#cheque_amount").validate({
                    expression: "if (( ($('input[name=loan_amount_type]:checked').val()==2) && !isNaN(VAL)) || ($('input[name=loan_amount_type]:checked').val()==1)) return true; else return false;",
                    message: "Only digits allowed in Amount!"
                });
				
				 jQuery("#cheque_no").validate({
                    expression: "if (( ($('input[name=loan_amount_type]:checked').val()==2) && VAL) || ($('input[name=loan_amount_type]:checked').val()==1)) return true; else return false;",
                    message: "Please enter Cheque Number"
                });
				jQuery("#cheque_no").validate({
                    expression: "if (( ($('input[name=loan_amount_type]:checked').val()==2) && (!isNaN(VAL) && (VAL.length==6 || VAL.length==8))) || ($('input[name=loan_amount_type]:checked').val()==1)) return true; else return false;",
                    message: "Only 6 or 8 digits allowed in Cheque Number!"
                });
				
				
				 jQuery("#axin_no").validate({
                    expression: "if (!VAL || VAL.match(/^[a-z0-9]+$/i)) return true; else return false;",
                    message: "Please enter Valid Cheque Axin Number"
                });
				
				
				jQuery("#remarks_remainder").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter Remarks!"
                });
				
				jQuery("#amount_interest").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter amount/day or interest!"
                });
				jQuery("#amount_interest").validate({
                    expression: "if (!isNaN(VAL) && VAL>0) return true; else return false;",
                    message: "Only digits allowed in amount/Interest!"
                });

function checkContactNo(contact,contactInput)
{
	
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

function checkProofNo(proofno,el)
{
	
	var letters = /^[0-9a-zA-Z]+$/;
					
	if(proofno=="" || proofno==null)
		{
			$(el).next().hide();
			$(el).removeClass("ErrorField");
			var proofIdEl=$(el).parent().parent().prev().find(".customerProofId");
			
					$(proofIdEl).next().hide();
					$(proofIdEl).removeClass("ErrorField");
					
			
			}
		else if(proofno.match(letters))
		{
			$(el).next().hide();
			$(el).removeClass("ErrorField");
			var proofIdEl=$(el).parent().parent().prev().find(".customerProofId");
			var proofId=$(el).parent().parent().prev().find(".customerProofId").val();
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
	
function checkProofNoGuarantor(proofno,el)
{
	
	var letters = /^[0-9a-zA-Z]+$/;
					
	if(proofno=="" || proofno==null)
		{
			$(el).next().hide();
			$(el).removeClass("ErrorField");
			var proofIdEl=$(el).parent().parent().prev().find(".guarantorProofId");
			
					$(proofIdEl).next().hide();
					$(proofIdEl).removeClass("ErrorField");
					
			
			}
		else if(proofno.match(letters))
		{
			$(el).next().hide();
			$(el).removeClass("ErrorField");
			var proofIdEl=$(el).parent().parent().prev().find(".guarantorProofId");
			var proofId=$(el).parent().parent().prev().find(".guarantorProofId").val();
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
		else if(contact.length>6 && contact.length<13 && $.isNumeric(contact))
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
	
	$( ".datepick" ).each(function( index ) {
		
		
		var date=$(this).val();
		dateFormat=/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/g;
		var elName=$(this).attr('name');
		if(dateFormat.test(date))
		{
			$(this).next().hide();
			$(this).removeClass("ErrorField");
			res=res && true;
			
			}
		else if(elName=="cheque_date" && document.getElementById('loan_amount_type_cash').checked)
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
	
	$(".customerProofId").each(function(index, element) {
		if(index!=0)
		{
		var proofno=$(".customerProofNo").eq(index).val();
        var proofid=$(this).val();
		
		
		if(proofid!=-1)
		{
			
					var proofnoEl=$(".customerProofId").eq(index);
					proofnoEl.next().hide();
					proofnoEl.removeClass("ErrorField");
					res=res && true;
			if((proofno=="" || proofno==null))
			{
				if(proofno=="" || proofno==null)
				{
					var proofnoEl=$(".customerProofNo").eq(index);
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
					var proofnoEl=$(".customerProofNo").eq(index);
					proofnoEl.next().hide();
					proofnoEl.removeClass("ErrorField");
					res=res && true;
					}
					else
					{
						var proofnoEl=$(".customerProofNo").eq(index);
						proofnoEl.next().show();
						proofnoEl.addClass("ErrorField");
						res=res && false;
						}
			}
			else
			{
					var proofnoEl=$(".customerProofNo").eq(index);
					proofnoEl.next().hide();
					proofnoEl.removeClass("ErrorField");
					res=res && true;
				
			}
		}
		else if(proofid==-1 && !((proofno=="" || proofno==null)))
		{
						var proofnoEl=$(".customerProofId").eq(index);
						proofnoEl.next().show();
						proofnoEl.addClass("ErrorField");
						res=res && false;
						
					if(!(proofno=="" || proofno==null))
					{
					var letters = /^[0-9a-zA-Z]+$/;
						if(proofno.match(letters))
						{
						var proofnoEl=$(".customerProofNo").eq(index);
						proofnoEl.next().hide();
						proofnoEl.removeClass("ErrorField");
						res=res && true;
						}
						else
						{
							var proofnoEl=$(".customerProofNo").eq(index);
							proofnoEl.next().show();
							proofnoEl.addClass("ErrorField");
							res=res && false;
							}
					}
					
			
			}
		}
    });
	if(document.getElementById('uneven_loan').checked)
	{
		$('.duration').each(function(index, element) {
			
			if($(this).parent().parent().attr('id')!="EMIDurationTR")
			{
				
				var duratio=$(this).val();
				
				if(isNaN(duratio) || duratio=="" || duratio==null)
				{
					
					$(this).next().next().show();
					$(this).addClass("ErrorField");
					res=res && false;
					
				}
				else
				{
					$(this).removeClass("ErrorField");
					var emiArray=$('.emi');
					emi=emiArray[index];
					if(isNaN(emi) || emi=="" || emi==null)
					{
						
						}
					else
					{	
					$(this).next().next().hide();
					res=res && true;
					}
					}
				}
        });
		
		
		
			$('.emi').each(function(index, element) {
			
			if($(this).parent().parent().attr('id')!="EMIDurationTR")
			{
				var emi=$(this).val();
				
				if(isNaN(emi) || emi=="" || emi==null)
				{
					
					$(this).next().next().next().show();
					$(this).addClass("ErrorField");
					res=res && false;
					
				}
				else
				{
					$(this).removeClass("ErrorField");
					var durArray=$('.duration');
					dur=durArray[index];
					if(isNaN(dur) || dur=="" || dur==null)
					{
						
						}
					else
					{	
					$(this).next().next().next().hide();
					
					res=res && true;
					}
					}
				}
        });
	}
	if(document.agency_type!=null && document.agency_type==2)
	{
	$('.duration_agency').each(function(index, element) {
			
			if($(this).parent().parent().attr('id')!="EMIDurationAgencyTR")
			{
				
				var duratio=$(this).val();
				
				if(isNaN(duratio) || duratio=="" || duratio==null)
				{
					
					$(this).next().next().show();
					$(this).addClass("ErrorField");
					res=res && false;
					
				}
				else
				{
					$(this).removeClass("ErrorField");
					var emiArray=$('.emi_agency');
					emi=emiArray[index];
					if(isNaN(emi) || emi=="" || emi==null)
					{
						
						}
					else
					{	
					$(this).next().next().hide();
					res=res && true;
					}
					}
				}
        });
		
		
		
			$('.emi_agency').each(function(index, element) {
			
			if($(this).parent().parent().attr('id')!="EMIDurationAgencyTR")
			{
				var emi=$(this).val();
				
				if(isNaN(emi) || emi=="" || emi==null)
				{
					
					$(this).next().next().next().show();
					$(this).addClass("ErrorField");
					res=res && false;
					
				}
				else
				{
					$(this).removeClass("ErrorField");
					var durArray=$('.duration_agency');
					dur=durArray[index];
					if(isNaN(dur) || dur=="" || dur==null)
					{
						
						}
					else
					{	
					$(this).next().next().next().hide();
					res=res && true;
					}
					}
				}
        });
	}
	$(".guarantorProofId").each(function(index, element) {
		if(index!=0)
		{
		var proofno=$(".guarantorProofNo").eq(index).val();
        var proofid=$(this).val();
		
		
		if(proofid!=-1)
		{
					
					var proofnoEl=$(".guarantorProofId").eq(index);
					proofnoEl.next().hide();
					proofnoEl.removeClass("ErrorField");
					res=res && true;
					
			if((proofno=="" || proofno==null))
			{
				if(proofno=="" || proofno==null)
				{
					
					var proofnoEl=$(".guarantorProofNo").eq(index);
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
					var proofnoEl=$(".guarantorProofNo").eq(index);
					proofnoEl.next().hide();
					proofnoEl.removeClass("ErrorField");
					res=res && true;
					}
					else
					{
						
						var proofnoEl=$(".guarantorProofNo").eq(index);
						proofnoEl.next().show();
						proofnoEl.addClass("ErrorField");
						res=res && false;
						}
			}
			else
			{
				
					var proofnoEl=$(".guarantorProofNo").eq(index);
					proofnoEl.next().hide();
					proofnoEl.removeClass("ErrorField");
					res=res && true;
				
			}
		}
		else if(proofid==-1 && !((proofno=="" || proofno==null)))
		{
						
						var proofIdEl=$(".guarantorProofId").eq(index);
						proofIdEl.next().show();
						proofIdEl.addClass("ErrorField");
						res=res && false;
						
					if(!(proofno=="" || proofno==null))
					{
						var letters = /^[0-9a-zA-Z]+$/;
						if(proofno.match(letters))
						{
							var proofnoEl=$(".guarantorProofNo").eq(index);
							proofnoEl.next().hide();
							proofnoEl.removeClass("ErrorField");
							res=res && true;
						}
						else
						{
								var proofnoEl=$(".guarantorProofNo").eq(index);
								proofnoEl.next().show();
								proofnoEl.addClass("ErrorField");
								res=res && false;
						}
					}
					
			
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

function onFocusContactNo(no,el)
{
	
	}
	
function calculateEmi()
{
	var duration_unit=document.getElementById('duration_unit').value;
	changeDurationStr(duration_unit);
	if(document.getElementById('even_loan').checked)
	{
	var amount=document.getElementById('amount').value;
	var duration=document.getElementById('duration').value;
	var roi=document.getElementById('roi').value;
	var loan_type=document.getElementById('flat_loan');
	var emi=document.getElementById('emi');
	var roi_type=0;
	if(loan_type.checked)
	{
		roi_type=1;
		}
	else
	{
		roi_type=2;
		}	
	
	if(!isNaN(amount) && !isNaN(duration) && !isNaN(roi) && amount!=null && amount!="" && roi!=null && roi!="" && duration!=null && duration!="")
	{
		if(roi_type==1)
		{
		var interest=(amount*roi)/(1200);
		var division=amount/duration;
		var emi=interest+division;
		emi=Math.round(emi);
		}
		else
		{
			var intr   = roi / 1200;
			var part1=(amount * intr);
			var part2=(1 - (Math.pow(1/(1 + intr), duration)));
			var emi=Math.round(part1/part2);
			}
			document.getElementById('emi').value=emi;
	}
	}
	else
	{
		
		var loan_type=document.getElementById('flat_loan');
		var amount=document.getElementById('amount').value;
		loan_type.checked=true;
		var duration_array=$('.duration');
		var emi_array=$('.emi');
		var emi=0;
		var duration=0;
		var total_collection=0;
		$('.duration').each(function(index, element) {
			var du=0;
			var em=0;
			
            du=parseInt(element.value,10);
		    em=parseInt(emi_array[index].value,10);
			if(!isNaN(du) && !isNaN(em))
			{
		    duration=duration+du;
			emi=emi+em;
			total_collection=total_collection+(du*em);
			}
        });
		
	emi=total_collection/duration;	
		
	 if(!isNaN(amount) && !isNaN(duration) && !isNaN(emi) && amount!=null && amount!="" && emi!=null && emi!="" && duration!=null && duration!="")
	{
		var division=amount/duration;	
		
		var interest=emi-division;
		var roi=(interest*1200)/amount;	
		document.getElementById('roi').value=roi;
	}
		}
}	

function calculateROI()
{
	
	var amount=document.getElementById('amount').value;
	var duration=document.getElementById('duration').value;
	var duration_unit=document.getElementById('duration_unit').value;
	var loan_type=document.getElementById('flat_loan');
	var loan_scheme=document.getElementById('even_loan');
	var emi=document.getElementById('emi').value;
	var emi_per_year=duration/duration_unit;
	var roi_type=0;
	
	if(loan_scheme.checked)
	{
	if(loan_type.checked)
	{
		
		roi_type=1;
		}
	else
	{
		roi_type=2;
		}	
	
    if(!isNaN(amount) && !isNaN(duration) && !isNaN(emi) && amount!=null && amount!="" && emi!=null && emi!="" && duration!=null && duration!="")
	{
		var division=amount/duration;	
		var interest=emi-division; // interest per emi
		var roi=(interest*100*emi_per_year)/amount;	
		
		if(roi_type==2 && duration_unit==1)
		{	
				
		var tenure=duration;
		var interest=roi/100;
		var pay=amount*(1+(tenure/12)*interest);
		var strt=(pay-amount)/amount/tenure;
		var emi =pay/tenure;
		var r=1/(1+strt);
		var res= emi*((Math.pow(r,tenure)-1)/(r-1))*r-amount;

		if(res>0)
		{
			do 
			{
			strt=strt+0.00001;
			r=1/(1+strt);
			res=emi*((Math.pow(r,tenure)-1)/(r-1))*r-amount;
			
			} while( res > 0 )
		}
		else
		{
			alert('Please check the input numbers');
		}
		var eirr=((strt)*12)*10000;
		eirr=eirr;
		eirr/=100;
		roi=eirr;

			
	}
			roi=roi;
			document.getElementById('roi').value=roi;
		
		}	
	}
	else
	{
		
		loan_type.checked=true;
		var duration_array=$('.duration');
		var emi_array=$('.emi');
		var emi=0;
		var duration=0;
		var count=0;
		var total_collection=0;
		$('.duration').each(function(index, element) {
			var du=0;
			var em=0;
			
            du=parseInt(element.value,10);
		    em=parseInt(emi_array[index].value,10);
			if(!isNaN(du) && !isNaN(em))
			{
			count++;	
		    duration=duration+du;
			emi=emi+em;
			total_collection=total_collection+(du*em);
			}
        });
	
	emi=total_collection/duration;	
		
		
	 if(!isNaN(amount) && !isNaN(duration) && !isNaN(emi) && amount!=null && amount!="" && emi!=null && emi!="" && duration!=null && duration!="")
	{
		
		var division=amount/duration;	
		var interest=emi-division;
		var emi_per_year=duration/duration_unit;
		var roi=(interest*100*emi_per_year)/amount;	
		document.getElementById('roi').value=roi;
	}
	}
	
}

function generateChequeDetails()
{
	
	if(document.getElementById('loan_amount_type_cheque').checked)
	{
		document.getElementById('loanChequeDetails').style.display="block";
		}
	else
	{
		document.getElementById('loanChequeDetails').style.display="none";
		}	
}
if(document.getElementById('loan_amount_type_cheque').checked)
	{
		document.getElementById('loanChequeDetails').style.display="block";
		}
	else
	{
		document.getElementById('loanChequeDetails').style.display="none";
		}	

function changeLoanStr()
{
	
	if(document.getElementById('uneven_loan').checked)
	{
		document.getElementById('uneven_loan_table').style.display="block";
		document.getElementById('even_loan_table').style.display="none";
		document.getElementById('flat_loan').checked=true;
		calculateROI();
		}
	else
	{
		document.getElementById('even_loan_table').style.display="block";
		document.getElementById('uneven_loan_table').style.display="none";
		}
}	

function changeDurationStr(unit)
{

	if(document.getElementById('reducing_loan').checked && unit!=1)
	{
		
		document.getElementById('flat_loan').checked=true;
		calculateROI();
		}
	else
	calculateROI();	
	
}	
	
if(document.getElementById('uneven_loan').checked)
	{
		document.getElementById('uneven_loan_table').style.display="block";
		document.getElementById('even_loan_table').style.display="none";
		document.getElementById('flat_loan').checked=true;
		}
	else
	{
		document.getElementById('even_loan_table').style.display="block";
		document.getElementById('uneven_loan_table').style.display="none";
		}	