$(function(){
    $('.bodyticket').load('ajaxadmin/fetch_agents.php')

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyticket').load('ajaxadmin/fetch_agents.php?search='+search);
    })
})