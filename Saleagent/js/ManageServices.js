$(function(){

    $('.bodyticket').load('ajaxsale/displayServiceClient.php')

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyticket').load('ajaxsale/displayServiceClient.php?search='+search);
    })


})