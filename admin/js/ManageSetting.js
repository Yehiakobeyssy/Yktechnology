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
    $('.card5').click(function(){
        location.href="managehowwework.php";
    })
    $('.card6').click(function(){
        location.href="manageMyCv.php";
    })
    $('.card7').click(function(){
        location.href="managefooder.php";
    })
    $('.card8').click(function(){
        location.href="mangepaypal.php";
    })


})