// JavaScript Document

 jQuery("#txtName").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the Insurance Company name"
                });
				 jQuery("#txtName").validate({
                     expression: "if (VAL.match(/^[a-zA-Z0-9()&.:;,*@# ]+$/)) return true; else return false;",
                    message: "Only letters and Dot(.) allowed!"
                });
				
				