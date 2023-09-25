$(function(){
    const currentURL = window.location.href;
    const catValue = (new URL(currentURL)).searchParams.get("cat");
    $('.display_services').load('ajaxService.php?cat='+catValue);
    $('#txtSearch').keyup(function(){
        let txtsearch = $(this).val();
        $('.display_services').load('ajaxService.php?cat='+catValue+'&search='+txtsearch);
    })  
});