$(function(){
    $('.bodyticket').load('ajaxsale/displaytickets.php')
    $('#countticket').load('ajaxsale/displaytickets.php?count')

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyticket').load('ajaxsale/displaytickets.php?search='+search);
        $('#countticket').load('ajaxsale/displaytickets.php?count&search='+search);
    })

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
});