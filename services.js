$(function(){
    const currentURL = window.location.href;
    const catValue = (new URL(currentURL)).searchParams.get("cat");
    $('.display_services').load('ajaxService.php?cat='+catValue);
    $('#count_cart').load('ajaxcountcart.php');
    $('#txtSearch').keyup(function(){
        let txtsearch = $(this).val();
        let search =  txtsearch.replace(/ /g, '_');
        $('.display_services').load('ajaxService.php?cat='+catValue+'&search='+search);
    })
    setTimeout(function(){
        var countValue = $("#count_cart").text();
        if (countValue === '0') {
            $("#count_cart").hide();
        } else {
            $("#count_cart").show();
        }
    },200);
});