$(function(){

    $('.btnshowdis').click(function(){
        let id= $(this).attr('data-index');
        $("html, body").animate({ scrollTop: 0 }, "slow");
        $('#showdiscription').load('ajaxadmin/showdiscriptionportfolio.php?port='+id);
        $('.popupdiscription').show();
    })

    $('.closepopup').click(function(){
        $('.popupdiscription').hide();
    })

    $('#addnew').click(function(){
        location.href="Manageportfolio.php?do=add";
    })
})