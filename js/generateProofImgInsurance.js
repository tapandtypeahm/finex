// JavaScript Document


function deleteProofImgInsuranceTr(elem){
	var parent1=elem.parentNode.parentNode;
	parent1.innerHTML="";
	}

function addProofImgInsurance(elem,proofno) {
   
   var jelem=$(elem);
	var tableRow=elem.parentNode.parentNode;
	var tableBody=elem.parentNode.parentNode.parentNode;
	var insertTable=document.getElementById('insertInsuranceTable');
		 
	var newIndex=jelem.parent().parent().index();

	newIndex=newIndex+1;
	
	var newRow=tableBody.insertRow(newIndex);
	
	

	
	var cell1=newRow.insertCell(0);
    var cell2=newRow.insertCell(1);
	
	cell1.innerHTML="Proof Image : ";
	
	var imgBtn=document.createElement("input");
	imgBtn.setAttribute("type","file");
	imgBtn.setAttribute("name","insuranceProofImg["+proofno+"][]");
	imgBtn.setAttribute("accept","image/jpeg,image/png,image/gif,application/pdf");
	imgBtn.setAttribute("title","insert Proof Image");
	cell2.appendChild(imgBtn);
	
	var spanOr=document.createElement("span");
	spanOr.innerHTML="<br>- OR -<br>";
	cell2.appendChild(spanOr);
	
	var scanBtn=document.createElement("input");
	scanBtn.setAttribute("type","button");
	scanBtn.setAttribute("class","btn scanBtn insuranceScanBtn");
	scanBtn.setAttribute("onclick","scanInsuranceProof(this,'"+proofno+"')");
	scanBtn.setAttribute("name","scanProof");
	scanBtn.setAttribute("value","scan");
	cell2.appendChild(scanBtn);
	
	var removeScanBtn=document.createElement("input");
	removeScanBtn.setAttribute("type","button");
	removeScanBtn.setAttribute("class","btn btn-danger removescanbtn");
	removeScanBtn.setAttribute("value","-");
	removeScanBtn.setAttribute("onclick","deleteProofImgInsuranceTr(this)");
	cell2.appendChild(removeScanBtn);
	
}



