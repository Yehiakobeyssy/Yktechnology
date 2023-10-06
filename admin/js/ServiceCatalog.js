$(function(){

    $('.result_cards').load('ajaxadmin/displayCatalog.php')

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.result_cards').load('ajaxadmin/displayCatalog.php?search='+search);
    })
})