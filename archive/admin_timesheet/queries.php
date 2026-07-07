
<?php

function punch_basic(){
    
    $sql1 = "SELECT last_name,first_name,Punch.* FROM Punch,Employee";
    $sql2 = "WHERE date > DATE_SUB(NOW(), INTERVAL 2 MONTH) AND fk_employeeID = employeeID";
    $sql3 = "ORDER BY last_name ASC,first_name ASC,date DESC,time DESC";

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

function getLastInsertData($db){
    $sql = "SELECT * FROM Punch WHERE punchID = LAST_INSERT_ID()";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting last Insert Data!<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

function getEmployeeSearchData($db,$first_name,$last_name){
    $sql = "SELECT employeeID,first_name,last_name FROM Employee WHERE first_name = '$first_name' and last_name = '$last_name'";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Searching result for employee<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
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

function minPunchQuery(){
    $sql = "SELECT first_name,last_name,punchID,date,time,type,fk_employeeID FROM Punch,Employee WHERE employeeID = fk_employeeID AND employeeID = (SELECT MAX(fk_employeeID) AS max FROM Punch)";
    return $sql;
}
//Show the attendance sheet
function showDeleteablePunch($db,$data,$fields){

    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    echo "<form method='POST' action='search_deletePunch.php'>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
        echo "<th>DELETE THIS PUNCH</th>\n";
    echo "</tr>";
        
    foreach($data as $row){
        echo "<tr>\n";
        $id = $row['punchID'];
        foreach($fields as $f){
            if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                $date = new DateTime($row[$f->name]);
                echo "<td>" . $date->format('m-d-Y')  ."</td>\n";
            }elseif(strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false){
                $time = new DateTime($row[$f->name]);
                echo "<td>" . $time->format('h:i:s A')  ."</td>\n";
            }else{
                echo "<td>" . $row[$f->name] ."</td>\n";
            }
        }
        
        echo "<td><button class='circular' name='id' value='$id'>--</button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</form>\n";
}

?>
