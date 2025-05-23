$(document).ready(function() {
    $.ajax({
        url: 'ajaxadmin/fetchstaff.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            let output = '';

            $.each(data, function(index, staff) {
                let statusClass = '';
                let statusText = staff.status;

                // Assign Bootstrap alert class based on status
                if (statusText === 'on Study') {
                    statusClass = 'alert alert-warning p-1 m-0 text-center';
                } else if (statusText === 'accepted') {
                    statusClass = 'alert alert-success p-1 m-0 text-center';
                } else if (statusText === 'blocked') {
                    statusClass = 'alert alert-danger p-1 m-0 text-center';
                } else {
                    statusClass = 'alert alert-secondary p-1 m-0 text-center';
                }

                output += `
                    <tr>
                        <td>
                            <strong>${staff.fullname}</strong><br>
                            <small>${staff.Staff_Phone}</small><br>
                            <small>${staff.Staff_email}</small>
                        </td>
                        <td>
                            ${staff.Staff_address}<br>
                            <small>${staff.Region}</small>
                        </td>
                        <td>${staff.Possition_Name}</td>
                        <td>$${staff.expected_sallary}</td>
                        <td><div class="${statusClass} statusfre">${statusText}</div></td>
                        <td>
                            <button class="btncotrol_staff btn btn-primary">View</button>
                            <button class="btncotrol_staff btn btn-success">Whatsup</button><br>
                            <button class="btncotrol_staff btn btn-danger">Block</button>
                            <button class="btncotrol_staff btn btn-secondary">Send Task</button>
                        </td>
                    </tr>
                `;
            });

            // If no data found
            if (output === '') {
                output = '<tr><td colspan="6" class="text-center">No freelancers found</td></tr>';
            }

            $('.viewFrelancer').html(output);
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            $('.viewFrelancer').html('<tr><td colspan="6" class="text-danger text-center">Error loading data</td></tr>');
        }
    });

    $('#txtserarchFree').on('keyup', function() {
        let value = $(this).val().toLowerCase();

        $('.viewFrelancer tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});