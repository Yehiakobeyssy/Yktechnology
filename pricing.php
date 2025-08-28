<?php
    include 'settings/connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Information - Kawnex</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link href="common/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="common/fcss/all.min.css">
    <link rel="stylesheet" href="common/fcss/fontawesome.min.css">
    <link rel="stylesheet" href="index.css">
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
    <script src="https://translate.google.com/translate_a/element.js?cb=loadGoogletranslate"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        .header {
            background-color: #007BFF;
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        .companyinfo {
            display: flex;
            align-items: center;
        }

        .companyinfo img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .companyinfo h3 {
            font-size: 24px;
            margin: 0;
        }

        .navbar {
            display: flex;
            list-style: none;
            padding: 0;
            justify-content: center;
        }

        .navbar li {
            margin: 0 15px;
        }

        .navbar a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }

        .navbar a:hover {
            color: #0056b3;
        }

        /* Pricing Page Styles */
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 28px;
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #007BFF;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .payment-methods {
            margin-top: 20px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        .return-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            display: block;
            width: 100px;
            text-align: center;
            margin: 20px auto;
        }

        .return-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <div class="companyinfo">
            <img src="images/logo.png" alt="">
            <h3>Kawnex</h3>
        </div>
        <input type="checkbox" id="menu-bar">
        <label for="menu-bar"><i class="fa-solid fa-list-ul"></i></label>
        <nav class="navbar">
            <ul>
                <li> <a href=""> <i class="fa-solid fa-money-bill-1"></i> Pricing</a></li>
                <li> <a href="index.php"> <i class="fa-solid fa-code"></i> Services</a></li>
                <li><a href="index.php"> <i class="fa-solid fa-briefcase"></i> How we Work</a></li>
                <li><a href="index.php"> <i class="fa-solid fa-phone"></i> Contact Us</a></li>
                <li><a href="user/"> <i class="fa-solid fa-user-tie"></i> Login</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <div class="pricing-header">
            <h1>Pricing Information</h1>
            <p>Our commitment to transparency means that you can find the price of each service in its respective section. Please be aware that all prices listed below exclude Value Added Tax (VAT).</p>
        </div>
        <table class="pricing-table">
        <tr>
            <th>Invoice Amount</th>
            <th>Number of Payments</th>
            <th>Return Policy</th>
        </tr>
        <tr>
            <td>Less than $150</td>
            <td>1 payment</td>
            <td>No Returns</td>
        </tr>
        <tr>
            <td>$150 - $500</td>
            <td>2 payments</td>
            <td>No Returns</td>
        </tr>
        <tr>
            <td>$500 - $1000</td>
            <td>3 payments</td>
            <td>No Returns</td>
        </tr>
        <tr>
            <td>$1000 - $3000</td>
            <td>4 payments</td>
            <td>No Returns</td>
        </tr>
        <tr>
            <td>$3000 - $6000</td>
            <td>5 payments</td>
            <td>No Returns</td>
        </tr>
        <tr>
            <td>$6000 - $10000</td>
            <td>6 payments</td>
            <td>No Returns</td>
        </tr>
        <tr>
            <td>$10000 - $15000</td>
            <td>7 payments</td>
            <td>No Returns</td>
        </tr>
        <tr>
            <td>$15000 - $20000</td>
            <td>8 payments</td>
            <td>No Returns</td>
        </tr>
        <tr>
            <td>Over $20000</td>
            <td>10 payments</td>
            <td>No Returns</td>
        </tr>
    </table>
        <div class="payment-methods">
            <h2>Accepted Payment Methods</h2>
            <p>We offer various secure payment options:</p>
            <ul>
                <?php
                    $sql=$con->prepare('SELECT methot,note FROM  tblpayment_method WHERE paymentmethodD !=3 AND method_active=1');
                    $sql->execute();
                    $rows=$sql->fetchAll();
                    $i=1; 
                    foreach($rows as $row){
                        echo '
                            <li><strong>'.$i.'. '.$row['methot'].':</strong> <br>'.nl2br($row['note']).' <br></li>
                        ';  
                        $i++;
                    }
                ?>
            </ul>
        </div>
        <p>Our goal is to make our services accessible to all clients, and these payment options are designed to cater to your financial needs. Please be aware that once a payment is made, it is non-refundable, and we have a strict no-return policy. If you have any inquiries or need further clarification regarding our pricing, payment options, or our refund policy, please do not hesitate to contact us for assistance.</p>
        <a class="return-button" href="index.php">Return</a>
    </div>
    <?php include 'common/jslinks.php'?>
</body>
</html>
