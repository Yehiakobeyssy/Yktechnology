$(function(){

    $('.btnviewproject').click(function(){
        let projectID = $(this).data('index');
        location.href="manageProjects.php?do=view&pid="+projectID
    })

    $('.btnviewtaskdaitail').click(function(){
        let taskID = $(this).data('index');
        location.href="manageTask.php?do=view&task="+taskID;
    })

})