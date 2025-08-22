$(function(){


    displaydoto()

    $('.btnviewproject').click(function(){
        let projectID = $(this).data('index');
        location.href="manageProjects.php?do=view&pid="+projectID
    })

    $('.btnviewtaskdaitail').click(function(){
        let taskID = $(this).data('index');
        location.href="manageTask.php?do=view&task="+taskID;
    })

    $(document).on('click','.dotoidcheck',function(){
        const index = $(this).data('index');
        
        $.ajax({
            url: 'ajaxstaff/updatetodo.php',
            method: 'GET',
            data: {index: index}, 
            dataType: 'json',
            success: function(response){
                consol.log('sacsess')
            }

        })
        displaydoto()
    })

    function displaydoto(){
        $.ajax({
            url:'ajaxstaff/fetchdoto.php',
            method:'GET',
            dataType:'json',
            success:function(response){ 
                let rows = '';

                if (!response || response.length === 0) {
                    rows = `
                        <tr>
                            <td colspan="4" class="text-center text-danger">No Records Found</td>
                        </tr>
                    `;
                } else {
                    response.forEach((doto, index) => {
                        let trClass = doto.done == 1 ? 1 : 0;
                        if(trClass == 0){
                            rows += `
                                <tr >
                                    <td><input type="checkbox" class="dotoidcheck" data-index="${doto.dotoID}" ${doto.done == 1 ? 'checked' : ''}></td>
                                    <td>${doto.priority_name}</td>
                                    <td>${doto.taskSubject}</td>
                                    <td>${doto.DateEnd}</td>
                                </tr>
                            `;
                        }
                        
                    });
                }
                $('#tblfetchtodo').html(rows);
            },
            error: function(xhr, status, error){
                console.error("AJAX Error:", error);
            }
        });
    }
})