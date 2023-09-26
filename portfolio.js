$(function(){
    var countValue = $("#count_cart").text();
    $('#count_cart').load('ajaxcountcart.php');

    if (countValue === '') {
        $("#count_cart").hide();
    } else {
        $("#count_cart").show();
    }
});