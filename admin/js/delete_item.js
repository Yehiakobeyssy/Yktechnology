// delete_item.js

function deleteItem(serviceID) {
    //alert("Deleting item with ServiceID: " + serviceID);
    // Send an AJAX request to a PHP script to delete the item
    $.ajax({
        type: 'POST',
        url: 'delete_item.php', // Create this PHP script
        data: { serviceID: serviceID },
        success: function(response) {
            // Handle the response here, such as removing the row from the table
            if (response === 'success') {
                // Assuming you have a table row with an ID matching the serviceID
                $('#' + serviceID).remove();
            } else {
                alert('Failed to delete item.');
            }
        }
    }); 
    location.reload();
}
