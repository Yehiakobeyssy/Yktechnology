$(function(){

    let currentURL = window.location.href

    $('.btnaddspe').click(function(){
        let serviceID = $(this).attr('data-index');
        let txttext =$('#txt'+serviceID).val();
        let newtext = txttext.replace(/ /g, '_');
        $('.runajax').load('ajaxadmin/insertspeafications.php?serviceID='+serviceID+'&text='+newtext);
        location.href= currentURL;
    })
    $('.btndeletespe').click(function(){
        let speaficID = $(this).attr('data-index');
        $('.runajax').load('ajaxadmin/deleteonespafication.php?spaID='+speaficID);
        location.href= currentURL;
    })
})