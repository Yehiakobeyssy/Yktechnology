<?php
    session_start();

    if(!isset($_COOKIE['useradmin'])){
        if(!isset($_SESSION['useradmin'])){
            header('location:index.php');
        }
    }
    $adminId= (isset($_COOKIE['useradmin']))?$_COOKIE['useradmin']:$_SESSION['useradmin'];

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT admin_active,admin_FName,admin_LName FROM  tbladmin WHERE admin_ID=?');
    $sql->execute(array($adminId));
    $result=$sql->fetch();
    $isActive=$result['admin_active'];
    $firstname= $result['admin_FName'];
    $lastName = $result['admin_LName'];
    $full_name = $firstname .' ' . $lastName ;

    if($isActive == 0){
        setcookie("useradmin","",time()-3600);
        unset($_SESSION['useradmin']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/ManageTask.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Do-to List</h1>
                <a href="ManageTask.php?do=add" class="btn btn-success btnnewtask"> + New Task </a>
            </div>
            <?php
                $do= (isset($_GET['do']))?$_GET['do']:'manage';

                if($do=='manage'){?>
                <div class="manage_container">
                    <input type="text" id="searchBox" placeholder="Search...">
                    <table class="todo-table">
                        <thead>
                            <tr>
                                <th>Done</th>
                                <th>Importance</th>
                                <th>Task</th>
                                <th>Discription</th>
                                <th>Date to Finish</th>
                            </tr>
                        </thead>
                        <tbody id="taskList">
                            <!-- Task rows will be dynamically generated here -->
                        </tbody>
                    </table>
                </div>
                <!-- Modal -->


                <?php
                }elseif($do=='add'){?>
                <div class="newtask">
                    <h1>Add New Task</h1>
                    <form action="" method="post">
                        <label for="priorityID">Priority:</label>
                        <select name="priorityID" id="priorityID">
                            <?php
                                $sql=$con->prepare('SELECT priority_id,priority_name FROM tbltaskpriority ');
                                $sql->execute();
                                $rows=$sql->fetchAll();
                                foreach($rows as $row){
                                    echo '<option value="'.$row['priority_id'].'">'.$row['priority_name'].'</option>';
                                }
                            ?>
                        </select><br><br>
                        <label for="Datetask">Task Date:</label>
                        <input type="date" name="Datetask" id="Datetask" required><br><br>
                        <label for="Task_subject">Task Subject:</label>
                        <input type="text" name="Task_subject" id="Task_subject" required><br><br>
                        <label for="Discription">Description:</label><br>
                        <textarea name="Discription" id="Discription" rows="4" cols="50"></textarea><br><br>
                        <label for="Datend">Due Date:</label>
                        <input type="date" name="Datend" id="Datend"><br><br>
                        <div class="controlbutn">
                            <input type="submit" value="Add Task" name="btnsave">
                        </div>
                    </form>
                    <?php
                        if (isset($_POST['btnsave'])) {
                            $adminID = $adminId;
                            $priorityID = $_POST['priorityID'];
                            $Datetask = $_POST['Datetask'];
                            $Task_subject = $_POST['Task_subject'];
                            $Discription = $_POST['Discription'];
                            $Datend = $_POST['Datend'];
                            $done = 0;
                            
                            $sql = "INSERT INTO tbltaskadmin (adminID, priorityID, Datetask, Task_subject, Discription, Datend, done)
                                    VALUES (:adminID, :priorityID, :Datetask, :Task_subject, :Discription, :Datend, :done)";
                            $stmt = $con->prepare($sql);
                            
                            // Bind parameters
                            $stmt->bindParam(':adminID', $adminID, PDO::PARAM_INT);
                            $stmt->bindParam(':priorityID', $priorityID, PDO::PARAM_INT);
                            $stmt->bindParam(':Datetask', $Datetask, PDO::PARAM_STR);
                            $stmt->bindParam(':Task_subject', $Task_subject, PDO::PARAM_STR);
                            $stmt->bindParam(':Discription', $Discription, PDO::PARAM_STR);
                            $stmt->bindParam(':Datend', $Datend, PDO::PARAM_STR);
                            $stmt->bindParam(':done', $done, PDO::PARAM_INT);
                            
                            // Execute the query
                            if ($stmt->execute()) {
                                // Data insertion was successful
                                echo "Task added successfully.";
                            } else {
                                // Data insertion failed
                                echo "Error: Task insertion failed.";
                            }
                        }
                        ?>

                </div>
                <?php
                }else{
                    echo '<script> location.href="index.php" </script>';
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageTask.js"></script>
    <script src="js/sidebar.js"></script>
</body>