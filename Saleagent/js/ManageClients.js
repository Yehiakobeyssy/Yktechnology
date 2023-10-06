$(function(){

    $('.bodyticket').load('ajaxsale/dispalayclients.php')

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyticket').load('ajaxsale/dispalayclients.php?search='+search);
    })


})