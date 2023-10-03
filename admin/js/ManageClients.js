$(function(){

    $('.bodyticket').load('ajaxadmin/dispalayclients.php')

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyticket').load('ajaxadmin/dispalayclients.php?search='+search);
    })


})