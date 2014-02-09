// JavaScript Document

$('#periodModal').on('show.bs.modal', function () {
  document.getElementById('periodModal').style.zIndex=1050;
});
$('#periodModal').on('hidden.bs.modal', function () {
  document.getElementById('periodModal').style.zIndex=-1050;
});

$('#currentDateModal').on('show.bs.modal', function () {
  document.getElementById('currentDateModal').style.zIndex=1050;
});
$('#currentDateModal').on('hidden.bs.modal', function () {
  document.getElementById('currentDateModal').style.zIndex=-1050;
});

jQuery(document).bind("keyup keydown", function(e){
    if(e.ctrlKey && e.keyCode == 113){
		$('#periodModal').modal('show');
	}});
	
jQuery(document).bind("keyup keydown", function(e){
    if(e.altKey && e.keyCode == 113){
		$('#currentDateModal').modal('show');
	}});	

