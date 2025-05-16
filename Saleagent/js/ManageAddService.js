$(function(){

    $('#btnaddnewServices').click(function(){
        location.href="ManageAddService.php?do=Service";
    });

    $('#btnaddnewDomains').click(function(){
        location.href="ManageAddService.php?do=Domain";
    });
});