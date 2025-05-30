$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const doParam = urlParams.get('do');


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
                            <button class="btn btn-sm btn-primary btnviewProject" data-index="${project.ProjectID}">View</button>
                            <button class="btn btn-sm btn-secondary btnEditProject" data-index="${project.ProjectID}">Edit</button>
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

    $('.btnnewproject').click(function(){
        location.href="ManageProject.php?do=add"
    });

    /*begin with new*/
    function loadClientInfoAndServices(clientID) {
        $('.txtClient').val(clientID);

        if (clientID !== '') {
            // Fetch client info
            $.ajax({
                url: 'ajaxadmin/fetchClinetinfo.php',
                method: 'GET',
                data: { client: clientID },
                dataType: 'json',
                success: function (response) {
                    if (response.length > 0) {
                        $('.lblAddress').text(response[0].Client_addresse);
                        $('.lblphonenUmber').text(response[0].Client_Phonenumber);
                    } else {
                        $('.lblAddress').text('---');
                        $('.lblphonenUmber').text('---');
                    }
                },
                error: function () {
                    $('.lblAddress').text('Error loading address');
                    $('.lblphonenUmber').text('Error loading phone');
                }
            });

            // Fetch services
            $.ajax({
                url: 'ajaxadmin/fetchServiceClient.php',
                method: 'GET',
                data: { client: clientID },
                dataType: 'json',
                success: function (response) {
                    let optionSer = '<option value="">Select Service</option>';
                    if (response.length > 0) {
                        response.forEach(service => {
                            optionSer += `<option value="${service.ServicesID}">${service.ServiceTitle} ( ${service.Service_Name} ) </option>`;
                        });
                    } else {
                        optionSer = '<option value="">No services found</option>';
                    }
                    $('.selService').html(optionSer);
                },
                error: function () {
                    $('.selService').html('<option value="">Error loading services</option>');
                }
            });
        } else {
            $('.lblAddress').text('---');
            $('.lblphonenUmber').text('---');
            $('.selService').html('<option value="">Select Service</option>');
        }
    }

    // Trigger on client selection
    $('.clientSelect').on('change', function () {
        const clientID = $(this).val();
        loadClientInfoAndServices(clientID);
    });

    // Trigger on page load if client is already selected
    $(document).ready(function () {
        const initialClientID = $('.clientSelect').val();
        if (initialClientID !== '') {
            loadClientInfoAndServices(initialClientID);
        }
    });


    $('.selService').on('change', function () {
        const serviceID = $(this).val();

        if (serviceID) {
            $.ajax({
                url: 'ajaxadmin/addServiceprojectarray.php',
                method: 'GET',
                data: { serID: serviceID },
                success: function(response) {
                    // After adding service to session, fetch updated list
                    $.ajax({
                        url: 'ajaxadmin/fetchServiceProject.php',
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            let rows = '';
                            response.services.forEach((item, index) => {
                                rows += `
                                    <tr>
                                        <td>${item.serviceID}</td>
                                        <td>${item.ServiceTitle}</td>
                                        <td>${item.Budget}</td>
                                        <td>
                                            <input 
                                                type="text" 
                                                name="note_${index}" 
                                                value="${item.note}" 
                                                data-index="${index}" 
                                                class="noteInput"
                                            />
                                        </td>
                                        <td>
                                            <button class="deleteServiceBtn" data-index="${index}" style="color: red; border: none; background: none;">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                            });

                            $('.viewServiceProject').html(rows);

                            // Display total budget
                            $('.totalbudgut').text(response.totalbudget + ' $');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching service list:', status, error);
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error adding service:', status, error);
                }
            });
        }
    });


    $('.selFreelancer').on('change', function () {
        const freelancerId = $(this).val();

        if (freelancerId) {
            $.ajax({
                url: 'ajaxadmin/addFreelancerObjectarray.php',
                method: 'GET',
                data: { freelancer: freelancerId },
                success: function(response) {
                    // Fetch updated list
                    loadFreelancers()
                },
                error: function(xhr, status, error) {
                    console.error('Error adding freelancer:', status, error);
                }
            });
        }
    });


    $(document).on('change', '.noteInput', function () {
        const index = $(this).data('index');
        const note = $(this).val();

        $.ajax({
            url: 'ajaxadmin/updateNote.php',
            method: 'POST',
            data: { index: index, note: note },
            success: function (res) {
                console.log('Note updated');
            },
            error: function (xhr, status, error) {
                console.error('Error updating note:', status, error);
            }
        });
    });

    $(document).on('click', '.deleteServiceBtn', function () {
        const index = $(this).data('index');
        $.ajax({
            url: 'ajaxadmin/deleteService.php',
            method: 'POST',
            data: { index: index },
            success: function (res) {
                const response = JSON.parse(res);
                if (response.status === 'success') {
                    // Refresh the list
                    $('.selService').trigger('change');
                } else {
                    alert('Failed to delete the service.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error deleting service:', status, error);
            }
        });

        return false
    });

    $(document).on('click','.btnviewProject',function(){
        const ProID = $(this).data('index');
        location.href= "ManageProject.php?do=view&proid="+ProID;
    })
    $(document).on('click','.btnEditProject',function(){
        const ProID = $(this).data('index');
        location.href= "ManageProject.php?do=edid&proid="+ProID;
    })

    // Update when user finishes typing (blur)
    $(document).on('blur', '.freelancerInput', function () {
        const $input = $(this); // save reference to input
        const index = $input.data('index');
        const field = $input.data('field');
        const value = $input.val();

        $.ajax({
            url: 'ajaxadmin/updateFreelancerField.php',
            method: 'POST',
            data: { index, field, value },
            success: function (res) {
                console.log('Updated:', field, value);
                $input.val(value); // force value back in case it gets overwritten
            },
            error: function (xhr) {
                console.error('Update error:', xhr);
            }
        });

        
    });

    $(document).on('blur', '.freelancerInput[type="number"]', function () {
        
        loadFreelancers()
        
    });


    // Delete button
    $(document).on('click', '.deleteFreelancerBtn', function () {
        const index = $(this).data('index');

        $.ajax({
            url: 'ajaxadmin/deleteFreelancerFromSession.php',
            method: 'POST',
            data: { index },
            success: function (res) {
                // Refresh the freelancer list
                loadFreelancers(); // You can create this function to re-fetch and re-render the table
                console.error(res);
            },
            error: function (xhr) {
                console.error('Delete error:', xhr);
            }
        });

        return false
    });


    // Load freelancers list
    function loadFreelancers() {
        const totalBudget = parseFloat($('.totalbudgut').text()) || 0;

        $.getJSON('ajaxadmin/fetchfreelancerProject.php', function (res) {
            let rows = res.freelancers.map((item, index) => {
                let share = parseFloat(item.Share) || 0;
                let amount = ((totalBudget * share) / 100).toFixed(2);

                return `
                    <tr>
                        <td>${item.Name}</td>
                        <td><input type="text" name="service_${index}" value="${item.Service}" data-index="${index}" data-field="Service" class="freelancerInput" /></td>
                        <td><input type="number" name="share_${index}" value="${item.Share}" data-index="${index}" data-field="share" class="freelancerInput" /></td>
                        <td>${amount}</td>
                        <td><input type="text" name="note_${index}" value="${item.Note}" data-index="${index}" data-field="note" class="freelancerInput" /></td>
                        <td><button class="deleteFreelancerBtn" data-index="${index}" style="color:red; border:none; background:none;"><i class="fa-solid fa-trash"></i></button></td>
                    </tr>
                `;
            }).join('');
            $('.viewfreelancers').html(rows);
        });
    }


    function normalizeSharesTo100() {
        let inputs = $('.freelancerInput[name^="share_"]').toArray();
        inputs.push($('.txtsharereserve')[0]);
        inputs.push($('.txtsharemanagment')[0]);

        let rawValues = inputs.map(input => ({
            element: $(input),
            original: parseFloat($(input).val()) || 0
        }));

        let totalOriginal = rawValues.reduce((sum, item) => sum + item.original, 0);

        // Only normalize if total is greater than 100
        if (totalOriginal > 100) {
            let scale = 100 / totalOriginal;
            let intValues = [];
            let totalInt = 0;

            // Scale and floor to integers
            for (let i = 0; i < rawValues.length; i++) {
                let scaled = Math.floor(rawValues[i].original * scale);
                intValues.push(scaled);
                totalInt += scaled;
            }

            // Distribute remaining points
            let remaining = 100 - totalInt;
            let i = 0;
            while (remaining > 0) {
                intValues[i % intValues.length]++;
                remaining--;
                i++;
            }

            // Update UI
            for (let i = 0; i < rawValues.length; i++) {
                rawValues[i].element.val(intValues[i]);
            }
        }

        // Always send current values to backend
        let sendData = {
            freelancers: [],
            reserve: parseInt($('.txtsharereserve').val()) || 0,
            management: parseInt($('.txtsharemanagment').val()) || 0
        };

        $('.freelancerInput[name^="share_"]').each(function () {
            let index = $(this).data('index');
            let share = parseInt($(this).val()) || 0;
            sendData.freelancers.push({ index: index, share: share });
        });

        // Send to backend
        $.ajax({
            url: 'ajaxadmin/updateSessionShares.php',
            method: 'POST',
            data: JSON.stringify(sendData),
            contentType: 'application/json',
            success: function (res) {
                console.log('Session updated');
            },
            error: function (xhr) {
                console.error('Session update failed:', xhr);
            }
        });

        updateTotalDisplay();
    }
    
    $(document).on('blur', '.freelancerInput[type="number"], .txtsharemanagment, .txtsharereserve', function () {
        normalizeSharesTo100();
    });

    $('.btnbacktomanage').click(function(){
        location.href="ManageProject.php"
    })


    if (doParam === 'edid') {
        $.ajax({
            url: 'ajaxadmin/fetchServiceProject.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                let rows = '';
                response.services.forEach((item, index) => {
                    rows += `
                        <tr>
                            <td>${item.serviceID}</td>
                            <td>${item.ServiceTitle}</td>
                            <td>${item.Budget}</td>
                            <td>
                                <input 
                                    type="text" 
                                    name="note_${index}" 
                                    value="${item.note}" 
                                    data-index="${index}" 
                                    class="noteInput"
                                />
                            </td>
                            <td>
                                <button class="deleteServiceBtn" data-index="${index}" style="color: red; border: none; background: none;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('.viewServiceProject').html(rows);

                // Display total budget
                $('.totalbudgut').text(response.totalbudget + ' $');
            },
            error: function(xhr, status, error) {
                console.error('Error fetching service list:', status, error);
            }
        });

        loadFreelancers()
    }

});

