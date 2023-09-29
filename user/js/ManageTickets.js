$(function(){

    $('.bodyticket').load('ajaxuser/displaytickets.php')
    $('#countticket').load('ajaxuser/displaytickets.php?count')

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyticket').load('ajaxuser/displaytickets.php?search='+search);
        $('#countticket').load('ajaxuser/displaytickets.php?count&search='+search);
    })


})