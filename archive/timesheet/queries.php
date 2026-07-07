
<?php


function punch_basic(){
    
    $sql1 = "SELECT first_name,last_name,Punch.* FROM Punch,Employee";
    $sql2 = "WHERE date > DATE_SUB(NOW(), INTERVAL 2 MONTH) AND fk_employeeID = employeeID";
    $sql3 = "ORDER BY last_name,date,time";

    $sql = "$sql1 $sql2 $sql3";

    return($sql);
}
function getPunchData($db,$id,$year,$month,$day,$type){
    $sql = "SELECT time,MONTH(date) as month,DAY(date) as day,YEAR(date) as year,type FROM Punch WHERE fk_employeeID = $id AND type='$type' AND MONTH(date)=$month AND DAY(date)=$day AND YEAR(date)=$year ORDER BY time";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }

    $result->free();

    return $data;
}

function getPunchFields($db){
    $sql = "SELECT time,MONTH(date) as month,DAY(date) as day,YEAR(date) as year,type FROM Punch ORDER BY time";
    $result = mysqli_query($db,$sql);
    if($result !== false){
        $fields=mysqli_fetch_fields($result);
    }

    $result->free();

    return $fields;
}

function getEmployee($db,$username,$password){
    $sql = "SELECT employeeID,first_name,last_name FROM admin,Employee WHERE username = '$username' AND password = '$password' AND fk_employeeID = employeeID";
    $data = array();
    $result = mysqli_query($db,$sql);
    
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Employee Data!<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

?>
