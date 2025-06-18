$(function(){

    $('.btnviewproject').click(function(){
        let projectID = $(this).data('index');
        location.href="manageProjects.php?do=view&pid="+projectID
    })

})