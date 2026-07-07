
<?php

//get basic expenditure info
function getExpenditureBasic($db){
    $sql1 = "SELECT name,Expenditure.* FROM Expenditure";
    $sql3 = "ORDER BY date DESC, time DESC";

    $sql = "$sql1 $sql3";
    $data = array();

    $result = mysqli_query($db,$sql);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Expenditure Data!<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

//query the last insert from the expenditure table
function getLastInsertData($db){
    $sql = "SELECT * FROM Expenditure WHERE expenditureID = LAST_INSERT_ID()";
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

//query distinct categories from the expenditures
function queryDistinctCategories($db){
    $sql = "SELECT DISTINCT(category) FROM Expenditure
            ORDER BY category";

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

//query distinct accounts from the expenditures
function queryDistinctAccounts($db){
    $sql = "SELECT DISTINCT(bank_account) FROM Expenditure
            ORDER BY bank_account";

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

//advanced query function for getting the amount at the specific month for a category
function queryCategoryAmount($db,$category,$month,$year){
    $sql = "SELECT DISTINCT(category), CAST((COALESCE(SUM(amount),0)) AS DECIMAL(10,2)) as `total`
        FROM Expenditure 
        WHERE category = '$category' AND MONTH(date) = $month AND YEAR(date) = $year
        GROUP BY category";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting category amount<br>";
        echo "<h2>".mysqli_error($db)."</h2>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//advanced query function for getting the amount at the specific month for an account
function queryAccountAmount($db,$account,$month,$year){
    $sql = "SELECT DISTINCT(bank_account), CAST((COALESCE(SUM(amount),0)) AS DECIMAL(10,2)) as `total`
        FROM Expenditure 
        WHERE bank_account = '$account' AND MONTH(date) = $month AND YEAR(date) = $year
        GROUP BY bank_account";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting account amount<br>";
        echo "<h2>".mysqli_error($db)."</h2>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}
//query for getting the expenditure report for a particular month and year
function queryDetailExpenditure($db,$category,$account,$month,$year){
    if($category != '')
        $sql = "SELECT * FROM Expenditure WHERE YEAR(date) = $year AND MONTH(date) = $month AND category = '$category' ORDER BY date";
    elseif($account != '')
        $sql = "SELECT * FROM Expenditure WHERE YEAR(date) = $year AND MONTH(date) = $month AND bank_account = '$account' ORDER BY date";
    else
        $sql = "both category and account are empty<br>";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h2>Error queryDetailExpenditure</h2><br>";
        echo "<h2>query: $sql</h2><br>";
        echo "<h2>".mysqli_error($db)."</h2>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//show expenditure Report
function showExpenditureReportByAccount($db,$year){

    //initialize some needed constants
    $distinct_names = queryDistinctAccounts($db);
    $months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

    //header
    echo "\n<form action='viewDetails.php' method='POST' enctype='multipart/form-data' target='_blank'>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>";
    echo "<th></th>";

    //show the top column
    foreach($months as $m){
        echo "<th>";
        echo "$m";
        echo "</th>";
    }
    echo "<th>TOTAL</th>";
    echo "</tr>";

    //inner data
    //show the monthly amount for each distinct bank_account
    $mon_total = array(0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00);
    $grand_total = 0.00;
    foreach($distinct_names['data'] as $row){
        $bank_account = $row['bank_account'];
        $cat_total = 0.00;
        echo "<tr>";
        echo "<th style='text-align:left;' >";
        echo "$bank_account";
        echo "</th>";

        //bank_account amount per month
        for($i = 0; $i < count($months); $i++){
            $month = $months[$i];
            $monthly_data = queryAccountAmount($db,$bank_account,$i + 1,$year);
            $amount = $monthly_data['data'][0]['total'];
            if($amount == '')
                $amount = '0.00';
            $cat_total += $amount;
            $mon_total[$i] += $amount;
            $m_count = $i + 1;
            echo "<td class='data' onclick=\"post('viewDetails.php',{bank_account:'$bank_account',month:$m_count,year:$year})\">\n";
            echo "$ ". "$amount";
            echo "</td>";
        }
        //bank_account total
        echo "<th style='text-align:right;' class='data'>$". number_format((float)$cat_total,2,'.','')."</th>";
        echo "\n</tr>";
    }

    //monthly totals per month
    echo "<tr>";
    echo "<th>TOTAL</th>";
    foreach($mon_total as $monthly){
        echo "<th style='text-align:right;' >$". number_format((float)$monthly,2,'.','') ."</th>";
        $grand_total += $monthly;
    }

    //grand total for entire year
    echo "<th>$". number_format((float)$grand_total,2,'.','') . "</th>";
    echo "</tr>";

    echo "</table>";
    echo "</form>\n";
}

//show expenditure Report
function showExpenditureReportByCategory($db,$year){

    //initialize some needed constants
    $categories = queryDistinctCategories($db);
    $months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

    //header
    echo "\n<form action='viewDetails.php' method='POST' enctype='multipart/form-data' target='_blank'>\n";
    echo "<table style='text-align:left;' class='data' align=\"center\">";
    echo "<tr>";
    echo "<th></th>";

    //show the top column
    foreach($months as $m){
        echo "<th>";
        echo "$m";
        echo "</th>";
    }
    echo "<th>TOTAL</th>";
    echo "</tr>";

    //inner data
    //show the monthly amount for each distinct category
    $mon_total = array(0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00);
    $grand_total = 0.00;
    foreach($categories['data'] as $row){
        $category = $row['category'];
        $cat_total = 0.00;
        echo "<tr>";
        echo "<th style='text-align:left;' >";
        echo "$category";
        echo "</th>";

        //category amount per month
        for($i = 0; $i < count($months); $i++){
            $month = $months[$i];
            $monthly_data = queryCategoryAmount($db,$category,$i + 1,$year);
            $amount = $monthly_data['data'][0]['total'];
            if($amount == '')
                $amount = '0.00';
            $cat_total += $amount;
            $mon_total[$i] += $amount;
            $m_count = $i + 1;
            echo "<td style='text-align:right;' class='data' onclick=\"post('viewDetails.php',{category:'$category',month:$m_count,year:$year})\">\n";
            echo "$ "."$amount";
            echo "</td>";
        }
        //category total
        echo "<th style='text-align:right;' class='data'>$". number_format((float)$cat_total,2,'.','')."</th>";
        echo "\n</tr>";
    }

    //monthly totals per month
    echo "<tr>";
    echo "<th>TOTAL</th>";
    foreach($mon_total as $monthly){
        echo "<th>$". number_format((float)$monthly,2,'.','') ."</th>";
        $grand_total += $monthly;
    }

    //grand total for entire year
    echo "<th style='text-align:right;' >$". number_format((float)$grand_total,2,'.','') ."</th>";
    echo "</tr>";

    echo "</table>";
    echo "</form>\n";
}

//Show the attendance sheet
function showDeleteableExpenditure($data,$fields){

    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
        echo "<th>DELETE THIS EXPENDITURE</th>\n";
    echo "</tr>";
        
    foreach($data as $row){
        echo "<tr>\n";
        $id = $row['expenditureID'];
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

//show the detailed expenditure report for a particular month and year
function showDetails($db,$category,$account,$month,$year){
    $total = 0;

    $months = array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
    $expenditure_data = queryDetailExpenditure($db,$category,$account,$month,$year);

    $found = count($expenditure_data['data']);
    echo "<u>$found records $months[$month] $year</u><br>\n";
    echo "<form method='POST' action=''>";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($expenditure_data['fields'] as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
    echo "<th></th>";
    echo "<th></th>";
    echo "</tr>";
    
    foreach($expenditure_data['data'] as $row){
        $total += $row['amount'];
        $id = $row[0];
        echo "<tr>\n";
        foreach($expenditure_data['fields'] as $f){
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
        echo "<td><button formaction='update/search_update.php' name='id' value=$id><img style='width:15px; height:15px;' src=\"/images/edit.png\"></button></td>\n";
        echo "<td><button formaction='delete/search_delete.php' name='id' value=$id><img style='width:15px; height:15px;' src=\"/images/x_mark.png\"></button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</form>";

    
    return $total;
}


?>
