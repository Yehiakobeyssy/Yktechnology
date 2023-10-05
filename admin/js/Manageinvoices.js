$(function(){
    $('.bodyticket').load('ajaxadmin/displayinvoice.php');

    $('#txtsearch').keyup(function(){
        let textsearch = $(this).val();
        let search = textsearch.replace(/ /g, '_');
        $('.bodyticket').load('ajaxadmin/displayinvoice.php?search='+search);
    })

    $.ajax({
        url: 'ajaxadmin/fetch_payments.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // Process the JSON response and display it in the 'payments-list' div
            var html = '<table>';
            html += '<tr><th>Payment ID</th><th>Payment Date</th><th>Client Name</th><th>Invoice ID</th><th>Method</th><th>No of Documents</th><th>Payment Amount</th></tr>';
            
            $.each(data, function(index, payment) {
                html += '<tr>';
                html += '<td>' + payment.paymentID + '</td>';
                html += '<td>' + payment.Payment_Date + '</td>';
                html += '<td>' + payment.ClientName + '</td>';
                html += '<td>' + payment.invoiceID + '</td>';
                html += '<td>' + payment.methot + '</td>';
                html += '<td>' + payment.NoofDocument + '</td>';
                html += '<td>' + payment.FormattedPaymentAmount + '</td>';
                html += '</tr>';
            });

            html += '</table>';
            $('#payments-list').html(html);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching payments:', error);
        }
    });

    // Handle search input
    $('#search').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        var table = $('#payments-list table');
        var rows = table.find('tr:gt(0)'); // Exclude table header row
        rows.show();

        if (searchTerm !== '') {
            rows.each(function() {
                var row = $(this);
                var paymentID = row.find('td:eq(0)').text().toLowerCase();
                var clientName = row.find('td:eq(2)').text().toLowerCase();

                if (paymentID.indexOf(searchTerm) === -1 && clientName.indexOf(searchTerm) === -1) {
                    row.hide();
                }
            });
        }
    });
})
