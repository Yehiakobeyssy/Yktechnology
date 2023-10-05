document.addEventListener("DOMContentLoaded", function () {
    // Select all task checkboxes
    const taskCheckboxes = document.querySelectorAll(".task-checkbox");

    // Add a click event listener to each task checkbox
    taskCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener("click", function () {
            const taskId = checkbox.getAttribute("data-task-id");
            const isDone = checkbox.checked;

            // Create an XMLHttpRequest to send an AJAX request to update the "done" status
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "ajaxadmin/updatetaskstatus.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Handle the response from the server if needed
                    // You can update the UI or perform other actions here
                    if (isDone) {
                        // Add the "completed" class to the parent row
                        checkbox.closest("tr").classList.add("completed");
                    } else {
                        // Remove the "completed" class from the parent row
                        checkbox.closest("tr").classList.remove("completed");
                    }
                }
            };

            // Send the task ID and "done" status to the server
            xhr.send(`taskID=${taskId}&isDone=${isDone ? 1 : 0}`);
        });
    });
});
