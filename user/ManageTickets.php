<?php
    session_start();
    if(!isset($_COOKIE['user'])){
        if(!isset($_SESSION['user'])){
            header('location:index.php');
        }
    }
    $clientId= (isset($_COOKIE['user']))?$_COOKIE['user']:$_SESSION['user'];
    include '../settings/connect.php';
    include '../common/function.php';
    include '../common/head.php';

    $sql=$con->prepare('SELECT Client_FName,Client_LName,client_active,Client_email FROM  tblclients WHERE ClientID=?');
    $sql->execute(array($clientId));
    $clientinfo = $sql->fetch();
    $clientName = $clientinfo['Client_FName'].' '. $clientinfo['Client_LName'];

    if($clientinfo['client_active'] == 0){
        setcookie("user","",time()-3600);
        unset($_SESSION['user']);
        echo '<script> location.href="index.php" </script>';
    }
?>
    <link rel="stylesheet" href="css/ManageTickets.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>
    <?php include 'include/navbar.php' ?>
    <div class="containerticket">
        <div class="title">
            <h1>Tickets</h1>
            <div class="addnewticket">
                
            </div>
        </div>  
        <?php
            $do=(isset($_GET['do']))?$_GET['do']:'manage';
            if($do=='manage'){?>
            <div class="containertable">
                <div class="searchsection">
                    <div class="searchinput">
                        <label for="">Search :</label>
                        <input type="text" name="" id="txtsearch">
                    </div>
                    <div class="addticket">
                        <a href="ManageTickets.php?do=add" class="btn btn-success">New Ticket</a>
                    </div>
                </div>
                <div class="table">
                    <table>
                        <thead>
                            <td>Section</td>
                            <td>Subject</td>
                            <td>Status</td>
                            <td>Last Update</td>
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
            }elseif($do=='add'){?>
                <div class="newticket">
                    <div class="titleadd">
                        <h5>Send a new Ticket</h5>
                    </div>
                    <div class="userinfo">
                        <div class="username">
                            <label for="">Client Name:</label>
                            <input type="text" name="" id="" value="<?php echo $clientName ?>" disabled>
                        </div>
                        <div class="email">
                            <label for="">Client E-mail:</label>
                            <input type="text" name="" id="" value="<?php echo $clientinfo['Client_email'] ?>" disabled>
                        </div>
                    </div>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="maininfo">
                            <div class="subjectticket">
                                <label for="">Subject:</label>
                                <input type="text" name="ticketSubject" id="" required>
                            </div>
                            <div class="reletion_ticket">
                                <table>
                                    <tr>
                                        <td><label for="">Section</label></td>
                                        <td><label for="">Ticket related service</label></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select name="ticketSection" id="" required>
                                                <?php
                                                    $sql=$con->prepare('SELECT TypeTicketID,TypeTicket FROM  tbltypeoftickets');
                                                    $sql->execute();
                                                    $types =$sql->fetchAll();
                                                    foreach($types as $type){
                                                        echo '<option value="'.$type['TypeTicketID'].'">'.$type['TypeTicket'].'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="TicketBelong" id="">
                                                <option value="0">Without</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="deitailticket">
                            <div class="messageinfo">
                                <label for="">Message text</label>
                                <textarea name="Message" id=""  rows="15" required></textarea>
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
                        if(isset($_POST['btnSend'])){
                            if(!empty($_FILES['attachment']['name'])){
                                $temp=explode(".",$_FILES['attachment']['name']);
                                $newfilename=round(microtime(true)).'.'.end($temp);
                                move_uploaded_file($_FILES['attachment']['tmp_name'],'../Documents/'.$newfilename);
                            }else{
                                $newfilename='';
                            }
                            //add to ticket 
                            $ticketDate     = date('Y-m-d');
                            $ClientID       = $clientId;
                            $ticketSection  = $_POST['ticketSection'];
                            $TicketBelong   = $_POST['TicketBelong'];
                            $ticketSubject  = $_POST['ticketSubject'];
                            $ticketStatus   = 1;

                            $sql=$con->prepare('INSERT INTO tblticket (ticketDate,ClientID,ticketSection,TicketBelong,ticketSubject,ticketStatus)
                                                VALUES (:ticketDate,:ClientID,:ticketSection,:TicketBelong,:ticketSubject,:ticketStatus)');
                            $sql->execute(array(
                                'ticketDate'    => $ticketDate,
                                'ClientID'      => $ClientID,
                                'ticketSection' => $ticketSection,
                                'TicketBelong'  => $TicketBelong,
                                'ticketSubject' => $ticketSubject,
                                'ticketStatus'  => $ticketStatus
                            ));

                            //Insert Detail
                            $TicketID       = get_last_ID('ticketID','tblticket');
                            $Message        = $_POST['Message'];
                            $Client_company = 1;
                            $athment        = $newfilename;

                            $sql=$con->prepare('INSERT INTO tbldeiteilticket (TicketID,Message,Client_company,file)
                                                VALUES (:TicketID,:Message,:Client_company,:file)');
                            $sql->execute(array(
                                'TicketID'          => $TicketID ,
                                'Message'           => $Message,
                                'Client_company'    => $Client_company,
                                'file'              => $athment
                            ));

                            require_once '../mail.php';
                            $mail->setFrom($applicationemail, 'YK technology');
                            $mail->addAddress($clientinfo['Client_email']);
                            $mail->Subject = 'Ticket Receipt Confirmation';
                            $mail->Body    = '
                            Dear '.$clientName.',<br>
                            We hope this message finds you well.<br>
                            We want to confirm that we have received your recent ticket submission. Your concern is important to us, and we appreciate your patience as we work to address it.<br>
                            Our dedicated team is currently reviewing your ticket, and we are committed to resolving your issue as swiftly as possible. Rest assured that your request is in capable hands, and we will do our best to provide you with a timely and satisfactory resolution.<br>
                            In the meantime, if you have any additional information or details that could assist us in expediting the process, please dont hesitate to reply to this email or include them in a follow-up message.<br>
                            Thank you for choosing our services, and we appreciate your understanding as we work to meet your needs. We will keep you updated on the progress of your ticket and notify you as soon as it has been successfully resolved.<br>
                            If you have any urgent concerns or require immediate assistance, please feel free to contact our customer support email at info@yktechnology.es.<br>
                            Once again, thank you for reaching out to us. We value your business and look forward to assisting you promptly.<br>
                            Best regards,<br>
                            YK-technology <br>
                            Customer Support Team
                            ';
                            $mail->send();
                                
                            echo '<script> location.href="ManageTickets.php"</script>';                            
                        }
                    ?>
                </div>
            <?php
            }else{  
                header('location:index.php');
            }
        ?>
    </div>
    <?php include '../common/jslinks.php' ?>
    <script src="js/ManageTickets.js"></script>
    <script src="js/navbar.js"></script>
</body>