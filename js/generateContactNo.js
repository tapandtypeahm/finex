// JavaScript Document

$('.addContactbtn').click(function(e) {
    var newRowData=document.getElementById('addcontactTrGenerated').innerHTML;
	var insertTable=document.getElementById('insertTable');
	var newIndex=$('#addcontactTr').index();
	newIndex=newIndex+2;
	var newRow=insertTable.insertRow(newIndex);
	newRow.innerHTML=newRowData;
});


function deleteContactTr(elem){
	
	var parent1=elem.parentNode.parentNode.parentNode;
	parent1.innerHTML="";
	}
