$(function(){

    $.ajax({
        url:'ajaxstaff/fetchallproject.php',
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
                response.forEach(project=>{
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
                    rows +=`
                        <tr>
                            <td>${project.Project_Name}</td>
                            <td>${project.Client_Name}</td>
                            <td>${project.Manager}</td>
                            <td>${project.Posstion}</td>
                            <td>${project.Tasks}</td>
                            <td>
                                <div class="${statusClass}" style="padding:5px; text-align:center;">
                                    ${project.Status}
                                </div>
                            </td>
                            <td>
                                <button class="btnviewProject" data-index="${project.ProjectID}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.63435 3.67285C12.721 3.67285 14.6548 6.41283 15.3356 7.69377C15.5524 8.10165 15.5524 8.57739 15.3356 8.98527C14.6548 10.2662 12.721 13.0062 8.63435 13.0062C4.54768 13.0062 2.61386 10.2662 1.93309 8.98527C1.71631 8.57739 1.71631 8.10165 1.93309 7.69377C2.61386 6.41283 4.54768 3.67285 8.63435 3.67285ZM5.19191 5.99131C4.08507 6.72468 3.43876 7.7018 3.11047 8.31951C3.1068 8.3264 3.10529 8.33119 3.10464 8.33382C3.10397 8.33649 3.10384 8.33952 3.10384 8.33952C3.10384 8.33952 3.10397 8.34255 3.10464 8.34522C3.10529 8.34785 3.1068 8.35263 3.11047 8.35953C3.43876 8.97723 4.08507 9.95436 5.19191 10.6877C4.73493 10.0191 4.46768 9.21052 4.46768 8.33952C4.46768 7.46852 4.73493 6.65994 5.19191 5.99131ZM12.0768 10.6877C13.1836 9.95435 13.8299 8.97723 14.1582 8.35953C14.1619 8.35263 14.1634 8.34785 14.1641 8.34522C14.1645 8.34349 14.1648 8.34104 14.1648 8.34104L14.1648 8.33952L14.1646 8.33658L14.1641 8.33382C14.1634 8.33119 14.1619 8.3264 14.1582 8.31951C13.8299 7.7018 13.1836 6.72469 12.0768 5.99132C12.5338 6.65995 12.801 7.46852 12.801 8.33952C12.801 9.21051 12.5338 10.0191 12.0768 10.6877ZM5.80101 8.33952C5.80101 6.77471 7.06954 5.50618 8.63435 5.50618C10.1992 5.50618 11.4677 6.77471 11.4677 8.33952C11.4677 9.90432 10.1992 11.1729 8.63435 11.1729C7.06954 11.1729 5.80101 9.90432 5.80101 8.33952Z" fill="#7F8291"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    `
                })
            }
            $('#tblprojects').html(rows);
        },
        error:function(){
            $('#tblprojects').html('<tr><td colspan="7">Error loading data</td></tr>')
        }
    })

    $('#txtserarchProject').on('keyup', function () {
        let searchTerm = $(this).val().toLowerCase();

        $('#tblprojects tr').each(function () {
            let rowText = $(this).text().toLowerCase();
            if (rowText.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        if ($('#tblprojects tr:visible').length === 0) {
            if (!$('#tblprojects .no-record').length) {
                $('#tblprojects').append('<tr class="no-record"><td colspan="6" class="text-center text-danger">No Record Found</td></tr>');
            }
        } else {
            $('#tblprojects .no-record').remove();
        }
    });

    $(document).on('click', '.btnviewProject', function () {
        const index = $(this).data('index');
        location.href="manageProjects.php?do=view&pid="+index
    })

    $('.btnbacktomanage').click(function(){
        location.href="manageProjects.php"
    })
});