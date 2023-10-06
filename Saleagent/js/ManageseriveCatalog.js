$(function(){

    $('.result_cards').load('ajaxsale/displayCatalogService.php')

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.result_cards').load('ajaxsale/displayCatalogService.php?search='+search);
    })
})