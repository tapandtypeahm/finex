// JavaScript Document

if($('.addEMIDurationBtn'))
{
$('.addEMIDurationBtn').click(function(e) {
    var newRowData=document.getElementById('EMIDurationTR').innerHTML;
	
	var insertTable=document.getElementById('uneven_loan_table');
	
	
	var newIndex=$('#EMIDurationTR').index();
	newIndex=newIndex+1;
	var newRow=insertTable.insertRow(-1);
	newRow.innerHTML=newRowData;
	
});
}

if($('.addEMIDurationBtnAgency'))
{
$('.addEMIDurationBtnAgency').click(function(e) {
    var newRowData=document.getElementById('EMIDurationAgencyTR').innerHTML;
	
	var insertTable=document.getElementById('uneven_loan_table_agency');
	
	
	var newIndex=$('#EMIDurationAgencyTR').index();
	newIndex=newIndex+1;
	var newRow=insertTable.insertRow(-1);
	
	newRow.innerHTML=newRowData;
	
});
}

function deleteEMIDurationTr(elem){
	
	var parent1=elem.parentNode.parentNode.parentNode;
	parent1.innerHTML="";
	calculateROI();
	}
	
function deleteEMIDurationAgencyTr(elem){
	
	var parent1=elem.parentNode.parentNode.parentNode;
	parent1.innerHTML="";
	}