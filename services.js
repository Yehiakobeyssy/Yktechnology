$(function(){
    const currentURL = window.location.href;
    const catValue = (new URL(currentURL)).searchParams.get("cat");
    $('.display_services').load('ajaxService.php?cat='+catValue);
    $('#count_cart').load('ajaxcountcart.php');
    $('#txtSearch').keyup(function(){
        let txtsearch = $(this).val();
        $('.display_services').load('ajaxService.php?cat='+catValue+'&search='+txtsearch);
    })
    var countValue = $("#count_cart").text();
    if (countValue === '') {
        $("#count_cart").hide();
    } else {
        $("#count_cart").show();
    }
});