$(function(){

$.ajax({
        url:'ajaxstaff/fetchallTask.php',
        method:'GET',
        dataType:'json',
        success:function(response){
            let rows = '';
            if (response.length === 0) {
                rows = `
                    <tr>
                        <td colspan="7" class="text-center text-danger">No Records Found</td>
                    </tr>
                `;
            }else{
                response.forEach(task=>{
                    let statusClass = '';
                    switch (task.Status) {
                        case 'Send To Freelancer':
                            statusClass = 'alert alert-info';
                            break;
                        case 'Accepted By freelancer':
                            statusClass = 'alert alert-warning';
                            break;
                        case 'Working':
                            statusClass = 'alert alert-warning';
                            break;
                        case 'Finish':
                            statusClass = 'alert alert-success';
                            break;
                        case 'Cancel':
                            statusClass = 'alert alert-danger';
                            break;
                        default:
                            statusClass = 'alert alert-secondary';
                    }
                    rows +=`
                        <tr>
                            <td>${task.taskID}</td>
                            <td>${task.admin_FName} ${task.admin_LName}</td>
                            <td>${task.project_Name}</td>
                            <td>${task.taskTitle}</td>
                            <td>
                                <label><strong>Start Date : </strong>${task.StartDate}</label><br>
                                <label><strong>Due Date : </strong>${task.DueDate}</label><br>
                                <label><strong>Finish Date :</strong> ${task.FinishDate}</label>
                            </td>
                            <td>
                                <div class="${statusClass}" style="padding:5px; text-align:center;">
                                    ${task.Status_name}
                                </div>
                            </td>
                            <td id="ctl">
                                <button class="btnviewTask" data-index="${task.taskID}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.63435 3.67285C12.721 3.67285 14.6548 6.41283 15.3356 7.69377C15.5524 8.10165 15.5524 8.57739 15.3356 8.98527C14.6548 10.2662 12.721 13.0062 8.63435 13.0062C4.54768 13.0062 2.61386 10.2662 1.93309 8.98527C1.71631 8.57739 1.71631 8.10165 1.93309 7.69377C2.61386 6.41283 4.54768 3.67285 8.63435 3.67285ZM5.19191 5.99131C4.08507 6.72468 3.43876 7.7018 3.11047 8.31951C3.1068 8.3264 3.10529 8.33119 3.10464 8.33382C3.10397 8.33649 3.10384 8.33952 3.10384 8.33952C3.10384 8.33952 3.10397 8.34255 3.10464 8.34522C3.10529 8.34785 3.1068 8.35263 3.11047 8.35953C3.43876 8.97723 4.08507 9.95436 5.19191 10.6877C4.73493 10.0191 4.46768 9.21052 4.46768 8.33952C4.46768 7.46852 4.73493 6.65994 5.19191 5.99131ZM12.0768 10.6877C13.1836 9.95435 13.8299 8.97723 14.1582 8.35953C14.1619 8.35263 14.1634 8.34785 14.1641 8.34522C14.1645 8.34349 14.1648 8.34104 14.1648 8.34104L14.1648 8.33952L14.1646 8.33658L14.1641 8.33382C14.1634 8.33119 14.1619 8.3264 14.1582 8.31951C13.8299 7.7018 13.1836 6.72469 12.0768 5.99132C12.5338 6.65995 12.801 7.46852 12.801 8.33952C12.801 9.21051 12.5338 10.0191 12.0768 10.6877ZM5.80101 8.33952C5.80101 6.77471 7.06954 5.50618 8.63435 5.50618C10.1992 5.50618 11.4677 6.77471 11.4677 8.33952C11.4677 9.90432 10.1992 11.1729 8.63435 11.1729C7.06954 11.1729 5.80101 9.90432 5.80101 8.33952Z" fill="#7F8291"/>
                                    </svg>
                                </button>
                                <button class="btnacceptthistask" data-index="${task.taskID}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>
                                </button>
                                <button class="btncancelthistask" data-index="${task.taskID}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="6" x2="6" y2="18" />
                                        <line x1="6" y1="6" x2="18" y2="18" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    `
                })
            }
            $('#tblTask').html(rows);
        },
        error:function(){
            $('#tblTask').html('<tr><td colspan="7">Error loading data</td></tr>')
        }
    })

    $('#txtserarchTask').on('keyup', function () {
        let searchTerm = $(this).val().toLowerCase();

        $('#tblTask tr').each(function () {
            let rowText = $(this).text().toLowerCase();
            if (rowText.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        if ($('#tblTask tr:visible').length === 0) {
            if (!$('#tblprojects .no-record').length) {
                $('#tblprojects').append('<tr class="no-record"><td colspan="6" class="text-center text-danger">No Record Found</td></tr>');
            }
        } else {
            $('#tblTask .no-record').remove();
        }
    });

    $(document).on('click', '.btnviewTask', function () {
        const index = $(this).data('index');
        location.href="manageTask.php?do=view&task="+index
    })
    $(document).on('click', '.btnacceptthistask', function () {
        const index = $(this).data('index');
        location.href="manageTask.php?do=accepted&task="+index
    })
    $(document).on('click', '.btncancelthistask', function () {
        const index = $(this).data('index');
        location.href="manageTask.php?do=cancel&task="+index
    })

    $("#btnedidReord").click(function() {
        var url = window.location.href;
        var urlObj = new URL(url);
        var taskValue = urlObj.searchParams.get("task");

        $.ajax({
            url: 'ajaxstaff/fetchtaskreport.php',
            method: 'GET',
            data: {id: taskValue},
            dataType: 'json',
            success: function(response){
                var note = response.notes; 
                console.log(note);
                $('#txttaskID').val(taskValue);
                $('#newReport').val(note);
                $('.popupreport').show();
            }
        });
    });

    $('.closepopup_report').click(function(){
        $('.popupreport').hide();
    })

    $(document).on('click', '.btnAccepttask', function () {
        const index = $(this).data('index');
        location.href="manageTask.php?do=accepted&task="+index
    })
    $(document).on('click', '.btnFinishtask', function () {
        const index = $(this).data('index');
        location.href="manageTask.php?do=finish&task="+index
    })
    $(document).on('click', '.btncanceltask', function () {
        const index = $(this).data('index');
        location.href="manageTask.php?do=cancel&task="+index
    })
}) 