// JavaScript Document

jQuery("#txtbank").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the bank name"
                });
jQuery("#txtbranch").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the branch name"
                });	
	jQuery("#txtbank").validate({
                    expression: "if (VAL.match(/^[a-zA-Z0-9,.:;()*&%#$@ ]+$/)) return true; else return false;",
                    message: "Only letters allowed!"
                });								
	jQuery("#txtbranch").validate({
                    expression: "if (VAL.match(/^[a-zA-Z0-9,.:;()*&%#$@ ]+$/)) return true; else return false;",
                    message: "Only letters allowed!"
                });		