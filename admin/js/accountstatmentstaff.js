$(document).ready(function(){
    // إخفاء popup عند الضغط على +
    $('.close_popup').click(function(){
        $('.popupadd').fadeOut(200);
    });

    // فتح popup عند الضغط على الزر
    $('.addstatment').click(function(){
        $('.popupadd').fadeIn(200).css('display','flex');
    });

});
