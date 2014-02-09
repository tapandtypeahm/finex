// JavaScript Document

$('.addContactbtnGuarantor').click(function(e) {
    var newRowData=document.getElementById('addcontactTrGeneratedGuarantor').innerHTML;
	var insertTable=document.getElementById('insertGuarantorTable');
	var newIndex=$('#addcontactTrGuarantor').index();
	newIndex=newIndex+1;
	var newRow=insertTable.insertRow(newIndex);
	newRow.innerHTML=newRowData;
});


function deleteContactTr(elem){
	
	var parent1=elem.parentNode.parentNode.parentNode;
	parent1.innerHTML="";
	}
