// JavaScript Document
document.totalInsuranceProof=1;

$('#addInsuranceProofBtn').click(function(e) {
	
	var proofno=document.totalInsuranceProof;
	
	var insertTable=document.getElementById('insertInsuranceTable');
		 
	var newIndex=$(this).parent().parent().index();
	
	var mytbody=document.createElement('tbody');
	insertTable.appendChild(mytbody);
	var proofImgRow=document.createElement('tr');
	var removeProof=document.createElement('tr');
	var hr2=document.createElement('tr');
	
	var cell5=proofImgRow.insertCell(0);
    var cell6=proofImgRow.insertCell(1);
	
	
	var cell9=hr2.insertCell(0);
    var cell0=hr2.insertCell(1);
	
	var cella=removeProof.insertCell(0);
    var cellb=removeProof.insertCell(1);
	
	
	cell5.innerHTML="Proof Image : ";
	

	
	cell9.innerHTML="<hr>";
	cell0.innerHTML="<hr>";
	
	cellb.innerHTML="<a class='removeProof' onclick='removeInsuranceProof(this)'>Remove this Proof</a>";
	
	
	cell6.innerHTML='<input class="customerFile fleInput" onchange="onChangeFile(this.value,this)" accept="image/jpeg,image/png,image/gif,application/pdf" type="file" name="insuranceImg[' + proofno + '][]"  /><br /> - OR - <br /><input type="button" name="scanProof" class="btn scanBtn insuranceScanBtn" value="scan" onclick="scanInsuranceProof(this,'+proofno+')" /><span class="ValidationErrors fileError">Not Supported File Type!</span>';
	document.totalInsuranceProof=++proofno;
	
	mytbody.appendChild(hr2);
	mytbody.appendChild(removeProof);
	mytbody.appendChild(proofImgRow);
	
	
});

function removeInsuranceProof(elem)
{
	elem=$(elem);
	removeTbody=elem.parent().parent().parent();
	removeTbody[0].innerHTML="";
	}