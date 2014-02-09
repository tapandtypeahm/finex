$('.delBtn').click(function(e) {
	$('#myModal').modal('show');
	var delLink=$(this).parent().prop('href');
	delLink=delLink+"&access=approved";
	document.getElementById('delLink').value=delLink;
	return false;
});


$('.splEditBtn').click(function(e) {
	
	$('#myModal').modal('show');
	var delLink=$(this).parent().prop('href');
	delLink=delLink+"&access=approved";
	document.getElementById('delLink').value=delLink;
	return false;
});
$('#myModal').on('shown.bs.modal', function () {
  document.getElementById('confirmationPassword').focus();
});
$('#myModal').on('show.bs.modal', function () {
  document.getElementById('myModal').style.zIndex=1050;
});
$('#myModal').on('hidden.bs.modal', function () {
  document.getElementById('myModal').style.zIndex=-1050;
});

function confirmDelete(password,delLink)
{
	
if (password=="")
  {
  return false;
  } 
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
	 
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
	
    var res=eval(xmlhttp.responseText);
	
	if(res=="success")
	window.location.href=delLink;
	else if(res=="error")
	alert("Access Denied! Incorrect Password");
    }
  } 

xmlhttp.open("GET","/finex/lib/checkForDeletion.php?p="+password,true);
xmlhttp.send();
}