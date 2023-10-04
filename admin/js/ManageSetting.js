$(function(){

    $('.card_slide').dblclick(function(){
        let slideId= $(this).attr('data-index')
        location.href= 'ManageSetting.php?doslide=delete&slideid='+slideId;
    })

    $('.card1').click(function(){
        location.href="ManageCountry.php";
    })
    $('.card2').click(function(){
        location.href="";
    })
    $('.card3').click(function(){
        location.href="";
    })
    $('.card4').click(function(){
        location.href="";
    })
    $('.card5').click(function(){
        location.href="";
    })
    $('.card6').click(function(){
        location.href="";
    })
    $('.card7').click(function(){
        location.href="";
    })
    $('.card8').click(function(){
        location.href="";
    })
})