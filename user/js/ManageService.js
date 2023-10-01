$(function(){

    $('.bodyService').load('ajaxuser/displayServices.php')
    $('#countService').load('ajaxuser/displayServices.php?count')

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyService').load('ajaxuser/displayServices.php?search='+search);
        $('#countService').load('ajaxuser/displayServices.php?count&search='+search);
    })


})