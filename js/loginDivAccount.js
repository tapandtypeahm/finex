// JavaScript Document


$('#userNameNav').click(function(e){
	var settings1 = document.getElementById('userNameNav');					
var settings = document.getElementById('userDetailDropDown');
var currentDisplay=settings.style.display;

			e.preventDefault();
			$('#usernameInput').focus();
			
   if(currentDisplay=='block')
   {
        			$('#userNameNav').addClass('selected');
   }
    else
    {
    	$('#userNameNav').removeClass('selected');
    }    	
	});
					
// to hide the div when clicked outside it
document.onclick = function(e){
	
	var settings1 = document.getElementById('userNameNav');					
var settings = document.getElementById('userDetailDropDown');
var currentDisplay=settings.style.display;

    var target = (e && e.target) || (event && event.srcElement);
    var display = 'none';
    
    
    while (target.parentNode) {
        if (target == settings || target == settings1 ) {
        	
        	if(target == settings1)
        	{
        		if(currentDisplay=='block')
        		{
        			
        			display = 'none';
        			break;
        		}
        		else
        		{
        			display ='block';
            		break;
        		}
        	}
        	else
        	{
            display ='block';
            break;
            }
        }
        target = target.parentNode;
    }
    	
    settings.style.display = display;
    
    if(display=='block')
    {
        			$('#userNameNav').addClass('selected');
    }
    else
    {
    	$('#userNameNav').removeClass('selected');
    }    	
}