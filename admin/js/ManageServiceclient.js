$(function(){

    $('.bodyticket').load('ajaxadmin/displayServiceClient.php')

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyticket').load('ajaxadmin/displayServiceClient.php?search='+search);
    })


})