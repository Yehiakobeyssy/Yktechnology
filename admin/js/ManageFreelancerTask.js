$(document).ready(function () {
    $.ajax({
        url: 'ajaxadmin/fetch_task.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            const tbody = $('.viewtask');
            tbody.empty(); // Clear previous content

            if (data.length === 0) {
                tbody.html('<tr><td colspan="7" class="text-center">No tasks found.</td></tr>');
                return;
            }

            data.forEach(task => {
                // Assign Bootstrap alert class based on status
                let statusClass = '';
                switch (task.status) {
                    case 'Send To Freelancer':
                        statusClass = 'alert-info';
                        break;
                    case 'In Accepted By freelancer':
                    case 'Working':
                        statusClass = 'alert-warning';
                        break;
                    case 'Finish':
                        statusClass = 'alert-success';
                        break;
                    case 'Cancel':
                        statusClass = 'alert-danger';
                        break;
                    default:
                        statusClass = 'alert-info';
                }

                const row = `
                    <tr>
                        <td>
                            <strong>${task.project_Name}</strong><br>
                            <small class="text-muted">Client: ${task.client_name || '—'}</small>
                        </td>
                        <td>
                            <strong>From:</strong> ${task.assignFrom}<br>
                            <small class="text-muted">To: ${task.assignTo}</small>
                        </td>
                        <td>${task.taskTitle}</td>
                        <td>
                            <strong>Start:</strong> ${task.StartDate}<br>
                            <small class="text-muted">Due: ${task.DueDate}</small>
                        </td>
                        <td>${task.communicationChannel}</td>
                        <td>
                            <span class="alert ${statusClass} p-2 m-2 d-block text-center">${task.status}</span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary mb-1">Show</button><br>
                            <button class="btn btn-sm btn-outline-danger">Cancel</button>
                        </td>
                    </tr>
                `;

                tbody.append(row);
            });
        },
        error: function () {
            $('.viewtask').html('<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>');
        }
    });

    $('#txtSearchTask').on('keyup', function () {
        const value = $(this).val().toLowerCase();

        $('.viewtask tr').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});
