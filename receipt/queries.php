
<?php

function receipt_basic($db){
    
    $sql1 = "SELECT student_1,student_2,Receipt.* FROM Receipt
            INNER JOIN Payment_to_Receipt ON receiptID = fk_receiptID
            INNER JOIN Payment ON paymentID = fk_paymentID
            INNER JOIN Account ON accountID = fk_accountID";
    $sql2 = "ORDER BY date DESC, time DESC";
    $sql = "$sql1 $sql2";
    
    $result = mysqli_query($db,$sql);

    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>\n";
        echo "Error Description: ".mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}


function showDeleteableReceipt($db,$data,$fields){

    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
        echo "<th>DELETE THIS receipt</th>\n";
    echo "</tr>";

    foreach($data as $row){
        echo "<tr>\n";
        $id = $row['data'][0]['receiptID'];

        foreach($fields as $f){
            if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                $date = new DateTime($row[$f->name]);
                echo "<td nowrap>" . $date->format('m-d-Y')  ."</td>\n";
            }elseif(strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false){
                $time = new DateTime($row[$f->name]);
                echo "<td nowrap>" . $time->format('h:i:s A')  ."</td>\n";
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
