$(function(){
    var firstText = $('.amountpayment:first').text();
    var firstNumber = parseFloat(firstText.replace('$', ''));
    var url = window.location.href;
    var urlParams = new URLSearchParams(url.split('?')[1]); // Extract the query parameters from the URL
    var invid = urlParams.get('id');


    $('#textnote').load('ajaxuser/display_note_payment.php');
    $('.conclution').load('ajaxuser/buttonpayment.php?invID='+invid+'&amount='+firstNumber)
    $('#Nextamount').text(firstNumber);
    $('#gotopage2').click(function(){
        $('.page1').hide();
        $('.page2').show();
    })

    $('#txtpaymentmethod').change(function(){
        let id = $(this).val();
        $('#textnote').load('ajaxuser/display_note_payment.php?id='+id);
        if(id==1){
            $('.conclution').load('ajaxuser/buttonpayment.php?invID='+invid+'&amount='+firstNumber)
        }else{
            $('.conclution').load('ajaxuser/buttonpayment.php?invID='+invid+'&id='+id+'&amount='+firstNumber);
        }
        
    })

    $('#btnpaydeiteil').click(function(){
        $('.page1').show();
        $('.page2').hide();
        $('.popuppayment').show();
    })

    $('.closePayment').click(function(){
        $('.popuppayment').hide();
    })
    jQuery('.btnclosepayment').click(function(){
        jQuery('.popuppayment').hide();
    })
});