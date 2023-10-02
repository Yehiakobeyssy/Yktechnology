$(function(){

    $('.card1').click(function(){
        location.href="ManageService.php";
    });

    $('.card2').click(function(){
        location.href="ManageDomein.php"
    });

    $('.card3').click(function(){
        location.href="ManageTickets.php";
    });

    $('.card4').click(function(){
        location.href="manageinvoice.php"
    });

    $('.single_service').click(function(){
        let serviceid = $(this).attr('data-index');
        location.href='viewService.php?id='+serviceid
    })

    $('.one_ticket').click(function(){
        let ticketID = $(this).attr('data-index');
        location.href='viewTicket.php?id='+ticketID;
    })
})