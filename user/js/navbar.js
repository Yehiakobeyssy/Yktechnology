$(function(){

    $('#count_cart').load('../ajaxcountcart.php');

    var countValue = $("#count_cart").text();
    if (countValue === '') {
        $("#count_cart").hide();
    } else {
        $("#count_cart").show();
    }
    
    $('#btnDashboard').click(function(){
        location.href="dashboard.php";
    })
    $('#btnmyservices').click(function(){
        location.href="";
    })
    $('#btninvoices').click(function(){
        location.href="";
    })
    $('#btnOrder').click(function(){
        location.href="";
    })
    $('#btnNews').click(function(){
        location.href="";
    })
    $('#btnTickets').click(function(){
        location.href="";
    })
    $('#btnContactus').click(function(){
        location.href="";
    })

})