// JavaScript Document
if($('.addContactbtnCustomer'))
{
$('.addContactbtnCustomer').click(function(e) {
	
    var newRowData=document.getElementById('addcontactTrGeneratedCustomer').innerHTML;
	var insertTable=document.getElementById('insertCustomerTable');
	var newIndex=$('#addcontactTrCustomer').index();
	newIndex=newIndex+2;
	var newRow=insertTable.insertRow(newIndex);
	newRow.innerHTML=newRowData;
});
}

function deleteContactTr(elem){
	
	var parent1=elem.parentNode.parentNode.parentNode;
	parent1.innerHTML="";
	}
