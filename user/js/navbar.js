$(function(){

    $('#count_cart').load('../ajaxcountcart.php');


    setTimeout(function(){
        var countValue = $("#count_cart").text();
        if (countValue === '0') {
            $("#count_cart").hide();
        } else {
            $("#count_cart").show();
        }
    },200);
    
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