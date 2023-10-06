$(function(){
    $('.bodyticket').load('ajaxsale/displayDomein.php');
    $('#relatedTo').load('ajaxsale/displayReletedServices.php');

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyticket').load('ajaxsale/displayDomein.php?search='+search);
    })

    $('#clientName').change(function(){
        let cID = $(this).val();
        $('#relatedTo').load('ajaxsale/displayReletedServices.php?clid='+cID);
    })
})