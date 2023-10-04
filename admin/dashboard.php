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
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Dashboard</h1>
            </div>
            <div class="card-grid">
                <div class="card card-primary">
                    <h2 class="card-title">Clients</h2>
                    <p class="card-count">General Count: 5</p>
                </div>

                <div class="card card-secondary">
                    <h2 class="card-title">Services</h2>
                    <p class="card-count">General Count: 0</p>
                    <div class="card-subtitles">
                        <div class="subtitle">
                            <h3>In Process</h3>
                            <p>2</p>
                        </div>
                        <div class="subtitle">
                            <h3>Expire Soon</h3>
                            <p>0</p>
                        </div>
                        <div class="subtitle">
                            <h3>Cancel</h3>
                            <p>0</p>
                        </div>
                    </div>
                </div>

                <div class="card card-primary">
                    <h2 class="card-title">Domains</h2>
                    <p class="card-count">General Count: 0</p>
                    <div class="card-subtitles">
                        <div class="subtitle">
                            <h3>Active</h3>
                            <p>0</p>
                        </div>
                        <div class="subtitle">
                            <h3>Expire Soon</h3>
                            <p>0</p>
                        </div>
                        <div class="subtitle">
                            <h3>Cancel</h3>
                            <p>0</p>
                        </div>
                    </div>
                </div>

                <div class="card card-secondary">
                    <h2 class="card-title">Invoices</h2>
                    <p class="card-count">General Count: 5</p>
                    <div class="card-subtitles">
                        <div class="subtitle">
                            <h3>Deo</h3>
                            <p>2</p>
                        </div>
                        <div class="subtitle">
                            <h3>Paid</h3>
                            <p>2</p>
                        </div>
                        <div class="subtitle">
                            <h3>Cancel</h3>
                            <p>1</p>
                        </div>
                    </div>
                </div>

                <div class="card card-primary">
                    <h2 class="card-title">Tickets</h2>
                    <p class="card-count">General Count: 0</p>
                    <div class="card-subtitles">
                        <div class="subtitle">
                            <h3>Open</h3>
                            <p>0</p>
                        </div>
                        <div class="subtitle">
                            <h3>Client Respond</h3>
                            <p>0</p>
                        </div>
                        <div class="subtitle">
                            <h3>Operator</h3>
                            <p>0</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-grids">
                <div class="card card-special">
                    <h2 class="card-title">Amount Invoices</h2>
                    <p class="card-count">1000</p>
                </div>

                <div class="card card-special">
                    <h2 class="card-title">Payments</h2>
                    <p class="card-count">500</p>
                </div>

                <div class="card card-special">
                    <h2 class="card-title">Expenses</h2>
                    <p class="card-count">300</p>
                </div>
            </div>
            <div class="table-container">
                <h2>Services</h2>
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Service</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Done</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Add your table rows here -->
                        <tr>
                            <td>Client 1</td>
                            <td>Service A</td>
                            <td>$100</td>
                            <td>In Progress</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <tr>
                            <td>Client 2</td>
                            <td>Service B</td>
                            <td>$200</td>
                            <td>Completed</td>
                            <td><input type="checkbox" checked></td>
                        </tr>
                        <tr>
                            <td>Client 3</td>
                            <td>Service C</td>
                            <td>$150</td>
                            <td>In Progress</td>
                            <td><input type="checkbox"></td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
            <div class="table-container">
                <h2>Tickets</h2>
                <table class="tickets-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Last Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Add your table rows here -->
                        <tr>
                            <td>Client 1</td>
                            <td>Support</td>
                            <td>Issue with product</td>
                            <td>Open</td>
                            <td>2023-10-10</td>
                        </tr>
                        <tr>
                            <td>Client 2</td>
                            <td>Sales</td>
                            <td>Order inquiry</td>
                            <td>Closed</td>
                            <td>2023-10-09</td>
                        </tr>
                        <tr>
                            <td>Client 3</td>
                            <td>Technical</td>
                            <td>Software bug</td>
                            <td>In Progress</td>
                            <td>2023-10-08</td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>
            <div class="container_div">
                <div class="left-div">
                    <h2>Domains End After 45 Days</h2>
                    <table class="domains-table">
                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Client</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Add your table rows here -->
                            <tr>
                                <td>Basic</td>
                                <td>Client A</td>
                                <td>$50</td>
                            </tr>
                            <tr>
                                <td>Premium</td>
                                <td>Client B</td>
                                <td>$100</td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
                <div class="right-div">
                    <h2>To-Do List</h2>
                    <table class="todo-table">
                        <thead>
                            <tr>
                                <th>Done</th>
                                <th>Task</th>
                                <th>Date to Finish</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Add your table rows here -->
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>Task 1</td>
                                <td>2023-11-15</td>
                            </tr>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>Task 2</td>
                                <td>2023-11-20</td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="container_expenis">
                <div class="left-div">
                    <h2>Expenses</h2>
                    <table class="expenses-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Add your table rows here -->
                            <tr>
                                <td>2023-11-01</td>
                                <td>Office Supplies</td>
                                <td>$100</td>
                            </tr>
                            <tr>
                                <td>2023-11-05</td>
                                <td>Utilities</td>
                                <td>$150</td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
                <div class="right-div">
                    <h2>Payments</h2>
                    <table class="payments-table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Method</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Add your table rows here -->
                            <tr>
                                <td>Client A</td>
                                <td>Credit Card</td>
                                <td>$200</td>
                            </tr>
                            <tr>
                                <td>Client B</td>
                                <td>PayPal</td>
                                <td>$150</td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/dashboard.js"></script>
    <script src="js/sidebar.js"></script>
</body>