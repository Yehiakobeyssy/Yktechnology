<?php
    include '../../settings/connect.php';

    $searchtext = (isset($_GET['search'])) ? $_GET['search'] : '';
    $search = str_replace('_', ' ', $searchtext);
    $searchParam = "%" . $search . "%";
    
    $sql=$con->prepare("SELECT 
                            CONCAT(Sale_FName, ' ', Sale_LName) AS AgentName,
                            email_Sale AS Email,
                            CountryName AS Country,
                            PromoCode,
                            saleActive,
                            SalePersonID,
                            ComitionRate AS CommissionRate,
                            COALESCE((SELECT COUNT(*) FROM tblclients WHERE promo_Code = tblsalesperson.PromoCode), 0) AS Clients,
                            COALESCE((SELECT SUM(Depit - Crieted) FROM tblaccountstatment_saleperson WHERE SaleManID = tblsalesperson.SalePersonID), 0) AS Balance
                        FROM tblsalesperson
                        LEFT JOIN tblcountrys ON tblsalesperson.Country = tblcountrys.CountryID
                        WHERE CONCAT(Sale_FName, ' ', Sale_LName)  LIKE ? OR email_Sale LIKE ? OR CountryName LIKE ? OR PromoCode LIKE ?
                        ORDER BY saleActive DESC;");
    $sql->execute(array($searchParam,$searchParam,$searchParam,$searchParam));
    $rows=$sql->fetchAll();
    foreach($rows as $row){
        if($row['saleActive'] ==1){
            $text= 'Block';
            $class = 'danger';
        }else{
            $text= 'Un Block';
            $class = 'success';
        }
        echo '
        <tr>
            <td>'.$row['AgentName'].'</td>
            <td>'.$row['Email'].'</td>
            <td>'.$row['Country'].'</td>
            <td>'.$row['PromoCode'].'</td>
            <td>'.number_format($row['CommissionRate'], 2, '.', '') .' %</td>
            <td>'.$row['Clients'].'</td>
            <td>'.number_format($row['Balance'], 2, '.', '').' $</td>
            <td class="btnstable">
                <a href="ManageSaleAgent.php?do=Edit&AID='.$row['SalePersonID'].'" class="btn btn-warning btnnewAgent">Deteil</a>
                <a href="ManageSaleAgent.php?do=Acc&AID='.$row['SalePersonID'].'" class="btn btn-primary btnnewAgent">Account Statment</a>
                <a href="ManageSaleAgent.php?do=block&AID='.$row['SalePersonID'].'" class="btn btn-'.$class.' btnnewAgent">'.$text.'</a>
            </td>
        </tr>
        ';  
    }
?>
