$(function(){
    
    $('.addnew').click(function(){
        location.href="managedoto.php?do=add";
    })
    displaydoto()

    
    

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

    $('#searchBox').on('input', function() {
        let value = $(this).val().toLowerCase();

        $('#tblfetchdoto tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

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
                            <td colspan="5" class="text-center text-danger">No Records Found</td>
                        </tr>
                    `;
                } else {
                    response.forEach((doto, index) => {
                        let trClass = doto.done == 1 ? 'completed' : '';
                        rows += `
                            <tr class="${trClass}">
                                <td><input type="checkbox" class="dotoidcheck" data-index="${doto.dotoID}" ${doto.done == 1 ? 'checked' : ''}></td>
                                <td>${doto.priority_name}</td>
                                <td>${doto.taskSubject}</td>
                                <td>${doto.disktiption}</td>
                                <td>${doto.DateEnd}</td>
                            </tr>
                        `;
                    });
                }
                $('#tblfetchdoto').html(rows);
            },
            error: function(xhr, status, error){
                console.error("AJAX Error:", error);
            }
        });
    }
});
