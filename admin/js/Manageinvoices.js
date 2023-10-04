$(function(){
    $('.bodyticket').load('ajaxadmin/displayinvoice.php');

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyticket').load('ajaxadmin/displayinvoice.php?search='+search);
    })

    
})
