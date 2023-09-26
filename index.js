$(function(){

    $('.card_service').click(function(){
        let catID = $(this).attr('data-index');
        location.href="services.php?cat="+catID;
    })
    
    $('.port_card').click(function(){
        let portID = $(this).attr('data-index');
        location.href= "portfolio.php?port="+portID;
    })
})