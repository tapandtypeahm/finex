// JavaScript Document

	jQuery("#remarks_remainder").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter Remarks!"
                });