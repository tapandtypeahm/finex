// JavaScript Document

jQuery("#name").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the Vehicle Dealer name"
                });
				 jQuery("#name").validate({
                     expression: "if (VAL.match(/^[a-zA-Z0-9().:;,*@#& ]+$/)) return true; else return false;",
                    message: "Only letters and Dot(.) allowed!"
                });		
 jQuery("#contactNo").validate({
                     expression: "if (!VAL || !isNaN(VAL)) return true; else return false;",
                    message: "Only Digits allowed in Contact number!"
                });												