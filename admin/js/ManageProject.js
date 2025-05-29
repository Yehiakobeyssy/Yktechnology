$(document).ready(function () {
    $.ajax({
        url: 'ajaxadmin/fetch_Project.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            let rows = '';
            if (response.length === 0) {
                rows = `
                    <tr>
                        <td colspan="7" class="text-center text-danger">No Records Found</td>
                    </tr>
                `;
            }else{
response.forEach(project => {
                // Determine status class
                let statusClass = '';
                switch (project.Status) {
                    case 'Study':
                        statusClass = 'alert alert-info';
                        break;
                    case 'Working':
                        statusClass = 'alert alert-warning';
                        break;
                    case 'Completed':
                        statusClass = 'alert alert-success';
                        break;
                    case 'Cancel':
                        statusClass = 'alert alert-danger';
                        break;
                    default:
                        statusClass = 'alert alert-secondary';
                }

                rows += `
                    <tr>
                        <td>
                            <strong>${project.project_Name}</strong><br>
                            <small>${project.ClientName}</small>
                        </td>
                        <td>${project.ProjectManager}</td>
                        <td>
                            ${project.Services} Services<br>
                            <strong>Budget:</strong> ${parseFloat(project.Budget).toFixed(2)} $
                        </td>
                        <td>${project.Developers}</td>
                        <td>
                            Start: ${project.StartTime}<br>
                            Expected: ${project.ExpectedDate}<br>
                            End: ${project.EndDate}
                        </td>
                        <td>
                            <div class="${statusClass}" style="padding:5px; text-align:center;">
                                ${project.Status}
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary">View</button>
                            <button class="btn btn-sm btn-secondary">Edit</button>
                        </td>
                    </tr>
                `;
            });
            } 
            

            $('.viewProject').html(rows);
        },
        error: function () {
            $('.viewProject').html('<tr><td colspan="7">Error loading data</td></tr>');
        }
    });

    $('#txtserarchProject').on('keyup', function () {
        let searchTerm = $(this).val().toLowerCase();

        $('.viewProject tr').each(function () {
            let rowText = $(this).text().toLowerCase();
            if (rowText.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Optional: Show "No Record Found" if all rows are hidden
        if ($('.viewProject tr:visible').length === 0) {
            if (!$('.viewProject .no-record').length) {
                $('.viewProject').append('<tr class="no-record"><td colspan="7" class="text-center text-danger">No Record Found</td></tr>');
            }
        } else {
            $('.viewProject .no-record').remove();
        }
    });
});

