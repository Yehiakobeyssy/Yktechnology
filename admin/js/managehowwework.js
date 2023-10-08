document.addEventListener('DOMContentLoaded', function () {
    // Function to fetch data from the server and populate the table
    function fetchData() {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'ajaxadmin/fetch_howwework.php', true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const data = JSON.parse(xhr.responseText);
                populateTable(data);
            }
        };
        xhr.send();
    }

    // Function to populate the HTML table with fetched data
    function populateTable(data) {
        const table = document.querySelector('#countryTable');
        const tbody = table.querySelector('tbody');
        tbody.innerHTML = ''; // Clear the existing table rows
        data.forEach(function (row) {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `<td>${row.No}</td><td contenteditable='true'>${row.title}</td><td contenteditable='true'>${row.discription}</td><td><button class="edit-btn"><i class="fa-solid fa-pen"></i></button><button class="delete-btn"><i class="fa-solid fa-trash-can"></i></button></td>`;
            tbody.appendChild(newRow);

            // Attach event listener for edit button
            const editBtn = newRow.querySelector('.edit-btn');
            editBtn.addEventListener('click', function () {
                editRow(row.No, newRow);
            });

            // Attach event listener for delete button
            const deleteBtn = newRow.querySelector('.delete-btn');
            deleteBtn.addEventListener('click', function () {
                deleteRow(row.No, newRow);
            });
        });
    }

    // Function to edit a row
    function editRow(id, rowElement) {
        const cells = rowElement.querySelectorAll('td');
        const newName = cells[1].textContent;
        const newTVA = cells[2].textContent;

        // Send an AJAX request to update the database
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'ajaxadmin/updatehowweWork.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    const response = xhr.responseText;
                    console.log('Response:', response); // Add this line for debugging
                    if (response === 'success') {
                        //alert('Row updated successfully.');
                    } else {
                        //alert('Row updated successfully..');
                    }
                } else {
                    //console.error('Request failed with status:', xhr.status, xhr.statusText); // Add this line for debugging
                    //alert('Failed to update row. Please check the console for details.');
                }
            }
        };
        xhr.send(`No=${id}&title=${newName}&discription=${newTVA}`);
    }

    // Function to delete a row
    function deleteRow(id, rowElement) {
        if (confirm('Are you sure you want to delete this Section')) {
            // Send an AJAX request to delete the country
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajaxadmin/deleteHowweWork.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        const response = xhr.responseText;
                        if (response === 'success') {
                            // Remove the deleted row from the table
                            rowElement.remove();
                            //alert('Country deleted successfully.');
                        } else {
                            //alert('Failed to delete country. Server response: ' + response);
                            //console.log('Server Response:', response);
                        }
                    } else {
                        //alert('Failed to delete country. Server status: ' + xhr.status);
                    }
                }
            };
            xhr.send(`No=${id}`);
        }
        
    }
    

    // Fetch data when the page loads
    fetchData();

    function filterData(searchText) {
        const table = document.querySelector('#countryTable');
        const tbody = table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr');

        rows.forEach(function (row) {
            const countryName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();

            if (countryName.includes(searchText.toLowerCase())) {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Attach event listener to the search box
    const searchBox = document.querySelector('#searchBox');
    searchBox.addEventListener('keyup', function () {
        const searchText = searchBox.value.trim();
        filterData(searchText);
    });
});
