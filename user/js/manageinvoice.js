$(function(){

    $('.gotoinvoice').click(function(){
        let invID = $(this).attr('data-index');
        location.href="viewinvoice.php?id="+invID;
    });
})