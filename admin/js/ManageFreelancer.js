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
                        <td>${staff.DatewillBegin}</td>
                        <td><div class="${statusClass} statusfre">${statusText}</div></td>
                        <td>
                            <button class="btncotrol_staff btn btn-primary btnview" data-index="${staff.staffID}">View</button>
                            <button class="btncotrol_staff btn btn-success">Whatsup</button><br>
                            <button class="btncotrol_staff btn btn-danger btnblock" data-index="${staff.staffID}">Block</button>
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

    $('.viewFrelancer').on('click', '.btnview', function() {
        let freid = $(this).data("index");
        location.href="ManageFreelancer.php?do=view&id="+ freid
    });


    $('.btnaccepted').click(function(){
        let freid = $(this).data("index");
        location.href="ManageFreelancer.php?do=accepted&id="+ freid
    }) 

    $('.btnblock').click(function(){
        let freid = $(this).data("index");
        location.href="ManageFreelancer.php?do=blocked&id="+ freid
    })
    $('.viewFrelancer').on('click', '.btnblock', function() {
        let freid = $(this).data("index");
        location.href="ManageFreelancer.php?do=blocked&id="+ freid
    });

    $('.btncalcelblock').click(function(){
        location.href="ManageFreelancer.php";
        return false
    })

    function fetchPositions() {
        $.ajax({
            url: 'ajaxadmin/fetchpositions.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                let tbody = $('.datafech');
                tbody.empty();

                data.forEach(function (row) {
                    let checked = row.active_postion == 1 ? 'checked' : '';
                    tbody.append(`
                        <tr data-id="${row.Possition_ID}">
                            <td>
                                <input type="text" class="position-name" value="${row.Possition_Name}">
                            </td>
                            <td>
                                <input type="checkbox" class="active-checkbox" ${checked}>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function () {
                alert('Failed to fetch data.');
            }
        });
    }

    // Load data initially
    fetchPositions();

    // Update on input/checkbox change
    $(document).on('change', '.position-name, .active-checkbox', function () {
        let row = $(this).closest('tr');
        let id = row.data('id');
        let name = row.find('.position-name').val();
        let active = row.find('.active-checkbox').is(':checked') ? 1 : 0;

        $.ajax({
            url: 'ajaxadmin/updatepostion.php',
            method: 'POST',
            data: {
                Possition_ID: id,
                Possition_Name: name,
                active_postion: active
            },
            success: function (res) {
                if (res.trim() === 'success') {
                    console.log('Updated successfully');
                } else {
                    alert('Update failed');
                }
            },
            error: function () {
                alert('Error updating record');
            }
        });
    });

    $('.btnPosstion').click(function(){
        $('.popup').show()
    })

    $('.closepopup').click(function(){
        $('.popup').hide()
    })
});