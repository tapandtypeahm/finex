// JavaScript Document
document.totalGuarantorProof=1;

$('#addGuarantorProofBtn').click(function(e) {
	
	var proofno=document.totalGuarantorProof;
	
	var insertTable=document.getElementById('insertGuarantorTable');
		 
	var newIndex=$(this).parent().parent().index();
	
	var mytbody=document.createElement('tbody');
	insertTable.appendChild(mytbody);
	var proofImgRow=document.createElement('tr');
	var proofNoRow=document.createElement('tr');
	var proofTypeRow=document.createElement('tr');
	var removeProof=document.createElement('tr');
	var hr2=document.createElement('tr');
	
	
	
	var cell1=proofTypeRow.insertCell(0);
    var cell2=proofTypeRow.insertCell(1);
	
	var cell3=proofNoRow.insertCell(0);
    var cell4=proofNoRow.insertCell(1);
	
	var cell5=proofImgRow.insertCell(0);
    var cell6=proofImgRow.insertCell(1);
	
	
	var cell9=hr2.insertCell(0);
    var cell0=hr2.insertCell(1);
	
	var cella=removeProof.insertCell(0);
    var cellb=removeProof.insertCell(1);
	
	cell1.innerHTML="Proof Type : ";
	
	cell3.innerHTML="Proof Number : ";
	
	cell5.innerHTML="Proof Image : <br />(.jpg,.jpeg,.png,.gif,.pdf)";
	

	
	cell9.innerHTML="<hr>";
	cell0.innerHTML="<hr>";
	
	cellb.innerHTML="<a class='removeProof' onclick='removeGuarantorProof(this)'>Remove this Proof</a>";
	
	
	var guarantorProofTypeHtml=document.getElementById('guarantorProofTypeTd').innerHTML;
	var guarantorNoTypeHtml=document.getElementById('guarantorProofNoTd').innerHTML;
	
	cell2.innerHTML=guarantorProofTypeHtml;
	cell4.innerHTML=guarantorNoTypeHtml;
	
	cell6.innerHTML='<input class="guarantorFile fleInput" onchange="onChangeFile(this.value,this)" accept="image/jpeg,image/png,image/gif,application/pdf" type="file" name="guarantorProofImg[' + proofno + '][]"  /><br /> - OR - <br /><input type="button" name="scanProof" class="btn scanBtn guarantorScanBtn" value="scan" onclick="scanGuarantorProof(this,'+proofno+')" /><input type="button" value="+" title="Add More image to this proof" class="btn btn-primary addscanbtnGuarantor" onclick="addProofImgGuarantor(this,'+proofno+')"/><span class="ValidationErrors fileError">Not Supported File Type!</span>';
	document.totalCustomerProof=++proofno;
	
	mytbody.appendChild(hr2);
	mytbody.appendChild(removeProof);
	mytbody.appendChild(proofTypeRow);
	mytbody.appendChild(proofNoRow);
	mytbody.appendChild(proofImgRow);
	
	
});

function removeGuarantorProof(elem)
{
	elem=$(elem);
	removeTbody=elem.parent().parent().parent();
	removeTbody[0].innerHTML="";
	}