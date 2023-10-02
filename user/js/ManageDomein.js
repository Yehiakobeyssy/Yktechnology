$(function(){

    $('.bodyService').load('ajaxuser/displayDomain.php')
    $('#countService').load('ajaxuser/displayDomain.php?count')

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyService').load('ajaxuser/displayDomain.php?search='+search);
        $('#countService').load('ajaxuser/displayDomain.php?count&search='+search);
    })


})