document.addEventListener("DOMContentLoaded", function () {
    const searchBox = document.getElementById("searchBox");
    const taskList = document.getElementById("taskList");

    // Function to fetch and display task data
    function fetchAndDisplayData() {
        // Create an XMLHttpRequest to fetch data from your server-side script
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "ajaxadmin/fetchdatatask.php", true);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    // Parse the JSON response from the server
                    const responseData = JSON.parse(xhr.responseText);

                    // Clear existing table rows
                    taskList.innerHTML = "";

                    // Loop through the data and generate table rows
                    responseData.forEach((task) => {
                        const doneClass = task.done ? 'completed' : '';

                        const row = document.createElement("tr");
                        if (doneClass) {
                            row.classList.add(doneClass);
                        }

                        row.innerHTML = `
                            <td><input type="checkbox" name="task_done[]" value="${task.taskID}" ${task.done ? 'checked' : ''}></td>
                            <td>${task.priority_name}</td>
                            <td class="task-name">${task.Task_subject}</td>
                            <td >${task.Discription}</td>
                            <td>${task.Datend}</td>
                        `;

                        // Add a click event listener to the checkbox
                        const checkbox = row.querySelector("input[type='checkbox']");
                        checkbox.addEventListener("click", function () {
                            toggleTaskDoneStatus(task.taskID, checkbox.checked, row);
                        });

                        taskList.appendChild(row);
                    });
                } catch (error) {
                    console.error("Error parsing JSON:", error);
                }
            }
        };

        xhr.send();
    }

    // Fetch and display data initially
    fetchAndDisplayData();

    // Add an event listener for keyup in the search box
    searchBox.addEventListener("keyup", function () {
        // Get the search query
        const query = searchBox.value.toLowerCase();

        // Filter and display the matching tasks
        const rows = taskList.querySelectorAll("tr");
        rows.forEach((row) => {
            const taskName = row.querySelector(".task-name").textContent.toLowerCase();
            if (taskName.includes(query)) {
                row.style.display = "table-row";
            } else {
                row.style.display = "none";
            }
        });
    });

    // Function to toggle task "done" status
    function toggleTaskDoneStatus(taskID, isDone, row) {
        // Create an XMLHttpRequest to send an AJAX request to update the "done" status
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "ajaxadmin/updatetaskstatus.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Handle the response from the server
                if (isDone) {
                    // Mark the row as completed
                    row.classList.add("completed");
                } else {
                    // Remove the completed class
                    row.classList.remove("completed");
                }
            }
        };

        // Send the task ID and "done" status to the server
        xhr.send(`taskID=${taskID}&isDone=${isDone ? 1 : 0}`);
    }
});
