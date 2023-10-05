<?php
    
    function checkItem($select, $from, $value) {
        global $con;
        $statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
        $statement->execute(array($value));
        $count = $statement->rowCount();
        return $count;
    }
    
    function getCount($con, $table, $condition = '') {
        $sql = "SELECT COUNT(*) AS count FROM $table $condition";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    function countItems($item, $table) {
		global $con;
		$stmt2 = $con->prepare("SELECT COUNT($item) FROM $table");
		$stmt2->execute();
		return $stmt2->fetchColumn();
	}

    function countbetweendates($table,$column,$begin,$end){
        global $con;
        $stmt3 = $con->prepare("SELECT COUNT(*) AS C FROM $table WHERE $column  BETWEEN  ?  AND ? ");
        $stmt3->execute(array($begin,$end));
        $result=$stmt3->fetch();
        return $result['C'];
    }

    function countitemRS($select, $from,$column,$value) {
        global $con;
        $statement = $con->prepare("SELECT $select FROM $from WHERE $column = ?");
        $statement->execute(array($value));
        $count = $statement->rowCount();
        return $count;
    }

    function getID($PrimeryKEY,$from,$column,$value){
        global $con;
        $sql = $con->prepare("SELECT $PrimeryKEY FROM $from WHERE $column =? ");
        $sql->execute(array($value));
        $result=$sql->fetch();
        return $result[$PrimeryKEY];
    }

    
    function get_last_ID($PrimeryKEY,$tableName){
        global $con;
        $sql=$con->prepare("SELECT $PrimeryKEY FROM $tableName ORDER BY $PrimeryKEY DESC LIMIT 1");
        $sql->execute();
        $result=$sql->fetch();
        return $result[$PrimeryKEY];
    }

    function calculateDistanceBetweenTwoPoints($latitudeOne='', $longitudeOne='', $latitudeTwo='', $longitudeTwo='',$distanceUnit ='',$round=false,$decimalPoints='')
    {
        if (empty($decimalPoints)) 
        {
            $decimalPoints = '3';
        }
        if (empty($distanceUnit)) {
            $distanceUnit = 'KM';
        }
        $distanceUnit = strtolower($distanceUnit);
        $pointDifference = $longitudeOne - $longitudeTwo;
        $toSin = (sin(deg2rad($latitudeOne)) * sin(deg2rad($latitudeTwo))) + (cos(deg2rad($latitudeOne)) * cos(deg2rad($latitudeTwo)) * cos(deg2rad($pointDifference)));
        $toAcos = acos($toSin);
        $toRad2Deg = rad2deg($toAcos);

        $toMiles  =  $toRad2Deg * 60 * 1.1515;
        $toKilometers = $toMiles * 1.609344;
        $toNauticalMiles = $toMiles * 0.8684;
        $toMeters = $toKilometers * 1000;
        $toFeets = $toMiles * 5280;
        $toYards = $toFeets / 3;


              switch (strtoupper($distanceUnit)) 
              {
                  case 'ML'://miles
                         $toMiles  = ($round == true ? round($toMiles) : round($toMiles, $decimalPoints));
                         return $toMiles;
                      break;
                  case 'KM'://Kilometers
                        $toKilometers  = ($round == true ? round($toKilometers) : round($toKilometers, $decimalPoints));
                        return $toKilometers;
                      break;
                  case 'MT'://Meters
                        $toMeters  = ($round == true ? round($toMeters) : round($toMeters, $decimalPoints));
                        return $toMeters;
                      break;
                  case 'FT'://feets
                        $toFeets  = ($round == true ? round($toFeets) : round($toFeets, $decimalPoints));
                        return $toFeets;
                      break;
                  case 'YD'://yards
                        $toYards  = ($round == true ? round($toYards) : round($toYards, $decimalPoints));
                        return $toYards;
                      break;
                  case 'NM'://Nautical miles
                        $toNauticalMiles  = ($round == true ? round($toNauticalMiles) : round($toNauticalMiles, $decimalPoints));
                        return $toNauticalMiles;
                      break;
              }


    }


    function calculatePaymentDetails($invoiceAmount, $amountPaid = 0) {
        if ($invoiceAmount < 150) {
            $numberOfPayments = 1;
            $paymentAmount = $invoiceAmount;
        } elseif ($invoiceAmount >= 150 && $invoiceAmount <= 500) {
            $numberOfPayments = 2;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 500 && $invoiceAmount <= 1000) {
            $numberOfPayments = 3;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 1000 && $invoiceAmount <= 3000) {
            $numberOfPayments = 4;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 3000 && $invoiceAmount <= 6000) {
            $numberOfPayments = 5;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 6000 && $invoiceAmount <= 10000) {
            $numberOfPayments = 6;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 10000 && $invoiceAmount <= 15000) {
            $numberOfPayments = 7;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } elseif ($invoiceAmount > 15000 && $invoiceAmount <= 20000) {
            $numberOfPayments = 8;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        } else {
            $numberOfPayments = 10;
            $paymentAmount = $invoiceAmount / $numberOfPayments;
        }
        
        $paymentsMade = floor($amountPaid / $paymentAmount);
        $remainingPayments = max(0, $numberOfPayments - $paymentsMade);
    
        $overpayment = $amountPaid - ($paymentsMade * $paymentAmount);
    
        $paymentDetails = array(
            'numberOfPayments' => $numberOfPayments,
            'paymentAmount' => $paymentAmount,
            'overpayment' => $overpayment,
            'paymentsMade' => $paymentsMade,
            'remainingPayments' => $remainingPayments
        );
    
        return $paymentDetails;
    }

    function calculateCommission($invoiceID, $userPayment, $con) {
        $result = array(
            'commission' => 0,
            'SalemanID' => null
        );
    
        // Get the total amount, total tax, client ID, and promo_Code from tblinvoice
        $invoiceQuery = "SELECT TotalAmount, TotalTax, ClientID FROM tblinvoice WHERE InvoiceID = :invoiceID";
        $invoiceStmt = $con->prepare($invoiceQuery);
        $invoiceStmt->bindParam(':invoiceID', $invoiceID);
        $invoiceStmt->execute();
        $invoiceRow = $invoiceStmt->fetch();
    
        if ($invoiceRow) {
            $totalAmount = $invoiceRow['TotalAmount'];
            $totalTax = $invoiceRow['TotalTax'];
            $clientID = $invoiceRow['ClientID'];
    
            // Calculate the total invoice amount
            $totalInvoiceAmount = $totalAmount + $totalTax;
    
            // Get the promo_Code associated with the client
            $clientQuery = "SELECT promo_Code FROM tblclients WHERE ClientID = :clientID";
            $clientStmt = $con->prepare($clientQuery);
            $clientStmt->bindParam(':clientID', $clientID);
            $clientStmt->execute();
            $clientRow = $clientStmt->fetch();
    
            if ($clientRow) {
                $promoCode = $clientRow['promo_Code'];
    
                // Get the SalemanID and commission rate based on the promo code
                $salesmanData = getSalesmanDataByPromoCode($promoCode, $con);
    
                if ($salesmanData) {
                    $SalemanID = $salesmanData['SalePersonID'];
                    $commissionRate = $salesmanData['ComitionRate'];
    
                    // Check if the service allows commission based on Get_commission
                    $detailInvoiceQuery = "SELECT Service FROM tbldetailinvoice WHERE Invoice = :invoiceID";
                    $detailInvoiceStmt = $con->prepare($detailInvoiceQuery);
                    $detailInvoiceStmt->bindParam(':invoiceID', $invoiceID);
                    $detailInvoiceStmt->execute();
    
                    while ($detailInvoiceRow = $detailInvoiceStmt->fetch()) {
                        $serviceID = $detailInvoiceRow['Service'];
    
                        // Check if the service allows commission based on Get_commission
                        $serviceQuery = "SELECT Get_commission FROM tblservices WHERE ServiceID = :serviceID";
                        $serviceStmt = $con->prepare($serviceQuery);
                        $serviceStmt->bindParam(':serviceID', $serviceID);
                        $serviceStmt->execute();
                        $serviceRow = $serviceStmt->fetch();
    
                        if ($serviceRow) {
                            // Convert Get_commission to boolean
                            $allowsCommission = ($serviceRow['Get_commission']==1)?1:0;
    
                            if ($allowsCommission == 1) {
                                // Calculate commission for the item based on user's payment and total invoice amount
                                $itemCommission = ($totalInvoiceAmount / $userPayment) * ($totalInvoiceAmount * ($commissionRate / 100));
    
                                // Add the item's commission to the total commission
                                $result['commission'] += $itemCommission;
                            }
                        }
                    }
    
                    // Set the SalemanID in the result array
                    $result['SalemanID'] = $SalemanID;
                }
            }
        }
    
        return $result;
    }
    
    // Function to get SalemanID and commission rate based on the promo code
    function getSalesmanDataByPromoCode($promoCode, $con) {
        $salesmanQuery = "SELECT SalePersonID,ComitionRate FROM tblsalesperson WHERE saleActive = 1 AND  PromoCode = :promoCode";
        $salesmanStmt = $con->prepare($salesmanQuery);
        $salesmanStmt->bindParam(':promoCode', $promoCode);
        $salesmanStmt->execute();
        return $salesmanStmt->fetch(PDO::FETCH_ASSOC);
    }
?>