<?php
    
    function checkItem($select, $from, $value) {
        global $con;
        $statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
        $statement->execute(array($value));
        $count = $statement->rowCount();
        return $count;
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

?>