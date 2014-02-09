// JavaScript Document

  jQuery("#txtlocation").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the location name"
                });
	jQuery("#txtlocation").validate({
                    expression: "if (VAL.match(/^[a-z0-9(),.:;-_& ]+$/i)) return true; else return false;",
                    message: "Only letters allowed!"
                });			
				
		
      