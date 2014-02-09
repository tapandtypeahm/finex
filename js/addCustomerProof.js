document.totalCustomerProof=1;

$('#addCustomerProofBtn').click(function(e) {
	
	var proofno=document.totalCustomerProof;
	
	var insertTable=document.getElementById('insertCustomerTable');
		 
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
	
	cell5.innerHTML="Proof Image :  <br />(.jpg | .jpeg | .png | .gif | .pdf)";
	

	
	cell9.innerHTML="<hr>";
	cell0.innerHTML="<hr>";
	
	cellb.innerHTML="<a class='removeProof' onclick='removeCustomerProof(this)'>Remove this Proof</a>";
	
	
	var customerProofTypeHtml=document.getElementById('customerProofTypeTd').innerHTML;
	var customerNoTypeHtml=document.getElementById('customerProofNoTd').innerHTML;
	
	cell2.innerHTML=customerProofTypeHtml;
	cell4.innerHTML=customerNoTypeHtml;
	 
	cell6.innerHTML='<input class="customerFile fleInput" onchange="onChangeFile(this.value,this)" accept="image/jpeg,image/png,image/gif,application/pdf" type="file" name="customerProofImg[' + proofno + '][]"  /><br /> - OR - <br /><input type="button" name="scanProof" class="btn scanBtn customerScanBtn" value="scan" onclick="scanCustomerProof(this,'+proofno+')" /><input type="button" value="+" title="Add More image to this proof" class="btn btn-primary addscanbtnCustomer" onclick="addProofImgCustomer(this,'+proofno+')"/><span class="ValidationErrors fileError">Not Supported File Type!</span>';
	document.totalCustomerProof=++proofno;
	
	mytbody.appendChild(hr2);
	mytbody.appendChild(removeProof);
	mytbody.appendChild(proofTypeRow);
	mytbody.appendChild(proofNoRow);
	mytbody.appendChild(proofImgRow);
	
	
});

function removeCustomerProof(elem)
{
	
	elem=$(elem);
	removeTbody=elem.parent().parent().parent();
	removeTbody[0].innerHTML="";
	}