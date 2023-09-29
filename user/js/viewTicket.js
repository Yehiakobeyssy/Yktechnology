$(function(){

    $('.addnewcoment').click(function() {
        var newReplay = $('.newreplay');
        var openSpan = $('#open');
        if (newReplay.is(':visible')) {
            newReplay.hide();
            openSpan.html('<i class="fa-solid fa-plus"></i>');
        } else {
            newReplay.show();
            openSpan.html('<i class="fa-solid fa-minus"></i>');
        }
    });
    
})