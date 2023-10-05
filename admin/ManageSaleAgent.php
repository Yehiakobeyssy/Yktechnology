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

    if(isset($_POST['btnpaynow'])){
        $Account_Date = date('Y-m-d');
        $agent        = $_POST['agent'];
        $Discription  = $_POST['Discription'];
        $Depit        = 0;
        $Crieted      = $_POST['amount'];
        
        $sql=$con->prepare('INSERT INTO tblaccountstatment_saleperson (Account_Date,SaleManID,Discription,Depit,Crieted) 
                            VALUES (:Account_Date,:SaleManID,:Discription,:Depit,:Crieted)');
        $sql->execute(array(
            'Account_Date'  =>$Account_Date,
            'SaleManID'     =>$agent,
            'Discription'   =>$Discription,
            'Depit'         =>$Depit ,
            'Crieted'       =>$Crieted
        ));

        $sql=$con->prepare('SELECT Sale_FName,Sale_LName FROM tblsalesperson WHERE SalePersonID =?');
        $sql->execute(array($agent));
        $result=$sql->fetch();
        $saleMan_name = $result['Sale_FName'].' '.$result['Sale_LName'];

        $ExpensisDate       =date('Y-m-d');
        $ExpenisType        =3;
        $Discription        ='Payment Commition To the agent : ' . $saleMan_name;
        $Expensis_Amount    =$Crieted;
        $Expensis_Note      ='';
        $attached           ='';

        $sql=$con->prepare('INSERT INTO tblexpensis (ExpensisDate,ExpenisType,Discription,Expensis_Amount,Expensis_Note,attached)
                            VALUES (:ExpensisDate,:ExpenisType,:Discription,:Expensis_Amount,:Expensis_Note,:attached)');
        $sql->execute(array(
            'ExpensisDate'      =>$ExpensisDate,
            'ExpenisType'       =>$ExpenisType,
            'Discription'       =>$Discription,
            'Expensis_Amount'   =>$Expensis_Amount,
            'Expensis_Note'     =>$Expensis_Note,
            'attached'          =>$attached
        ));
    }

?>
    <link rel="stylesheet" href="css/ManageSaleAgent.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <?php include 'include/navbar.php'?>
    <div class="mainform">
        <?php include 'include/sidebar.php'?>
        <div class="contain">
            <div class="title">
                <h1> <i class="fa-solid fa-user-secret"></i> Sale Agent</h1>
                <a href="ManageSaleAgent.php?do=add" class="btn btn-primary btnnewAgent">New Agent</a>
            </div>
            <?php
            $do=(isset($_GET['do']))?$_GET['do']:'manage';
            if($do=='manage'){?>
            <div class="manageAgent">
                <h1>Agent Information</h1>
                <div class="search-box">
                    <input type="text" id="txtsearch" placeholder="Search...">
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Agent Name</th>
                            <th>Email</th>
                            <th>Country</th>
                            <th>Promo Code</th>
                            <th>Commission</th>
                            <th>Clients</th>
                            <th>Balance</th>
                            <th>Control</th>
                        </tr>
                    </thead>
                    <tbody class="bodyticket">
                    </tbody>
                </table>
            </div>
            <?php
            }elseif($do=='add'){?>
            <div class="newagent">
                <h2>New Agent Registration</h2>
                <form action="" method="post">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required>

                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="country">Country:</label>
                    <select id="country" name="country">
                        <?php
                            $sql = $con->prepare('SELECT CountryID, CountryName FROM tblcountrys');
                            $sql->execute();
                            $rows=$sql->fetchAll();
                            foreach($rows as $row){
                                echo '<option value="' . $row['CountryID'] . '">' . $row['CountryName'] . '</option>';
                            }
                        ?>
                    </select>

                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" >

                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" >

                    <label for="commission_rate">Commission Rate (%):</label>
                    <input type="number" id="commission_rate" name="commission_rate" step="0.01" min="0" max="100" required>

                    <label for="payment_type">Payment Type:</label>
                    <select id="payment_type" name="payment_type">
                        <option value="0">[select One]</option>
                        <?php
                            $sql = $con->prepare('SELECT paymentmethodD, methot FROM tblpayment_method WHERE paymentmethodD != 2');
                            $sql->execute();
                            $rows=$sql->fetchAll();
                            foreach($rows as $row){
                                echo '<option value="' . $row['paymentmethodD'] . '">' . $row['methot'] . '</option>';
                            }
                        ?>
                    </select>
                    <label for="note">Note:</label>
                    <textarea id="note" name="note" rows="4"></textarea>
                    <button type="submit" name="btnAddnewagent">Submit</button>
                    
                </form>
                <?php
                    if(isset($_POST['btnAddnewagent'])){
                        $Sale_FName     =$_POST['first_name'];
                        $Sale_LName     =$_POST['last_name'];
                        $email_Sale     =$_POST['email'];
                        $password_sale  =generateRandomPassword(8);
                        $Country        =$_POST['country'];
                        $City           =$_POST['city'];
                        $Addresse       =$_POST['address'];

                        $firstName = $Sale_FName;
                        $lastName = $Sale_LName;
                        $promoCodePrefix = substr($firstName, 0, 2) . substr($lastName, -2);
                        $randomNumber = sprintf("%05d", rand(0, 99999));
                        $promoCode = $promoCodePrefix . $randomNumber;

                        $PromoCode      =$promoCode;
                        $ComitionRate   =$_POST['commission_rate'];
                        $PaymentType    =$_POST['payment_type'];
                        $Note           =$_POST['note'];
                        $saleActive     =1;

                        $sql=$con->prepare('INSERT INTO tblsalesperson (Sale_FName,Sale_LName,email_Sale,password_sale,Country,City,Addresse,PromoCode,ComitionRate,PaymentType,Note,saleActive) 
                                            VALUES (:Sale_FName,:Sale_LName,:email_Sale,:password_sale,:Country,:City,:Addresse,:PromoCode,:ComitionRate,:PaymentType,:Note,:saleActive)');
                        $sql->execute(array(
                            'Sale_FName'    =>$Sale_FName,
                            'Sale_LName'    =>$Sale_LName,
                            'email_Sale'    =>$email_Sale,
                            'password_sale' =>sha1($password_sale),
                            'Country'       =>$Country,
                            'City'          =>$City,
                            'Addresse'      =>$Addresse,
                            'PromoCode'     =>$PromoCode,
                            'ComitionRate'  =>$ComitionRate,
                            'PaymentType'   =>$PaymentType,
                            'Note'          =>$Note ,
                            'saleActive'    =>$saleActive
                        ));

                        require_once '../mail.php';
                        $mail->setFrom($applicationemail, 'YK technology Sale Agent');
                        $mail->addAddress($email_Sale);
                        $mail->Subject = 'Welcome to YK technology - Your Agent Account Details';
                        $mail->Body    = '
                                            Dear '.$Sale_FName.' '.$Sale_LName.'<br>
                                            We are pleased to inform you that you have been accepted as a member of our sales team. 
                                            Welcome aboard! We are excited to have you as part of our team.<br>
                                            Here are your important details:<br>
                                            Username: <strong> '.$email_Sale.' </strong><br>
                                            Password: <strong>'.$password_sale.' </strong><br>
                                            Commission Rate: <strong>'.$ComitionRate.' % </strong><br>
                                            Promo Code: <strong>'.$PromoCode.'</strong><br>
                                            Please keep this information confidential and secure. It is essential for accessing our systems and ensuring a smooth working relationship.<br>
                                            Additionally, as an agent, you will play a crucial role in assisting our clients with their transactions. 
                                            You will need to provide them with your unique promo code to ensure that we can track and process their payments 
                                            correctly.<br>
                                            Here is the code you should share with your clients for payments:<br>
                                            <h1>'.$PromoCode.'</h1>
                                            Please instruct your clients to include this code when making payments to ensure that their 
                                            transactions are associated with your account. This will help us accurately calculate your 
                                            commission. <br>
                                            If you have any questions or need further assistance, please dont hesitate to reach out to our 
                                            support team at info@yktechnology.com . We are here to help you succeed and provide the best service to 
                                            our clients. <br>
                                            Once again, welcome to our team, and we look forward to a successful partnership!<br>
                                            Best regards,<br>
                                            YK-technology
                                        ';
                        $mail->send();
                    }
                ?>
            </div>
            <?php
            }elseif($do=='Edit'){
                $agentID= (isset($_GET['AID']))?$_GET['AID']:0;
                $sql=$con->prepare('SELECT * FROM  tblsalesperson WHERE  SalePersonID = ?');
                $sql->execute(array($agentID));
                $result=$sql->fetch();
            ?>
            <div class="newagent">
                <h2>Edit Agent Information</h2>
                <form action="" method="post">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" required  value="<?php echo $result['Sale_FName'] ?>">

                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required value="<?php echo $result['Sale_LName'] ?>">

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required value="<?php echo $result['email_Sale'] ?>">

                    <label for="country">Country:</label>
                    <select id="country" name="country" >
                        <?php
                            $sql = $con->prepare('SELECT CountryID, CountryName FROM tblcountrys');
                            $sql->execute();
                            $rows=$sql->fetchAll();
                            foreach($rows as $row){
                                if($result['Country'] == $row['CountryID']){
                                    echo '<option value="' . $row['CountryID'] . '" selected>' . $row['CountryName'] . '</option>';
                                }else{
                                    echo '<option value="' . $row['CountryID'] . '">' . $row['CountryName'] . '</option>';
                                }
                                
                            }
                        ?>
                    </select>

                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" value="<?php echo $result['City'] ?>" >

                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address"  value="<?php echo $result['Addresse'] ?>">

                    <label for="commission_rate">Commission Rate (%):</label>
                    <input type="number" id="commission_rate" name="commission_rate" step="0.01" min="0" max="100" required value="<?php echo $result['ComitionRate'] ?>">

                    <label for="payment_type">Payment Type:</label>
                    <select id="payment_type" name="payment_type">
                        <option value="0">[select One]</option>
                        <?php
                            $sql = $con->prepare('SELECT paymentmethodD, methot FROM tblpayment_method WHERE paymentmethodD != 2');
                            $sql->execute();
                            $rows=$sql->fetchAll();
                            foreach($rows as $row){
                                if($result['PaymentType'] == $row['paymentmethodD']){
                                    echo '<option value="' . $row['paymentmethodD'] . '" selected>' . $row['methot'] . '</option>';
                                }else{
                                    echo '<option value="' . $row['paymentmethodD'] . '">' . $row['methot'] . '</option>';
                                }
                            }
                        ?>
                    </select>
                    <label for="note">Note:</label>
                    <textarea id="note" name="note" rows="4"><?php echo $result['Note'] ?></textarea>
                    <button type="submit" name="btnEdit">Edit</button>
                    
                </form>
                <?php
                    if(isset($_POST['btnEdit'])){
                        $Sale_FName     =$_POST['first_name'];
                        $Sale_LName     =$_POST['last_name'];
                        $email_Sale     =$_POST['email'];
                        $Country        =$_POST['country'];
                        $City           =$_POST['city'];
                        $Addresse       =$_POST['address'];
                        $ComitionRate   =$_POST['commission_rate'];
                        $PaymentType    =$_POST['payment_type'];
                        $Note           =$_POST['note'];

                        $sql=$con->prepare('UPDATE  tblsalesperson 
                                            SET     Sale_FName          = :Sale_FName,
                                                    Sale_LName          = :Sale_LName,
                                                    email_Sale          = :email_Sale,
                                                    Country             = :Country,
                                                    City                = :City,
                                                    Addresse            = :Addresse,
                                                    ComitionRate        = :ComitionRate ,
                                                    PaymentType         = :PaymentType,
                                                    Note                = :Note
                                            WHERE   SalePersonID        = :SalePersonID ');
                        $sql->execute(array(
                            'Sale_FName'    =>$Sale_FName,
                            'Sale_LName'    =>$Sale_LName,
                            'email_Sale'    =>$email_Sale,
                            'Country'       =>$Country,
                            'City'          =>$City,
                            'Addresse'      =>$Addresse,
                            'ComitionRate'  =>$ComitionRate,
                            'PaymentType'   =>$PaymentType,
                            'Note'          =>$Note ,
                            'SalePersonID'  =>$agentID
                        ));
                        echo '<script> location.href="ManageSaleAgent.php" </script>';
                    }
                ?>
            </div>
            <?php
            }elseif($do=='Acc'){
                $agentID= (isset($_GET['AID']))?$_GET['AID']:0;
                $saleManID = $agentID; // Replace with the actual SaleManID

                $sql=$con->prepare('SELECT Sale_FName,Sale_LName FROM tblsalesperson WHERE SalePersonID = ?');
                $sql->execute(array($agentID));
                $resultA=$sql->fetch();
                $agentName =$resultA['Sale_FName'].' '.$resultA['Sale_LName'];
                // Retrieve data from the database
                $sql = $con->prepare('SELECT AccountID, Account_Date, Discription, Depit, Crieted FROM tblaccountstatment_saleperson WHERE SaleManID = ?');
                $sql->execute([$saleManID]);
                $accountStatements = $sql->fetchAll(PDO::FETCH_ASSOC);

                // Calculate the total Depit and total Crieted
                $totalDepit = 0;
                $totalCrieted = 0;

                foreach ($accountStatements as $row) {
                    $totalDepit += $row['Depit'];
                    $totalCrieted += $row['Crieted'];
                }

                // Calculate the Balance
                $balance = $totalDepit - $totalCrieted;
            ?>
            <div class="accounttable">
                <div class="information">
                    <h1>Account Statment : </h1>
                    <table id="acountagentinfo">
                        <tr>
                            <td><label for="">Agent Name : </label></td>
                            <td><span><?=  $agentName ?></span></td>
                        </tr>
                        <tr>
                            <td><label for="">Balance: </label></td>
                            <td><span>$<?= number_format($balance, 2) ?></span></td>
                        </tr>
                    </table>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Account Date</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        foreach ($accountStatements as $row): ?>
                        <tr>
                            <td><?= $row['Account_Date'] ?></td>
                            <td><?= $row['Discription'] ?></td>
                            <td><?= $row['Depit'] > 0 ? '$' . number_format($row['Depit'], 2) : '' ?></td>
                            <td><?= $row['Crieted'] > 0 ? '$' . number_format($row['Crieted'], 2) : '' ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2"><strong>Total</strong></td>
                            <td><strong>$<?= number_format($totalDepit, 2) ?></strong></td>
                            <td><strong>$<?= number_format($totalCrieted, 2) ?></strong></td>
                        </tr>
                        <tr>
                            <td colspan="3"><strong>Balance</strong></td>
                            <td><strong>$<?= number_format($balance, 2) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                <div class="btnPay">
                    <button id="opendialog">Pay</button>
                </div>
            </div>
            <div class="popupmesseage">
                <div class="containermessage">
                    <div class="closemessage">+</div>
                    <h2> Make a Payment</h2>
                    <form action="" method="post">
                        <input type="text" name="agent" id="" value="<?php echo $agentID ?>" hidden>
                        <label for="">Discription</label>
                        <input type="text" name="Discription" id="" value="payment from the account">
                        <label for="">Amount</label>
                        <input type="number" name="amount" id="" step="0.01" value="<?php echo $balance?>">
                        <div class="btnpayconsole">
                            <button type="submit" name="btnpaynow">Pay Now</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php
            }elseif($do=='block'){
                $agentID= (isset($_GET['AID']))?$_GET['AID']:0;

                $sql=$con->prepare('SELECT saleActive FROM tblsalesperson WHERE SalePersonID = ?');
                $sql->execute(array($agentID));
                $result=$sql->fetch();

                if($result['saleActive'] == 1){
                    $sql=$con->prepare('UPDATE  tblsalesperson SET saleActive = 0 WHERE SalePersonID =?');
                    $sql->execute(array($agentID));
                    echo '<script> location.href="ManageSaleAgent.php" </script>';
                }elseif($result['saleActive'] == 0){
                    $sql=$con->prepare('UPDATE  tblsalesperson SET saleActive = 1 WHERE SalePersonID =?');
                    $sql->execute(array($agentID));
                    echo '<script> location.href="ManageSaleAgent.php" </script>';
                }else{
                    echo '<script> location.href="index.php" </script>';
                }

            }else{
                echo '<script> location.href="index.php" </script>';
            }
        ?>
        </div>
    </div>
    <?php include '../common/jslinks.php'?>
    <script src="js/ManageSaleAgent.js"></script>
    <script src="js/sidebar.js"></script>
</body>