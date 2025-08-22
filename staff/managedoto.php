<?php
    session_start();

    if(!isset($_COOKIE['staff'])){
        if(!isset($_SESSION['staff'])){
            header('location:index.php');
        }
    }
    $staff_Id= (isset($_COOKIE['staff']))?$_COOKIE['staff']:$_SESSION['staff'];

    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT Fname,MidelName,LName,accepted , block FROM tblstaff WHERE staffID  = ?');
    $sql->execute(array($staff_Id));
    $result= $sql->fetch();
    $accepted = $result['accepted'];
    $block = $result['block'];
    $staff_name = $result['Fname'].' '.$result['MidelName'].' '.$result['LName'];

    if($block == 1){
        header('location:index.php');
    }else{
        if($accepted == 0){
            header('location:index.php');
        }
    }

    $do=(isset($_GET['do']))?$_GET['do']:'manage';

?>
    <link rel="stylesheet" href="css/managedoto.css">
</head>
<body>
    <?php include 'include/headerstaff.php' ?>
    <main>
        <?php include 'include/aside.php' ?>
        <div class="project_container">
            <div class="title">
                <h3>Do-to List</h3>
                <button class="btn btn-success addnew">Add New</button>
            </div>
            <?php
                if($do=='manage'){?>
                <div class="manageDoto">
                    <div class="searchbox">
                        <input type="text" name="" id="searchBox" placeholder="Search...">
                    </div>
                    <table class="todo-table">
                        <thead>
                            <th>Done</th>
                            <th>Importance</th>
                            <th>Task</th>
                            <th>Discription</th>
                            <th>Date to Finish</th>
                        </thead>
                        <tbody  id="tblfetchdoto">
                        </tbody>
                    </table>
                </div>
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
                            $staff_Id = $staff_Id;
                            $priorityID = $_POST['priorityID'];
                            $Datetask = $_POST['Datetask'];
                            $Task_subject = $_POST['Task_subject'];
                            $Discription = $_POST['Discription'];
                            $Datend = $_POST['Datend'];
                            $done = 0; 
                            
                            $sql = "INSERT INTO  tbldoto (freelancer_ID, priority, Datetask, taskSubject, disktiption, DateEnd, done)
                                    VALUES (:freelancer_ID, :priority, :Datetask, :taskSubject, :disktiption, :DateEnd, :done)";
                            $stmt = $con->prepare($sql);
                            
                            // Bind parameters
                            $stmt->bindParam(':freelancer_ID', $staff_Id, PDO::PARAM_INT);
                            $stmt->bindParam(':priority', $priorityID, PDO::PARAM_INT);
                            $stmt->bindParam(':Datetask', $Datetask, PDO::PARAM_STR);
                            $stmt->bindParam(':taskSubject', $Task_subject, PDO::PARAM_STR);
                            $stmt->bindParam(':disktiption', $Discription, PDO::PARAM_STR);
                            $stmt->bindParam(':DateEnd', $Datend, PDO::PARAM_STR);
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
                    echo '<script> location.href="managedoto.php"</script>';
                }
            ?>
        </div>
    </main>
    <?php include '../common/jslinks.php' ?>
    <script src="js/managetodo.js"></script>
</body>