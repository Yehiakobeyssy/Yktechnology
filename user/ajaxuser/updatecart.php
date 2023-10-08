<?php
    session_start();


    include '../../settings/connect.php';
    include '../../common/function.php';

    $clientId= (isset($_COOKIE['user']))?$_COOKIE['user']:$_SESSION['user'];
    
    $id = $_POST['btnsave'];
    $serviceID =$_POST['serviceID'];
    $titlename = $_POST['titlename'];
    $domain = (isset($_POST['domain']))?$_POST['domain']:'';
    $transfer = (isset($_POST['transfer']))?$_POST['transfer']:0;
    $code = isset($_POST['code'])?$_POST['code']:'';
    $forwhat = $_POST['forwhat'];
    $colors = $_POST['colors'];
    $description = $_POST['description'];
    $filename = $_FILES['filename']['name'];
 

    if(!empty($_FILES['filename']['name'])){
        $temp=explode(".",$_FILES['filename']['name']);
        $newfilename=round(microtime(true)).'.'.end($temp);
        move_uploaded_file($_FILES['filename']['tmp_name'],'../../Documents/'.$newfilename);
    }else{
        $newfilename='';
    }

    if(isset($_SESSION['shooping'])){
        $itemToUpdate = $id; 
        $newItemData = array(
            'id' => $serviceID,   
            'titlename' => $titlename, 
            'domain' => $domain,
            'transfer' => $transfer,
            'code' => $code,
            'forwhat' => $forwhat,  
            'colors' => $colors, 
            'description' => $description,
            'filename' => $newfilename 
        );
        if(isset($_SESSION['shooping'][$itemToUpdate])){
            $_SESSION['shooping'][$itemToUpdate] = $newItemData;

            $sql=$con->prepare('SELECT Service_Price,days 
                                FROM tblservices 
                                INNER JOIN tblduration ON tblservices.Duration = tblduration.DurationID
                                WHERE ServiceID=?');
            $sql->execute(array($serviceID));
            $result = $sql->fetch();
            $serviceprice = $result['Service_Price'];
            $duration = $result['days'];
            $currentDate = date('Y-m-d'); 
            $expirationDate = date('Y-m-d', strtotime($currentDate . ' + ' . $duration . ' days'));

            $ClientID       = $clientId;
            $Date_service   = $currentDate;
            $ServiceID      = $serviceID;
            $Price          = $serviceprice;
            $Dateend        = $expirationDate;
            $ServiceTitle   = $titlename;
            $ServiceDomain  = $domain;
            $ServiceTransfer= $transfer;
            $CodeTransfer   = $code;
            $forwhat        = $forwhat;
            $Colors         = $colors;
            $Discription    = $description;
            $filename       = $newfilename ;
            $serviceStatus  = 1 ;

            $sql=$con->prepare('INSERT INTO tblclientservices (ClientID,Date_service,ServiceID,Price,Dateend,ServiceTitle,ServiceDomain,ServiceTransfer,CodeTransfer,forwhat,Colors,Discription,filename,ServiceDone,serviceStatus)
                                VALUES (:ClientID,:Date_service,:ServiceID,:Price,:Dateend,:ServiceTitle,:ServiceDomain,:ServiceTransfer,:CodeTransfer,:forwhat,:Colors,:Discription,:filename,:ServiceDone,:serviceStatus)');
            $sql->execute(array(
                'ClientID'          => $ClientID,
                'Date_service'      => $Date_service,
                'ServiceID'         => $ServiceID,
                'Price'             => $Price,
                'Dateend'           => $Dateend,
                'ServiceTitle'      => $ServiceTitle,
                'ServiceDomain'     => $ServiceDomain,
                'ServiceTransfer'   => $ServiceTransfer,
                'CodeTransfer'      => $CodeTransfer,
                'forwhat'           => $forwhat,
                'Colors'            => $Colors,
                'Discription'       => $Discription,
                'filename'          => $filename,
                'ServiceDone'       => 0,
                'serviceStatus'     => $serviceStatus
            ));

            $ClientServiceID=get_last_ID('ServicesID','tblclientservices');

            if(isset($_SESSION['dlinvoice'])){
                $itemarray=array(
                    'clientserviceID' =>$ClientServiceID,
                    'Service'         =>$ServiceID,
                    'serviceTitle'    =>$ServiceTitle,
                    'Price'           =>$Price,
                );
                array_push($_SESSION['dlinvoice'],$itemarray);
            }else{
                $itemarray=array(
                    'clientserviceID' =>$ClientServiceID,
                    'Service'         =>$ServiceID ,
                    'serviceTitle'    =>$ServiceTitle ,
                    'Price'           =>$Price
                );
                $_SESSION['dlinvoice'][0]= $itemarray;
            };

            unset($_SESSION['shooping'][$itemToUpdate]);

        }
    }
    

    echo '<script>location.href="../mycart.php"</script>';
?>
