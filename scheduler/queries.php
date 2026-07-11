
<?php

function getScheduleBasic($db){
    $sql1 = "SELECT * FROM Schedule";
    $sql3 = "ORDER BY last_name ASC";

    $sql = "$sql1 $sql2 $sql3";
    $data = array();

    $result = mysqli_query($db,$sql);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Schedule Data!<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}


function getSchedulesAll($db){
    $sql1 = "SELECT scheduleID,first_name,last_name,time_in,lunch_out,lunch_in,time_out,days_of_week FROM Schedule,Employee";
    $sql2 = "WHERE fk_employeeID = employeeID";
    $sql3 = "ORDER BY time_in ASC";

    $sql = "$sql1 $sql2 $sql3";
    
    $data = array();

    $result = mysqli_query($db,$sql);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Schedule Data!<br>\n";
        echo "<br>".mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function getLastInsertData($db){
    $sql = "SELECT * FROM Schedule WHERE scheduleID = LAST_INSERT_ID()";
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

    return $return;
}

//Show the attendance sheet
function showSchedule($db,$day){

    $data = getSchedulesAll($db);
    $found = count($data['data']);

    echo "<u>$found records found</u><br>\n";

    echo "<form action='/upload.php' method='POST'>\n";
    echo "<table style='width:100%; background-color:#ffffff;'>";
    //header of the table
    echo "<tr>";
    $min = 14.5 * 60;
    $num = $min / 30;
    for($i = 0; $i < $num; $i++){
        $hour = floor(($i * 30) / 60) + 6;
        $min = ($i * 30) % 60;
        echo "<th>";
        if($hour < 10)
            echo "0"."$hour";
        elseif($hour > 12){
            $hour = $hour % 12;
            echo "0$hour";
        }else
            echo "$hour";
        echo ":";
        if($min == 0)
            echo "00";
        else
            echo "$min";
        echo "</th>";
    }
    echo "</tr>";

    //content of the table
    foreach($data['data'] as $row){
        $tmp = $row['days_of_week'];
        $days_of_week = explode(',',$tmp);
        $found = false;
        foreach($days_of_week as $d){
            $today = date('w');
            $week = array('sun','mon','tue','wed','thu','fri','sat');
            if($d == $day)
                $found = true;
            elseif($day == 'today' and $week[$today] == $d)
                $found = true;

            if($found)
                break;
        }

        if($found){
            echo "<tr>\n";

            $tmp = $row['time_in'];
            $h = substr($tmp,0,2);
            $min = substr($tmp,3,2);
            $total_min = (intval($h) - 6) * 60 + intval($min);
            $index1 = floor($total_min / 30);

            $tmp = $row['lunch_out'];
            $index2 = -1;
            if($tmp != ''){
                $h = substr($tmp,0,2);
                $min = substr($tmp,3,2);
                $total_min = (intval($h) - 6) * 60 + intval($min);
                $index2 = floor($total_min / 30);
            }

            $tmp = $row['lunch_in'];
            $index3 = -1;
            if($tmp != ''){
                $h = substr($tmp,0,2);
                $min = substr($tmp,3,2);
                $total_min = (intval($h) - 6) * 60 + intval($min);
                $index3 = floor($total_min / 30);
            }
            
            $tmp = $row['time_out'];
            $h = substr($tmp,0,2);
            $min = substr($tmp,3,2);
            $total_min = (intval($h) - 6) * 60 + intval($min);
            $index4 = floor($total_min / 30);

            for($i = 0; $i < $index1; $i++)
                echo "<th></th>";

            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $assignment = $row['assignment'];
            if($index2 != -1 and $index3 != -1){
                $colspan1 = $index2 - $index1 ;
                $colspan2 = $index3 - $index2 ;
                $colspan3 = $index4 - $index3 ;

                $time_in = date('h:i A', strtotime($row['time_in']));
                $time_out = date('h:i A', strtotime($row['time_out']));
                $lunch_out = date('h:i A', strtotime($row['lunch_out']));
                $lunch_in = date('h:i A', strtotime($row['lunch_in']));

                echo "<td colspan=$colspan1>";
                echo $time_in." - $first_name $last_name => $assignment - ".$lunch_out;
                echo "</td>";
                echo "<td colspan=$colspan2 style='background-color:#000000; color:#ffffff;'>";
                echo 'break time';
                echo "</td>";
                echo "<td colspan=$colspan3>";
                echo $lunch_in." - $first_name $last_name => $assignment - ".$time_out;
                echo "</td>";

            }else{
                $total_span = $index4 - $index1;
                $time_in = date('h:i A', strtotime($row['time_in']));
                $time_out = date('h:i A', strtotime($row['time_out']));
                $lunch_out = date('h:i A', strtotime($row['lunch_out']));
                $lunch_in = date('h:i A', strtotime($row['lunch_in']));

                echo "<td colspan=$total_span>";
                echo $time_in." - $first_name $last_name => $assignment - ".$time_out;
                echo "</td>";
            }

            $total = 14.5 * 60 / 30;
            $end_adjustment = $total - $index4;
            for($i = 0; $i < $end_adjustment; $i++)
                echo "<th></th>";

            $id = $row['scheduleID'];
            echo "<td><button formaction='update/search_update.php' style='width:30px; height:30px;' class='circular' name='id' value=$id>?</button></td>\n";
            echo "<td><button formaction='delete/search_delete.php' style='width:30px; height:30px;' class='circular' name='id' value=$id>X</button></td>\n";
            echo "</tr>\n";
        }
    }
    echo "<tr>";
    $min = 14.5 * 60;
    $num = $min / 30;
    for($i = 0; $i < $num; $i++){
        $hour = floor(($i * 30) / 60) + 6;
        $min = ($i * 30) % 60;
        echo "<th>";
        if($hour < 10)
            echo "0"."$hour";
        elseif($hour > 12){
            $hour = $hour % 12;
            echo "0$hour";
        }else
            echo "$hour";
        echo ":";
        if($min == 0)
            echo "00";
        else
            echo "$min";
        echo "</th>";
    }
    echo "</tr>";
    echo "</table>";
    echo "</form>";
}

//Show the attendance sheet
function showDeleteableSchedule($db,$data,$fields){

    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
        echo "<th>DELETE THIS schedule</th>\n";
    echo "</tr>";
        
    foreach($data as $row){
        echo "<tr>\n";
        $id = $row['scheduleID'];
        foreach($fields as $f){
            if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                $date = new DateTime($row[$f->name]);
                echo "<td nowrap>" . $date->format('m-d-Y')  ."</td>\n";
            }elseif(strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false or $f->name == 'lunch_out' or $f->name == 'lunch_in'){
                $time = new DateTime($row[$f->name]);
                echo "<td nowrap>" . $time->format('h:i A')  ."</td>\n";
            }else{
                echo "<td nowrap>" . $row[$f->name] ."</td>\n";
            }
        }
        
        echo "<td><button class='circularsmall' name='id' value='$id'>--</button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}


?>
