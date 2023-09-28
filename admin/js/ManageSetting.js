$(function(){

    $('.card_slide').dblclick(function(){
        let slideId= $(this).attr('data-index')
        location.href= 'ManageSetting.php?doslide=delete&slideid='+slideId;
    })
})