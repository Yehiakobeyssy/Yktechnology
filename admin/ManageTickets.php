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
    <link rel="stylesheet" href="css/ManageTickets.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1>Manage Tickets</h1>
            </div>
            <?php
                $do=(isset($_GET['do']))?$_GET['do']:'manage';

                if($do=='manage'){?>
                    <div class="containertable">
                        <div class="search-container">
                            <input type="text" class="search-input" placeholder="Search ..." id="txtsearch">
                        </div>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <th>Client Name</th>
                                    <th>Section</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Last Update</th>
                                </thead>
                                <tbody class="bodyticket">
                                </tbody>
                            </table>
                        </div>
                        <div class="conclujen">
                            <label for="">Total tickets : <span id="countticket"></span></label>
                        </div>
                    </div>
                <?php
                }elseif($do=='view'){
                    $ticketID =(isset($_GET['id']))?$_GET['id']:0;

                ?>
                <div class="conatiner_Ticket">
                    <div class="tilte">
                        <h2> Deitail Ticket</h2>
                        <form action="" method="post">
                            <button type="submit" name="btncloseticket" class="btn btn-danger">Close Ticket</button>
                        </form>
                        <?php
                            if(isset($_POST['btncloseticket'])){
                                $sql=$con->prepare('UPDATE tblticket SET ticketStatus = 4 WHERE ticketID=?');
                                $sql->execute(array($ticketID));
                            }
                        ?>
                    </div>
                    <?php
                        $sql=$con->prepare('SELECT ticketStatus FROM  tblticket WHERE ticketID=? ');
                        $sql->execute(array($ticketID));
                        $result=$sql->fetch();
                        if($result['ticketStatus']==4){
                            echo '
                                <div class="alert alert-danger" role="alert">
                                    This Ticked is closed Please open a new Ticket 
                                </div>
                            ';
                        }
                    ?>
                    <div  class="alert alert-info addnewcoment">
                        <h4><i class="fa-solid fa-pen"></i> Add a Reply</h4>
                        <span id="open"><i class="fa-solid fa-plus"></i></span>
                    </div>
                    <div class="newreplay">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="deitailticket">
                                <div class="messageinfo">
                                    <label for="">Message text</label>
                                    <textarea name="Message" id=""  rows="7" required></textarea>
                                </div>
                            </div>
                            <label for="" id="titleAtt">Attachments</label>
                            <div class="attachments">
                                <input type="file" accept=".jpg, .jpeg, .png, .gif" name="attachment"/>
                            </div>
                            <p id="allowfiles">Allowed attachment file extensions: .jpg, .gif, .jpeg, .png</p>
                            <div class="controlbtn">
                                <button type="reset" class="btn btn-danger">Cancel</button>
                                <button type="submit" class="btn btn-success" name="btnSend">Send</button>
                            </div>
                        </form>
                        <?php
                            if(isset($_POST['btnSend']) && $result['ticketStatus']< 4){
                                if(!empty($_FILES['attachment']['name'])){
                                    $temp=explode(".",$_FILES['attachment']['name']);
                                    $newfilename=round(microtime(true)).'.'.end($temp);
                                    move_uploaded_file($_FILES['attachment']['tmp_name'],'../Documents/'.$newfilename);
                                }else{
                                    $newfilename='';
                                }
                                $TicketID       = $ticketID;
                                $Message        = $_POST['Message'];
                                $Client_company = 2;
                                $athment        = $newfilename;

                                $sql=$con->prepare('INSERT INTO tbldeiteilticket (TicketID,Message,Client_company,file)
                                                    VALUES (:TicketID,:Message,:Client_company,:file)');
                                $sql->execute(array(
                                    'TicketID'          => $TicketID ,
                                    'Message'           => $Message,
                                    'Client_company'    => $Client_company,
                                    'file'              => $athment
                                ));

                                $sql=$con->prepare('UPDATE tblticket SET ticketStatus = 3 WHERE ticketID=?');
                                $sql->execute(array($ticketID));
                            }
                        ?>
                    </div>
                    <div class="cards_respond">
                        <?php
                            $sql=$con->prepare('SELECT Client_company,Date,Message,file 
                                                FROM  tbldeiteilticket 
                                                WHERE ticketID=?
                                                ORDER BY Date DESC');
                            $sql->execute(array($ticketID));
                            $cards=$sql->fetchAll();
                            foreach($cards as $card){
                                if($card['Client_company'] ==1){
                                    $Operator = 'Client';
                                    $classname = 'alert alert-secondary';
                                }elseif($card['Client_company'] ==2){
                                    $Operator ='Operator';
                                    $classname = 'alert alert-primary';
                                }

                                echo '
                                <div class="card_respond">
                                    <div class="tiltlecard '.$classname.'">
                                        <i class="fa-solid fa-user"></i>
                                        <div class="info">
                                            <span id="oper">'.$Operator .'</span>
                                            <span>'.$card['Date'] .'</span>
                                        </div>
                                    </div>
                                    <div class="textrespont">
                                        <p>'.$card['Message'].'</p>
                                    </div>
                                    <div class="attathment">
                                        <span>'.$card['file'].'</span><a href="../Documents/'.$card['file'].'" download=""><i class="fa-solid fa-paperclip"></i></a>
                                    </div>
                                </div>
                                ';
                            }
                        ?>
                    </div>
                </div>
                <?php
                }else{
                    header('location:index.php');
                }
            ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageTickets.js"></script>
    <script src="js/sidebar.js"></script>
</body>