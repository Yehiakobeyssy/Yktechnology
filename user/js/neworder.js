$(function(){
    $('.card_service').click(function(){
        let catID = $(this).attr('data-index');
        location.href="services.php?cat="+catID;
    })
})