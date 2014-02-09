// JavaScript Document

     $('.editBranchName').editable('editBranch.php', {
         indicator : 'Saving...',
         tooltip   : 'Click to edit...',
		 id   : 'lid',
         name : 'branch'
     });

$('.editBranchBtn').click(function() {
	
    $(this).parent().prev().children('.editBranchName').click();
});