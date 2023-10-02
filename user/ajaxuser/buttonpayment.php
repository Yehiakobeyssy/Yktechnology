<?php
    include '../../settings/connect.php';

    $url = "http://localhost/work/YKtechnology/user/viewinvoice.php?id=1";
    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $params);
    if (isset($params['id'])) {
        $invoiceid = $params['id'];
    }else{
        $invoiceid = 0;
    }
    
    $paymentmethod = isset($_GET['id']) ? $_GET['id'] : 0;
    $amount = isset($_GET['amount'])?$_GET['amount']:0;

    echo '<input type="text" name="" id="txtamont" value='.$amount.' hidden >';
    if ($paymentmethod === 0) {
        $sql = $con->prepare('SELECT methot,paymentmethodD FROM tblpayment_method LIMIT 1');
        $sql->execute();
        $result = $sql->fetch();
        if($result['paymentmethodD']== 1){
            
            echo '<div id="paypal-button-container"></div> ';
        }else{
            echo '
                <a href="viewinvoice.php?id='.$invoiceid.'" class="btn btn-primary" style="width:100%"> OK </a>
                <p>Kindly initiate a new support ticket and attach the necessary documents or screenshots to facilitate the payment process.</p>
            ';
        }
    } else {
        $sql = $con->prepare('SELECT methot,paymentmethodD FROM tblpayment_method WHERE paymentmethodD = ?');
        $sql->execute(array($paymentmethod));
        $result = $sql->fetch();
        if ($result) {
            if($result['paymentmethodD']== 1){
                echo '<div id="paypal-button-container"></div> ';
            }else{
                echo '
                    <a href="viewinvoice.php?id='.$invoiceid.'" class="btn btn-primary" style="width:100%"> OK </a>
                    <p>Kindly initiate a new support ticket and attach the necessary documents or screenshots to facilitate the payment process.</p>
                ';
            }
        } else {
            $sql = $con->prepare('SELECT note FROM tblpayment_method LIMIT 1');
            $sql->execute();
            $result = $sql->fetch();
            if($result['paymentmethodD']== 1){
                echo '<div id="paypal-button-container"></div> ';
            }else{
                echo '
                    <a href="viewinvoice.php?id='.$invoiceid.'" class="btn btn-primary" style="width:100%"> OK </a>
                    <p>Kindly initiate a new support ticket and attach the necessary documents or screenshots to facilitate the payment process.</p>
                ';
            }
        }
    }
?>


<script>
    var inputElement = document.getElementById("txtamont");
    var amountpaypal = inputElement.value;

    paypal.Buttons({
            createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                amount: {
                    value: amountpaypal
                }
                }],
                application_context: {
                shipping_preference: 'NO_SHIPPING'
                }
            });
            },
            onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                let tras=details.id
                location.href = "paymentpaypal.php?id="+tras+"&invoiceID=<?php echo $invoiceid ?>&amountpay=" + encodeURIComponent(amountpaypal);
            });
            }
    }).render('#paypal-button-container');

</script>