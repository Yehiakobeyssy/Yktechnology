$(function(){

    $('.card_slide').dblclick(function(){
        let slideId= $(this).attr('data-index')
        location.href= 'ManageSetting.php?doslide=delete&slideid='+slideId;
    })

    $('.card1').click(function(){
        location.href="ManageCountry.php";
    })
    $('.card2').click(function(){
        location.href="managePaymentMethod.php";
    })
    $('.card3').click(function(){
        location.href="manageDomeinTypes.php";
    })
    $('.card4').click(function(){
        location.href="manageTickettype.php";
    })

})