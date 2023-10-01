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
        location.href="ManageService.php";
    })
    $('#btninvoices').click(function(){
        location.href="manageinvoice.php";
    })
    $('#btnOrder').click(function(){
        location.href="neworder.php";
    })
    $('#btnNews').click(function(){
        location.href="";
    })
    $('#btnTickets').click(function(){
        location.href="ManageTickets.php";
    })
    $('#btnContactus').click(function(){
        location.href="contactus.php";
    })

})