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

    
?>
    <link rel="stylesheet" href="css/dashbord.css">
</head>
<body>
    <?php include 'include/headerstaff.php' ?>
    <main>
        <?php include 'include/aside.php' ?>
        <div class="dashbord_info">
            <div class="title_daschborad">
                <h3>Welcome <span> <?php echo $staff_name ?> </span>,</h3>
            </div>
            <div class="statistic">

            </div>
            <div class="work_section">  
                <div class="project_doto">
                    <div class="project">
                        <h4>Projects</h4>
                        <table>
                            <thead>
                                <th>Name</th>
                                <th>Client</th>
                                <th>Manager</th>
                                <th>Posstion</th>
                                <th>Tasks</th>
                                <th>Control</th>
                            </thead>
                            <tbody>
                                <?php 
                                    $sql = $con->prepare("
                                                            SELECT 
                                                                p.ProjectID,
                                                                p.project_Name AS Project_Name,
                                                                CONCAT(c.Client_FName, ' ', c.Client_LName) AS Client_Name,
                                                                CONCAT(a.admin_FName, ' ', a.admin_LName) AS Manager,
                                                                dp.Posstion AS Posstion,
                                                                COUNT(t.taskID) AS Tasks
                                                            FROM tbldevelopers_project dp
                                                            INNER JOIN tblprojects p ON dp.projectID = p.ProjectID
                                                            INNER JOIN tblclients c ON p.ClientID = c.ClientID
                                                            INNER JOIN tbladmin a ON p.Project_Manager = a.admin_ID
                                                            LEFT JOIN tbltask t 
                                                                ON t.ProjectID = p.ProjectID 
                                                                AND t.Assign_to = ?
                                                            WHERE dp.FreelancerID = ?
                                                            AND p.Status IN (1, 2)
                                                            GROUP BY p.ProjectID
                                                        ");

                                    $sql->execute([$staff_Id, $staff_Id]);

                                    $results = $sql->fetchAll();

                                    foreach($results as $project){
                                        echo '
                                            <tr>
                                                <td>'.$project['Project_Name'].'</td>
                                                <td>'.$project['Client_Name'].'</td>
                                                <td>'.$project['Manager'].'</td>
                                                <td>'.$project['Posstion'].'</td>
                                                <td>'.$project['Tasks'].'</td>
                                                <td>
                                                    <button class="btnviewproject" data-index="'.$project['ProjectID'].'">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.63435 3.67285C12.721 3.67285 14.6548 6.41283 15.3356 7.69377C15.5524 8.10165 15.5524 8.57739 15.3356 8.98527C14.6548 10.2662 12.721 13.0062 8.63435 13.0062C4.54768 13.0062 2.61386 10.2662 1.93309 8.98527C1.71631 8.57739 1.71631 8.10165 1.93309 7.69377C2.61386 6.41283 4.54768 3.67285 8.63435 3.67285ZM5.19191 5.99131C4.08507 6.72468 3.43876 7.7018 3.11047 8.31951C3.1068 8.3264 3.10529 8.33119 3.10464 8.33382C3.10397 8.33649 3.10384 8.33952 3.10384 8.33952C3.10384 8.33952 3.10397 8.34255 3.10464 8.34522C3.10529 8.34785 3.1068 8.35263 3.11047 8.35953C3.43876 8.97723 4.08507 9.95436 5.19191 10.6877C4.73493 10.0191 4.46768 9.21052 4.46768 8.33952C4.46768 7.46852 4.73493 6.65994 5.19191 5.99131ZM12.0768 10.6877C13.1836 9.95435 13.8299 8.97723 14.1582 8.35953C14.1619 8.35263 14.1634 8.34785 14.1641 8.34522C14.1645 8.34349 14.1648 8.34104 14.1648 8.34104L14.1648 8.33952L14.1646 8.33658L14.1641 8.33382C14.1634 8.33119 14.1619 8.3264 14.1582 8.31951C13.8299 7.7018 13.1836 6.72469 12.0768 5.99132C12.5338 6.65995 12.801 7.46852 12.801 8.33952C12.801 9.21051 12.5338 10.0191 12.0768 10.6877ZM5.80101 8.33952C5.80101 6.77471 7.06954 5.50618 8.63435 5.50618C10.1992 5.50618 11.4677 6.77471 11.4677 8.33952C11.4677 9.90432 10.1992 11.1729 8.63435 11.1729C7.06954 11.1729 5.80101 9.90432 5.80101 8.33952Z" fill="#7F8291"/>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        ';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="do_to">
                        <h4>Do to</h4>
                    </div>
                </div>
                <div class="tasks">
                    <h4>Task</h4>
                    <table>
                        <thead>
                            <tr>
                                <th>Assignment</th>
                                <th>Title</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Control</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $sql = $con->prepare("
                                                        SELECT 
                                                            CONCAT(a.admin_FName, ' ', a.admin_LName) AS Assignment,
                                                            t.taskTitle AS Title,
                                                            t.DueDate AS DueDate,
                                                            s.Status_name AS Status,
                                                            t.Status AS StatusID
                                                        FROM tbltask t
                                                        INNER JOIN tbladmin a ON t.assignFrom = a.admin_ID
                                                        INNER JOIN tblstatus_task_freelancer s ON t.Status = s.StatusID
                                                        WHERE t.Assign_to = ?
                                                        ORDER BY DueDate DESC
                                                    ");
                                $sql->execute([$staff_Id]);
                                $tasks = $sql->fetchAll();


                                foreach($tasks as $task): 
                            
                                    // Determine Bootstrap alert class
                                    $statusClass = '';
                                    switch($task['StatusID']) {
                                        case 1: $statusClass = 'alert alert-primary'; break;
                                        case 2: $statusClass = 'alert alert-info'; break;
                                        case 3: $statusClass = 'alert alert-warning'; break;
                                        case 4: $statusClass = 'alert alert-success'; break;
                                        case 5: $statusClass = 'alert alert-danger'; break;
                                        default: $statusClass = 'alert alert-secondary';
                                    }
                            ?>
                            <tr>
                                <td><?php echo $task['Assignment']; ?></td>
                                <td><?php echo $task['Title']; ?></td>
                                <td><?php echo $task['DueDate']; ?></td>
                                <td><label class="<?php echo $statusClass; ?>"><?php echo $task['Status']; ?></label></td>
                                <td>
                                    
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </main>
    <?php include '../common/jslinks.php' ?>
    <script src="js/dashbord.js"></script>
</body>