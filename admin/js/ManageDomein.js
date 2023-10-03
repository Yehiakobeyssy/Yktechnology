$(function(){
    $('.bodyticket').load('ajaxadmin/displayDomein.php');
    $('#relatedTo').load('ajaxadmin/displayReletedServices.php');

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyticket').load('ajaxadmin/displayDomein.php?search='+search);
    })

    $('#clientName').change(function(){
        let cID = $(this).val();
        $('#relatedTo').load('ajaxadmin/displayReletedServices.php?clid='+cID);
    })
})