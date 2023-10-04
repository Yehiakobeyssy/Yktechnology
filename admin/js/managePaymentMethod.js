$(function(){
    $('.cards_payments').load('ajaxadmin/displaypaymentmethods.php');
    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.cards_payments').load('ajaxadmin/displaypaymentmethods.php?search='+search);
    })

})