$(function(){

    let currentURL = window.location.href
    var urlParams = new URLSearchParams(currentURL.split('?')[1]);
    var catValue = urlParams.get('cat');

    $('.manage_services').load('ajaxadmin/displayItems.php?catid='+catValue);

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.manage_services').load('ajaxadmin/displayItems.php?catid='+catValue+'&search='+search);
    })
}) 