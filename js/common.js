// JavaScript Document
$('.selectpicker').selectpicker({container: 'body'});
jQuery(document).bind("keyup keydown", function(e){
    if(e.ctrlKey && e.keyCode == 80){
		printCheckBox();
		selectTR(null);
        var htmlToPrint = $("#adminContentTable").html(); 
		if(!htmlToPrint)
		 var htmlToPrint = $("#adminContentReport").html();
 		document.getElementById('to_print').innerHTML=htmlToPrint;
		window.print() ;
    }
});


$('.printBtn').click(function(e) {
    printCheckBox();
	selectTR(null);
	
	 var htmlToPrint = $("#adminContentTable").html(); 
	 if(!htmlToPrint)
	 var htmlToPrint = $("#adminContentReport").html();
 	 document.getElementById('to_print').innerHTML=htmlToPrint;
	
		window.print() ;
});

function disableSubmitButton()
{
	
	//$('#disableSubmit').attr('disabled','disabled');
	//$('#disableSubmit').attr('value','Processing...');
	}

function selectTR(el)
{
	
	var checked = countChecked();
	
		
	$('.selectTR').each(function(index, element) {
        
		
		if(checked>0)
		{
		
		if(!$(element).parent().parent().hasClass('no_print'))
		{
			$(element).parent().parent().addClass('no_print');
			}
		
		$( "input:checked.selectTR" ).parent().parent().removeClass('no_print');
		}
		else
		{
			$( ".selectTR" ).parent().parent().removeClass('no_print');
			
			}	
    });
	
}

var countChecked = function() {
  var n = $( "input:checked.selectTR" ).length;
  return n;
};

$('.showCB').change(function(e) {
	var elID=$(this).attr('id');
	var headings=$('th.heading');
	
	
	var heading=headings[elID];
   
    if($(this).is(':checked'))
	{
		
		 $('#adminContentTable tr.resultRow').each(function(index, element) {
            tds=$(element).children('td');
			td=tds[elID];
		$(heading).removeClass('no_print');		
		$(td).removeClass('no_print');	
        });
		
		 $('#adminContentReport tr.resultRow').each(function(index, element) {
            tds=$(element).children('td');
			td=tds[elID];
		$(heading).removeClass('no_print');		
		$(td).removeClass('no_print');	
        });
		 
		}
	else
	{
		 
		 if(!$(heading).hasClass('no_print'))
				{
				$(heading).addClass('no_print');
				}
		 $('#adminContentTable tr.resultRow').each(function(index, element) {
            tds=$(element).children('td');
			td=tds[elID];
			if(!$(td).hasClass('no_print'))
				{
				$(td).addClass('no_print');
				}
        });
		 $('#adminContentReport tr.resultRow').each(function(index, element) {
            tds=$(element).children('td');
			td=tds[elID];
			if(!$(td).hasClass('no_print'))
				{
				$(td).addClass('no_print');
				}
        });
		}	
	
	
	 
});


 function printCheckBox() 
 {  
	  $('.showCB').each(function(index, element) {
        
    
       		var elID=$(element).attr('id');
	var headings=$('th.heading');
	
	
	var heading=headings[elID];
   
    if($(element).is(':checked'))
	{
		
		 $('#adminContentTable tr.resultRow').each(function(index, element) {
            tds=$(element).children('td');
			td=tds[elID];
		$(heading).removeClass('no_print');		
		$(td).removeClass('no_print');	
        });
		 $('#adminContentReport tr.resultRow').each(function(index, element) {
            tds=$(element).children('td');
			td=tds[elID];
		$(heading).removeClass('no_print');		
		$(td).removeClass('no_print');	
        });
		 
		}
	else
	{
		 
		 if(!$(heading).hasClass('no_print'))
				{
				$(heading).addClass('no_print');
				}
		 $('#adminContentTable tr.resultRow').each(function(index, element) {
            tds=$(element).children('td');
			td=tds[elID];
			if(!$(td).hasClass('no_print'))
				{
				$(td).addClass('no_print');
				}
        });
		 $('#adminContentReport tr.resultRow').each(function(index, element) {
            tds=$(element).children('td');
			td=tds[elID];
			if(!$(td).hasClass('no_print'))
				{
				$(td).addClass('no_print');
				}
        });
		}
	}); 	
   
 }
 
