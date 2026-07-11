
<?php

function payment_basic(){
    
    $sql1 = "SELECT paymentID, student_1,student_2,student_3,amount,date,time,description,method FROM Payment,Account";
    $sql2 = "WHERE accountID = fk_accountID";
    $sql3 = "ORDER BY date DESC,time DESC LIMIT 500";

    $sql = "$sql1 $sql2 $sql3";

    return($sql);
}

//Show the deleteable payment sheet 
function showDeleteablePayment($db,$data,$fields){

    $found = count($data ?? []);
    echo "<u>$found records found</u><br>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
        echo "<th>DELETE THIS payment</th>\n";
    echo "</tr>";
        
    foreach($data as $row){
        echo "<tr>\n";
        $id = $row['paymentID'];
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
        echo "<td><button class='circularsmall' name='id' value='$id'>--</button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}

?>
