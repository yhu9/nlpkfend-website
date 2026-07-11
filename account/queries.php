
<?php

function queryAccountBasic($db){
    $query = "SELECT * FROM Account LIMIT 500";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function queryAccountsActive($db){

$query = "
SELECT details.accountID as aid,Account.autopay as AUTO,Account.drop_in as DROPIN,CONCAT(details.student_1,' / ',details.student_2,' / ',details.student_3) as Students_On_Account,details.authorization as auth, tuition2 as 'next month', tuition1 as 'this month',details.BALANCE,details.note as note
     FROM Account
     LEFT OUTER JOIN 
     (
         SELECT fk_accountID,SUM(GREATEST(CCA.NLPS_tuition - CCA.state_payment, 0)) AS tuition1 FROM CCA,Student
         WHERE studentID = fk_studentID AND (DATE(NOW()) <= CCA.end_date AND DATE(NOW()) >= CCA.start_date)
         GROUP BY fk_accountID
     ) t1 ON accountID = t1.fk_accountID
    LEFT OUTER JOIN 
          (
              SELECT fk_accountID,SUM(GREATEST(CCA.NLPS_tuition - CCA.state_payment, 0)) AS tuition2 FROM CCA,Student
              WHERE studentID = fk_studentID AND DATE_ADD(DATE(NOW()),INTERVAL 1 MONTH) <= CCA.end_date AND DATE_ADD(DATE(NOW()),INTERVAL 1 MONTH) >= CCA.start_date
              GROUP BY fk_accountID
          ) t2 ON accountID = t2.fk_accountID
     INNER JOIN(
SELECT account_detail.* FROM Account
     INNER JOIN
     (
         SELECT 
             acc.*,
             COALESCE(charge_table.total_charge,0) - COALESCE(pay_table.total_payment,0) AS BALANCE
         FROM
             Account acc
         LEFT OUTER JOIN 
         (
             SELECT 
                 Payment.fk_accountID, COALESCE(SUM(Payment.amount),0) AS total_payment
             FROM
                 Payment
             GROUP BY Payment.fk_accountID
         ) pay_table ON acc.accountID = pay_table.fk_accountID
         LEFT OUTER JOIN 
         (
             SELECT 
                 Charge.fk_accountID, COALESCE(SUM(Charge.amount),0) AS total_charge
             FROM
                 Charge
             GROUP BY Charge.fk_accountID
         ) charge_table ON acc.accountID = charge_table.fk_accountID
     ) account_detail ON Account.accountID = account_detail.accountID
     WHERE account_detail.status = 'active'
     ORDER BY account_detail.student_1
     ) details ON details.accountID = Account.accountID
     WHERE Account.status = 'active' ORDER BY Account.student_1";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function queryAccountsInactive($db){
	$query = "SELECT account_detail.accountID as aid,CONCAT(account_detail.student_1,' / ',account_detail.student_2,' / ',account_detail.student_3) as Students_On_Account,account_detail.status,account_detail.authorization,account_detail.BALANCE,account_detail.note as note FROM Account
	INNER JOIN
	(
		SELECT 
			acc.*,
			COALESCE(charge_table.total_charge,0) - COALESCE(pay_table.total_payment,0) AS BALANCE
		FROM
			Account acc
		LEFT OUTER JOIN 
		(
			SELECT 
				Payment.fk_accountID, COALESCE(SUM(Payment.amount),0) AS total_payment
			FROM
				Payment
			GROUP BY Payment.fk_accountID
		) pay_table ON acc.accountID = pay_table.fk_accountID
		LEFT OUTER JOIN 
		(
			SELECT 
				Charge.fk_accountID, COALESCE(SUM(Charge.amount),0) AS total_charge
			FROM
				Charge
			GROUP BY Charge.fk_accountID
		) charge_table ON acc.accountID = charge_table.fk_accountID
    ) account_detail ON Account.accountID = account_detail.accountID
    WHERE account_detail.status = 'inactive'
    ORDER BY account_detail.student_1";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

//query the account balance
function queryAccountsAll($db){
	$query = "SELECT account_detail.accountID as aid,CONCAT(account_detail.student_1,' / ',account_detail.student_2,' / ',account_detail.student_3) as Students_On_Account,account_detail.status,account_detail.authorization,account_detail.BALANCE,account_detail.note as note FROM Account
	INNER JOIN
	(
		SELECT 
			acc.*,
			COALESCE(charge_table.total_charge,0) - COALESCE(pay_table.total_payment,0) AS BALANCE
		FROM
			Account acc
		LEFT OUTER JOIN 
		(
			SELECT 
				Payment.fk_accountID, COALESCE(SUM(Payment.amount),0) AS total_payment
			FROM
				Payment
			GROUP BY Payment.fk_accountID
		) pay_table ON acc.accountID = pay_table.fk_accountID
		LEFT OUTER JOIN 
		(
			SELECT 
				Charge.fk_accountID, COALESCE(SUM(Charge.amount),0) AS total_charge
			FROM
				Charge
			GROUP BY Charge.fk_accountID
		) charge_table ON acc.accountID = charge_table.fk_accountID
    ) account_detail ON Account.accountID = account_detail.accountID
    ORDER BY account_detail.student_1";
    
	$result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//show month's tuition only
function queryMonthlyTuition($db,$date){
	$query = "SELECT Account.*, tuition as 'tuition this month'
	FROM Account
	LEFT OUTER JOIN 
	(
		SELECT fk_accountID,SUM(GREATEST(CCA.NLPS_tuition - CCA.state_payment, 0)) AS tuition FROM CCA,Student
        WHERE studentID = fk_studentID AND $date <= CCA.end_date AND $date >= CCA.start_date
		GROUP BY fk_accountID
    ) t1 ON accountID = t1.fk_accountID
    WHERE Account.status = 'active' ORDER BY Account.student_1";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function queryNextMonthTuition($db){
	$query = "SELECT Account.*, tuition as 'tuition this month'
	FROM Account
	LEFT OUTER JOIN 
	(
		SELECT fk_accountID,SUM(GREATEST(CCA.NLPS_tuition - CCA.state_payment, 0)) AS tuition FROM CCA,Student
		WHERE studentID = fk_studentID AND DATE_ADD(DATE(NOW()),INTERVAL 1 MONTH) <= CCA.end_date AND DATE_ADD(DATE(NOW()),INTERVAL 1 MONTH) >= CCA.start_date
		GROUP BY fk_accountID
    ) t1 ON accountID = t1.fk_accountID
    WHERE Account.status = 'active' ORDER BY Account.student_1";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function queryLastMonthTuition($db){
	$query = "SELECT Account.*, tuition as 'tuition this month'
	FROM Account
	LEFT OUTER JOIN 
	(
		SELECT fk_accountID,SUM(GREATEST(CCA.NLPS_tuition - CCA.state_payment, 0)) AS tuition FROM CCA,Student
		WHERE studentID = fk_studentID AND DATE_SUB(DATE(NOW()),INTERVAL 1 MONTH) <= CCA.end_date AND DATE_SUB(DATE(NOW()),INTERVAL 1 MONTH) >= CCA.start_date
		GROUP BY fk_accountID
    ) t1 ON accountID = t1.fk_accountID
    WHERE Account.status = 'active' ORDER BY Account.student_1";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

//get authorizations from students
function queryLateAuthorization($db){
    $query = "SELECT accountID,CONCAT(student_1,' / ', student_2,' / ', student_3) as Students_On_Account, authorization,  last_account_cca,note FROM Account
                LEFT OUTER JOIN
        (
            SELECT fk_accountID, MAX(last_cca) as last_account_cca FROM Student
            INNER JOIN
            (
                SELECT fk_studentID, MAX(end_date) as last_cca FROM CCA
                GROUP BY fk_studentID
            ) s ON studentID = fk_studentID
            GROUP BY fk_accountID
        ) a ON accountID = fk_accountID
        WHERE (last_account_cca <= CURDATE() OR last_account_cca IS NULL) AND Account.status = 'active'
        ORDER BY student_1";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
        echo "<h2>Function: queryLateAuthorization() </h2><br>";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

//get acounts with a balance greater than 0
function queryAccountsDue($db){

	$query = "SELECT account_detail.accountID as aid,CONCAT(account_detail.student_1,' / ',account_detail.student_2,' / ',account_detail.student_3) as Students_On_Account,account_detail.status,account_detail.authorization as auth,account_detail.BALANCE,account_detail.note as note FROM Account
	INNER JOIN
	(
		SELECT 
			acc.*,
			COALESCE(charge_table.total_charge,0) - COALESCE(pay_table.total_payment,0) AS BALANCE
		FROM
			Account acc
		LEFT OUTER JOIN 
		(
			SELECT 
				Payment.fk_accountID, COALESCE(SUM(Payment.amount),0) AS total_payment
			FROM
				Payment
			GROUP BY Payment.fk_accountID
		) pay_table ON acc.accountID = pay_table.fk_accountID
		LEFT OUTER JOIN 
		(
			SELECT 
				Charge.fk_accountID, COALESCE(SUM(Charge.amount),0) AS total_charge
			FROM
				Charge
			GROUP BY Charge.fk_accountID
		) charge_table ON acc.accountID = charge_table.fk_accountID
    ) account_detail ON Account.accountID = account_detail.accountID
    WHERE BALANCE > 0
    ORDER BY account_detail.status, account_detail.student_1";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

//get authorizations
function getAuthorizations($db,$id,$year){
    $query = 
        "SELECT ccaID,studentID,last_name,first_name,assistance,CCA.start_date,CCA.end_date,PT,FT,NLPS_tuition,state_payment,(NLPS_tuition - state_payment) AS tuition,CCA.note
        FROM CCA,Student
        WHERE fk_accountID = $id AND studentID = fk_studentID AND (YEAR(CCA.start_date) = $year OR YEAR(CCA.end_date) = $year)
        ORDER BY last_name,first_name,CCA.end_date DESC";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

//get student names from account
function getStudentnames($db,$aid,$mode){
    if($mode == 'all')
        $query = "SELECT last_name,first_name FROM Student WHERE fk_accountID = $aid";
    else
        $query = "SELECT last_name,first_name FROM Student WHERE fk_accountID = $aid and status = '$mode'";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Student Data!<br>\n";
        echo "Error: ". mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}


//requires config.php
//shows account by id
function showAccountByID($db,$aid){
    $data = getAccountByID($db,$aid);

    echo "<form method='POST'>";
    echo "<table class='data' align=\"center\" >";

    //display the fields
    $numfields = count($data['fields'] ?? []) + 2;
    echo "<tr><th colspan=$numfields>Account Information</th></tr>\n";
    echo "<tr>\n";
    foreach($data['fields'] as $f){
        if(strpos($f->name,'ID') !== false){
            $pos = strpos($f->name,'ID');
            $newstr = substr_replace($f->name, " ", $pos, 0);
            echo "<th>". $newstr ."</th>\n";
        }else{
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
        }
    }
    echo "<th colspan=2></th>";
    echo "</tr>";

    //display the body 
    echo "<tr class='data'>\n";
    foreach($data['fields'] as $f){
        echo "<td>".$data['data'][0][$f->name]."</td>\n";
    }
    echo "<td title='Update Row'><button formaction='update/search_update.php' name='id' value=$aid><img style='width: 10px; height: 10px;' src='/images/edit.png'></button></td>\n";
    echo "<td title='Delete Row'><button formaction='delete/search_delete.php' name='id' value=$aid><img style='width: 10px; height: 10px;' src='/images/x_mark.png'></button></td>\n";

    //get the balance and show it 
    echo "</tr>\n";
    echo "</table>\n";
    echo "</form>\n";
}

function showAccountData($db,$data,$fields,$mode){

    $found = count($data ?? []);

    echo "<u>$found Accounts found</u><br>\n";
    echo "<form method='post'>\n";
    echo "<table id='data_table' class='data' style='width:100%;'>";
    
    //display the fields
    echo "<tr>\n";
    $flag = 1;
    foreach($fields as $f){
        if($f->name == 'aid'){
            echo "<th>aid</th>\n";
        }elseif(strpos($f->name,'ID') !== false){
            $pos = strpos($f->name,'ID');
            $newstr = substr_replace($f->name, " ", $pos, 0);
            echo "<th>". $newstr ."</th>\n";
        //hidden data field
        }elseif($f->name == 'AUTO' or $f->name == 'autopay' or $f->name =='DROPIN'){
        }else{
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
        }
    }
    echo "</tr>";

    //display the body 
    foreach($data as $row){
        //get the accountID
        $aid = $row['aid'];
        $auto = $row['AUTO'];
        $drop = $row['DROPIN'];
        echo "<tr>\n";

        //print each column value
        foreach($fields as $f){
            if($f->name == 'aid'){
                echo "<td onclick=\"post('viewDetails.php',{id:$aid})\">".$row[$f->name]."</td>\n";
            }elseif(strpos($f->name,'date') !== false OR $f->name == 'DOB' OR $f->name == 'cca end date'){
                if($row[$f->name] != ''){
                    $date = new DateTime($row[$f->name]);
                    echo "<td title='$title' onclick=\"post('viewDetails.php',{id:$aid})\">" . $date->format('m-d-Y')  ."</td>\n";
                }else
                    echo "<td></td>";
            }elseif($f->name == 'BALANCE' or $f->name == 'next month' or $f->name == 'this month'){
                $var = $row[$f->name];
                if($auto == 'Yes')
                    echo "<td style='background-color:#f49542;' onclick=\"post('viewDetails.php',{id:$aid})\">";
                else
                    echo "<td onclick=\"post('viewDetails.php',{id:$aid})\" wrap>";
                echo $var."</td>\n";
            }elseif($f->name == 'Students_On_Account'){
                $student_data = getStudentnames($db,$aid,$mode);
                $var = "";
                foreach($student_data['data'] as $sname){
                    $f_name = $sname['first_name'];
                    $l_name = $sname['last_name'];
                    $full_name = " " .$l_name . ", ". $f_name;
                    $var = $var . $full_name . " /";
                }
                $var = substr($var,0,-1);

                if($drop == 'Yes')
                    echo "<td style='background-color:#42ebf4;' onclick=\"post('viewDetails.php',{id:$aid})\">";
                else
                    echo "<td onclick=\"post('viewDetails.php',{id:$aid})\" wrap>";
                echo $var."</td>\n";
            }elseif($f->name == 'AUTO' or $f->name == 'autopay' or $f->name == 'DROPIN'){
            }elseif($f->name == 'note' or $f->name == 'Note'){
                $val = $row[$f->name];
                echo "<td onclick=\"cellForm('$val','note','$aid')\" id='$aid'>$val</td>\n";
            }elseif($f->name == 'auth'){
                if(substr($row[$f->name],-2) == '-n')
                    echo "<td style='color:red;'>".$row[$f->name]."</td>\n";
                else
                    echo "<td style='color:green;'>".$row[$f->name]."</td>\n";
            }else{
                echo "<td onclick=\"post('viewDetails.php',{id:$aid})\">".$row[$f->name]."</td>\n";
            }
        }

        //Button to show detailed information and add payments and charges
        echo "</tr>\n";
    }

    echo "</table>\n";
    echo "</form>\n";
}

function showLateAuthorization($data,$fields){

    $found = count($data ?? []);

    echo "<u>$found Accounts found</u><br>\n";
    echo "<form method='post'>\n";
    echo "<table class='data' align=\"center\">";
    
    //display the fields
    echo "<tr>\n";
    foreach($fields as $f){
        if(strpos($f->name,'ID') !== false){
            $pos = strpos($f->name,'ID');
            $newstr = substr_replace($f->name, " ", $pos, 0);
            echo "<th>". $newstr ."</th>\n";
        }elseif($f->name == 'last_account_cca'){
            echo "<th>Last Contract Date</th>\n";

        }else{
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
        }
    }
    echo "</tr>";

    //display the body 
    foreach($data as $row){

        $note = $row['note'];
        $aid = $row['accountID'];
        echo "<tr class='data' onclick=\"post('viewDetails.php',{id:$aid})\">\n";
        $strtime = $row['last_account_cca'];
        $time = new DateTime($strtime);
        $beg_lastmonth = new DateTime('first day of last month');
        $nextmonth = new DateTime();
        $nextmonth->add(new DateInterval('P30D'));
        foreach($fields as $f){

            if(strpos($f->name,'date') !== false OR $f->name == 'DOB' OR $f->name == 'cca end date'){
                if($row[$f->name] != ''){
                    $date = new DateTime($row[$f->name]);
                    $output = $date ? $date->format('m-d-Y') : "";
                }else
                    $output = '';
            }else
                $output = $row[$f->name];

            if(($strtime == '' or $time < $beg_lastmonth) and strtolower($note) != 'drop in'){
                echo "<td style='background-color: #fc5a5a;'>".$output."</td>\n";
            }elseif(strtolower($note) != 'drop in' and $time <= $nextmonth){
                echo "<td style='background-color:white;'>".$output."</td>\n";
            }else{
                echo "<td style='background-color:white;'>".$output."</td>\n";
            }
        }

        //get the account id
        $aid = $row['accountID'];

        echo "</tr>\n";
    }

    echo "</table>\n";
    echo "</form>\n";
}

//show detailed information of charges and payments of a particular account
function showDetailedAccount($db,$aid,$year){
    echo "<form method='POST'>";


    echo "<table class='detailview' style='font-size: 10px;'>";

    //get cca information and show it
    echo "<tr>";
    $data = getAuthorizations($db,$aid,$year);
    echo "<td colspan='3'>";
    echo "<table class='data' style='font-size:12px;'>\n";
    $numfields = count($data['fields'] ?? []) + 2;
    echo "<tr><th colspan=$numfields style='margin-top:10px;'><font size='5'>Child Care Contracts</font></th></tr>";
    echo "<tr>";
    foreach($data['fields'] as $f){
        echo "<th>".$f->name."</th>\n";
    }
    echo "<th colspan=2></th>";
    echo "</tr>\n";
    foreach($data['data'] as $row){

        $sid = $row['studentID'];
        $ccaID = $row['ccaID'];

        echo "<tr onclick=\"post('/student/cca/update/search_update.php',{'id':$ccaID})\">\n";
        foreach($data['fields'] as $f){
            if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                if($row[$f->name] != ''){
                    $date = new DateTime($row[$f->name]);
                    echo "<td title='$title'>" . $date->format('m-d-Y')  ."</td>\n";
                }else
                    echo "<td></td>\n";
            }else
                echo "<td nowrap>".$row[$f->name]."</td>\n";
        }

        echo "<td title='Update Row'><button formaction='/student/cca/update/search_update.php' name='id' value=$ccaID><img style='width: 20px; height: 20px;' src='/images/edit.png'></button></td>\n";
        echo "<td title='Delete Row'><button formaction='/student/cca/delete/search_delete.php' name='id' value=$ccaID><img style='width: 20px; height: 20px;' src='/images/x_mark.png'></button></td>\n";

        echo "</tr>";
    }
    echo "</table>";
    echo "<br><br>";
    echo "<button class='button' style='width:200px; height:50px;' formaction='/student/cca/add/addCCA_page.php' name='aid' value=$aid>Add New Contract</button>";
    echo "</td>";
    echo "</tr>";
    //end view row

    //get student data
    echo "<td rowspan=2 style='vertical-align:top; padding:5px; text-align: left;'>";
    $query = "SELECT * FROM Student WHERE fk_accountID = $aid";
    $result = mysqli_query($db,$query);
    echo "<div class='print_hide'>";
    echo "<div>";
    echo "<button class='button' name='aid' value=$aid style='width:200px; height:50px; margin-left:0px; margin-right:auto;' formaction='/student/add/addStudent_page.php'>Add New Student</button>";
    echo "</div>";
    while($row = mysqli_fetch_array($result)){
        $sid = $row['studentID'];
        $fields = mysqli_fetch_fields($result);
        echo "<table class='data' style='float:left; font-size: 12px; margin-top: 5px;'  align=\"left\">";
        echo "<tr nowrap>";
        echo "<th colspan=2 nowrap>";
        echo "<button formaction='/student/update/search_update.php' name='id' value=$sid><img style='width:20px; height: 20px;' src=\"/images/edit.png\"></button>\n";
        //echo "<button formaction='/student/delete/search_delete.php' name='id' value=$sid><img style='width:20px; height:20px;' src=\"/images/x_mark.png\"></button>\n";
        echo "<font size='4'>Student Information</font>\n";
        echo "</th></tr>";
        foreach($fields as $f){
            echo "<tr ondblclick=\"post('/student/update/search_update.php',{'id':$sid})\">\n";
            if(strpos($f->name,'ID') !== false){
                $pos = strpos($f->name,'ID');
                $newstr = substr_replace($f->name, " ", $pos, 0);
                echo "<th>". $newstr ."</th>\n";
            }else{
                echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
            }
            if(strpos($f->name,'date') !== false or $f->name == 'DOB'){
                echo "<td>";
                $str = $row[$f->name];
                if($str != ''){
                    $date = new DateTime($str);
                    $val = $date ? $date->format('m-d-Y') : "";
                }else
                    $val = '';
            }elseif($f->name == 'mailing_address' or strpos($f->name,'mailing_address') !== false){
                $val = $row[$f->name];
                echo "<td class='mailingaddress' style='max-width:100px; white-space:normal;'>";
            }else{
                echo "<td>";
                $val = $row[$f->name];
            }
            echo "$val</td>\n";
            echo "</tr>";
        }
        echo "</table>\n";
    }
    echo "</div>";
    echo "</td>";

    //display the account information
    echo "<td style='vertical-align:top;' >";
    if($aid != ''){
        //show the account
        $a_data = getAccountByID($db,$aid);
        echo "<div style='margin-left:auto; margin-right:auto; padding:2px;'>";
        echo "<table class='data' style='padding: 0px;' align=\"center\">";
        echo "<tr><th colspan=9><font size='5'>Account Information</font></th></tr>";

        //show data
        foreach($a_data['data'] as $row){
            //show columns
            echo "<tr>";
            foreach($a_data['fields'] as $f){
                $name = $f->name;
                $val = $row[$f->name];
                if($val != '' or $name == 'note')
                    echo "<th>$name</th>";
            }
            echo "<th></th>";
            echo "</tr>";

            echo "<tr onclick=\"post('/account/update/search_update.php',{'id':$aid})\">";
            foreach($a_data['fields'] as $f){
                $val = $row[$f->name];
                if($val != '' or $f->name == 'note')
                    echo "<td>$val</td>";
            }

            echo "<td><button formaction='/account/update/search_update.php' name='id' value=$aid><img style='width:20px; height:20px;' src=\"/images/edit.png\"></button></td>\n";
            echo "</tr>";
        }
        echo "</tr>";
        echo "</table>";
        echo "</div>";
    }else{
        echo "<div>";
        echo "<h3>This Child Does Not Have an Account</h3>";
        echo "<button class='button' formaction ='/account/add/addAccount_page.php' name='id' value=$sid>Create A New Account</button>";
        echo "</div>";
    }

    //separate the account from the payment info
    echo "<br><br>";
    echo "<hr><hr>";
    echo "<br><br>";

    //initialize variables
    $payment_data = array();
    $payment_fields;
    $num_payments;
    $charge_data = array();
    $charge_fields;
    $num_charges;

    //get the data of payments and charges for these kids
    if($sid != ''){
        $query = 
            "SELECT Payment.paymentID as `id`,Payment.date,Payment.amount,Payment.description,'payment' as type FROM Payment,Student,Account
            WHERE accountID = Student.fk_accountID AND accountID = Payment.fk_accountID AND studentID = $sid AND YEAR(date) = $year
            UNION
            SELECT Charge.chargeID `id`,Charge.date,Charge.amount,Charge.description,'charge' as type FROM Charge,Student,Account
            WHERE accountID = Student.fk_accountID AND accountID = Charge.fk_accountID AND studentID = $sid AND YEAR(date) = $year
            ORDER BY date";
        $query2 = 
            "SELECT Student.fk_accountID,SUM(amount) as total_payment FROM Payment,Student,Account 
            WHERE accountID = Student.fk_accountID AND accountID = Payment.fk_accountID AND studentID = $sid AND YEAR(date) = $year";
        $query3 = 
            "SELECT Student.fk_accountID,SUM(amount) as total_charge FROM Charge,Student,Account
            WHERE accountID = Student.fk_accountID AND accountID = Charge.fk_accountID AND studentID = $sid AND YEAR(date) = $year";
        $query_prev_payment = 
            "SELECT Student.fk_accountID,SUM(amount) as total_payment FROM Payment,Student,Account 
            WHERE accountID = Student.fk_accountID AND accountID = Payment.fk_accountID AND studentID = $sid AND YEAR(date) <= ($year - 1)";
        $query_prev_charge = 
            "SELECT Student.fk_accountID,SUM(amount) as total_charge FROM Charge,Student,Account
            WHERE accountID = Student.fk_accountID AND accountID = Charge.fk_accountID AND studentID = $sid AND YEAR(date) <= ($year - 1)";
        $result = mysqli_query($db,$query);
        $result2 = mysqli_query($db,$query2);
        $result3 = mysqli_query($db,$query3);
        $prev_payment_result = mysqli_query($db,$query_prev_payment);
        $prev_charge_result = mysqli_query($db,$query_prev_charge);

        //make sure all queries were successful
        if($result !== false and $result2 !== false and $result3 !== false){
            $fields = mysqli_fetch_fields($result);
            $data1 = array();
            $data2 = array();
            $data3 = array();
            $prev_payment_data = array();
            $prev_charge_data = array();

            //store the results into arrays
            while($row = mysqli_fetch_array($result))
                $data1[] = $row;
            while($row = mysqli_fetch_array($result2))
                $data2[] = $row;
            while($row = mysqli_fetch_array($result3))
                $data3[] = $row;
            while($row = mysqli_fetch_array($prev_payment_result))
                $prev_payment_data[] = $row;
            while($row = mysqli_fetch_array($prev_charge_result))
                $prev_charge_data[] = $row;

            $payment_total = $data2[0]['total_payment'];
            $charge_total = $data3[0]['total_charge'];
            $prev_balance = $prev_charge_data[0]['total_charge'] - $prev_payment_data[0]['total_payment'];


            //start another new view row
            echo "<div style='display: table;'>";
            echo "<table style='table-layout:fixed; width:100%;' class='data'>\n";

            //make table headers for the balance
            echo "<tr><th colspan=7><font size='5'>Payment Information</font></th></tr>";
            echo "<tr>\n";
            echo "<th></th>";
            echo "<th>Date</th>";
            echo "<th>Description</th>";
            echo "<th>Payment Amount</th>";
            echo "<th>Charge Amount</th>";
            echo "<th>BALANCE</th>";
            echo "</tr>\n";

            //get previous year's balance
            $BALANCE = 0;
            if($prev_balance != 0)
                echo "<tr><td></td><td></td><td>Previous Balance</td><td></td><td style='color:#ff0000;' >$ $prev_balance</td><td>$ $prev_balance</td></tr>";

            $BALANCE += $prev_balance;
            $charge_total += $prev_balance;

            //loop through each payment/charge
            foreach($data1 as $row){
                $id = $row['id'];
                if($row['type'] == 'payment'){
                    echo "<tr onclick=\"post('/account/payment/update/search_update.php',{'id':$id})\">\n";
                    echo "<td><button formaction='/account/payment/delete/search_delete.php' name='id' value=$id><img style='width:20px; height:20px;' src=\"/images/x_mark.png\"></button></td>\n";
                }elseif($row['type'] == 'charge'){
                    echo "<tr onclick=\"post('/account/charge/update/search_update.php',{'id':$id})\">\n";
                    echo "<td><button formaction='/account/charge/delete/search_delete.php' name='id' value=$id><img style='width:20px; height:20px;' src=\"/images/x_mark.png\"></button></td>\n";
                }else
                    echo "<tr>\n";

                //delete button

                $tmp = new DateTime($row['date']);
                $str_date = $tmp->format('m-d-Y');
                echo "<td>$str_date</td>";
                echo "<td>".$row['description']."</td>";
                if($row['type'] == 'payment'){
                    $BALANCE = $BALANCE - round($row['amount'],2);
                    echo "<td>\$ ".round($row['amount'],2)."</td>";
                    echo "<td></td>";
                }elseif($row['type'] == 'charge'){
                    $BALANCE = $BALANCE + round($row['amount'],2);
                    echo "<td></td>";
                    echo "<td style='color:#ff0000;'>\$ ".round($row['amount'],2)."</td>";
                }

                $BALANCE = round($BALANCE,2);
                echo "<td>\$$BALANCE</td>";
                echo "</tr>\n";
            }

            echo "<tr><th colspan=3>TOTAL</th><th>\$ $payment_total</th><th>\$ $charge_total</th><td></td></tr>\n";
            echo "<tr><th colspan=3>BALANCE</th><th colspan=2>\$ $BALANCE</th></tr>\n";
            echo "</table>\n";
            echo "</div>";
            echo "<br><br>";
            echo "<button class='button' style='width:200px; height:50px; margin-right:40px;' formaction='/account/addCharge.php' name='id' value=$aid>Add Charge</button>";
            echo "<button class='button' style='width:200px; height:50px; margin-left:40px;' formaction='/account/addPayment.php' name='id' value=$aid>Add Payment</button>";
            echo "</td></tr>\n";
        }else{
            echo "<tr><td>";
            echo "<h1>";
            echo "query: $sql<br>";
            echo "error: ". mysqli_error($db);
            echo "</h1>";
            echo "</td></tr>\n";
        }
    }

    echo "</table>";
    echo "</form>";
}


//get the last insert data not used and deprecated
function getLastInsertData($db){
    $sql = "SELECT * FROM Account WHERE accountID = LAST_INSERT_ID()";
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
function showDeleteableAccount($db,$data,$fields){
    $found = count($data ?? []);
    echo "<u>$found records found</u><br>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
        echo "<th>DELETE THIS account</th>\n";
    echo "</tr>";
        
    foreach($data as $row){
        echo "<tr>\n";
        $id = $row['accountID'];
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
        
        echo "<td><button class='circularsmall' style='background-color:#ff5050' name='id' value='$id'>--</button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}

//function to show Editable data given fields
function showEditableAccount($db,$data,$fields){

    //find all active students in the database
    $query = "SELECT studentID,first_name,last_name FROM Student ORDER BY last_name,first_name";
    $result = mysqli_query($db,$query);
    $s_data = array();
    if($result !== false){
        while($row = mysqli_fetch_array($result))
            $s_data[] = $row;
    }

    //show how many found
    $found = count($data ?? []);

        //create a table
        echo "<table class='editable' align=\"center\">";
        echo "<tr>\n";
        echo "</tr>\n";
        foreach ($fields as $f){
            echo "<tr>";
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";

        foreach($data as $row){
            $title = str_replace('_',' ',$f->name);
            echo "<td title='$title' style='text-align:left;' nowrap>\n";

            //save the id of each data row
            if($isPK){
                $isPK = false;
                $id = $row[$f->name];
            }

            if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                $val = $row[$f->name];
                echo "<input type='date' name=\"$f->name\" value='$val'>\n";
                echo "</td>\n";
            }elseif($f->name == 'file_location'){
                if($row[$f->name] == '')
                    echo "no digital file on record<br>";
                else{
                    $file = $row[$f->name];

                    $f_location = "/$file";
                    echo "<a align='center' href=\"$f_location\">$file</a><br>";
                }
                echo "<input align='center' type='file' name='fileToUpload$id' id='fileToUpload'>";
                echo "</td>\n";
            }elseif($f->name == 'age'){
                $age = $row[$f->name];
                echo "$age";
                echo "<input type='hidden' name='$f->name' value='$age'>";
            }elseif(strpos($f->name,'student_') !== false){
                $val = $row[$f->name];
                $status = $row['status'];
                echo "<select style='width:100%;' class='selectpicker' name='$f->name'>";
                if($status == 'inactive' AND $val != ''){
                    $tokens = explode(", ",$val);
                    $sdata = getStudentByName($db,$tokens[1],$tokens[0]);
                    $sid = $sdata['data'][0]['studentID'];
                    echo "<option val=$sid>".$tokens[0].", ". $tokens[1]."</option>";
                }else{
                    echo "<option></option>";
                }
                foreach($s_data as $row){
                    $fname = $row['first_name'];
                    $lname = $row['last_name'];
                    $sid = $row['studentID'];
                    $name = "$lname, $fname";
                    if($val == $name and $val != '')
                        echo "<option value=$sid selected>$name</option>";
                    else
                        echo "<option value=$sid>$name</option>";
                }
                echo "</td>\n";
            }elseif($f->name == "sex"){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>M</option>";
                echo "<option>F</option>";
                echo "</td>\n";
            }elseif($f->name == "allow_picture"){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>yes</option>";
                echo "<option>no</option>";
                echo "</td>\n";
            }elseif($f->name == "status" AND ($row[$f->name] == "Employee" OR $row[$f->name] == "Student")){
                echo "".$row[$f->name]."";
                echo "<input type='hidden' value='".$row[$f->name]."' name='$f->name'>";
                echo "</td>\n";
            }elseif($f->name == "status"){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>active</option>";
                echo "<option>inactive</option>";
                echo "</td>\n";
            }elseif($f->name == 'autopay' or $f->name == 'AUTO'){
                echo "<select class='selectpicker' name='autopay'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>Yes</option>";
                echo "<option value=''>No</option>";
                echo "</td>\n";
            }elseif($f->name == 'drop_in' or $f->name == 'dropin'){
                echo "<select class='selectpicker' name='drop_in'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>Yes</option>";
                echo "<option value=''>No</option>";
                echo "</td>\n";
            }elseif($f->name == 'PT' OR $f->name == 'FT'){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option>FT MONTH</option>\n";
                echo "<option>PT MONTH</option>\n";
                echo "<option selected>".$row[$f->name]."</option>\n";
                for($i = 1; $i < 32; $i++){
                    echo "<option>$i</option>\n";
                }
                echo "</td>";
            }elseif($f->name == 'expected_tuition' or $f->name == 'NLPS_tuition' or $f->name == 'state_payment'){
                $val = $row[$f->name];
                echo "$ <input style='width:60%;' value='$val' vatype=\"text\" name=\"$f->name\"></td>";
            }elseif($f->name == "type"){
                echo "<select class='selectpicker' style='width:100%;' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>clock in</option>";
                echo "<option>clock out</option>";
                echo "</td>\n";
            }elseif($f->name == "auth_type"){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>C</option>";
                echo "<option>S</option>";
                echo "<option>O</option>";
                echo "</td>\n";
            }elseif($f->name == 'days_of_week'){
                $days = explode(',',$row[$f->name]);
                $dname = array();
                $dname[0] = 'SUN';$dname[1] = 'MON';$dname[2] = 'TUE';
                $dname[3] = 'WED';$dname[4] = 'THU';$dname[5] = 'FRI';
                $dname[6] = 'SAT';

                echo "<table border=none>";
                echo "<tr>";
                foreach($dname as $d)
                    echo "<td>$d</td>";
                echo "</tr>";
                echo "<tr>";

                foreach($dname as $d){
                    $found = false;
                    $name = strtolower($d);
                    $str ='';
                    foreach($days as $d_actual){
                        if($d_actual == "$name"){
                            $found = true;
                            $str = $d_actual;
                            break;
                        }
                    }

                    if($found == true){
                        echo "<td><input type='checkbox' name='$name' value='y' checked></td>\n";
                    }else{
                        echo "<td><input type='checkbox' name='$name' value='y'></td>\n";
                    }
                }
                
                echo "</tr>";
                echo "</table>";
                echo "</td>";
            }elseif($f->name == 'out_in'){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>+</option>";
                echo "<option>-</option>";
                echo "</td>\n";
            }elseif($f->name == "authorization"){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>P1</option>";
                echo "<option>P2</option>";
                echo "<option>CITC</option>";
                echo "<option>OCS</option>";
                echo "<option>SELF</option>";
                echo "<option>P1-n</option>";
                echo "<option>P2-n</option>";
                echo "<option>CITC-n</option>";
                echo "<option>OCS-n</option>";
                echo "</td>\n";
            }elseif($f->name == "assistance"){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>P1</option>";
                echo "<option>P2</option>";
                echo "<option>CITC</option>";
                echo "<option>OCS</option>";
                echo "<option>SELF</option>";
                echo "</td>\n";
            }elseif($f->name == 'tuition_type'){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>Full</option>";
                echo "<option>Part</option>";
                echo "</td>\n";
            }elseif($f->name == 'time' or strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false or $f->name == 'lunch_in' or $f->name == 'lunch_out'){
                $tmp = $row[$f->name];
                if($tmp == '')
                    $time = '';
                else
                    $time = new DateTime($row[$f->name]);

                if($time == ''){
                    echo "<input class='time' maxlength='2' type='text' value=\"\" name=\""."$f->name"."[hour]\">:\n";
                    echo "<input class='time' maxlength='2' type='text' value=\"\" name=\""."$f->name"."[min]\">\n";
                    echo "<select class='selectpicker' style='width:70px;' name='time_ext$f->name'>";
                    echo "<option value='' selected></option>";
                    echo "<option>AM</option>\n";
                    echo "<option>PM</option>\n";
                    echo "</select>";
                    echo "</td>";
                }else{
                    echo "<input class='time' maxlength='2' type='text' value=\"" . $time->format('h')  ."\" name=\""."$f->name"."[hour]\">:\n";
                    echo "<input class='time' maxlength='2' type='text' value=\"" . $time->format('i')  ."\" name=\""."$f->name"."[min]\">\n";
                    echo "<select class='selectpicker' style='width:70px;' name='time_ext$f->name'>";
                    echo "<option value='".$time->format('A')."' selected>".$time->format('A')."</option>";
                    echo "<option>AM</option>\n";
                    echo "<option>PM</option>\n";
                    echo "</select>";
                    echo "</td>";
                }
            }elseif(strpos($f->name,'ID') !== false){
                echo "" . $row[$f->name];
                echo "<input type='hidden' value='".$row[$f->name]."' name='$f->name'>";
                echo "</td>\n";
            }elseif(strpos($f->name, 'room') !== false){
                echo "<select class='selectpicker' name='$f->name'>";
                echo "<option selected>".$row[$f->name]."</option>";
                echo "<option>Purple</option>";
                echo "<option>Rainbow</option>";
                echo "<option>Orange</option>";
                echo "<option>Green</option>";
                echo "<option>Yellow</option>";
                echo "<option>Pink</option>";
                echo "<option>Blue</option>";
                echo "<option>Red</option>";
                echo "<option>S.A 1-3</option>";
                echo "<option>S.A 4-6</option>";
                echo "<option>Saturday</option>";
                echo "</td>\n";
            }else{
                echo "<input class='editable' type='text' value=\"".$row[$f->name]."\" name='$f->name'></td>\n";
            }
        }
            echo "</tr>";
        }

        echo "</table>";
        echo "<br><br>";
}
//function to show the add form
function showAddAccountForm($db,$data,$fields,$sdata){

    //initialize variables
    $id = $sdata['data'][0]['studentID'];
    $name = $sdata['data'][0]['last_name'] . ", " . $sdata['data'][0]['first_name'];

    //find all active students in the database
    $query = "SELECT studentID,first_name,last_name FROM Student WHERE status = 'active' ORDER BY last_name,first_name";
    $result = mysqli_query($db,$query);
    $s_data = array();
    if($result !== false){
        while($row = mysqli_fetch_array($result))
            $s_data[] = $row;

    //$s_fields = mysqli_fetch_fields($result);
    }else
        echo "<h1>ERROR</h1>";

    echo "<TABLE class='form' BORDER=\"1\">";

    //Show form for adding an student
    foreach($fields as $field){
        echo "<tr>";
        $fname = $field->name;

        //parse the row name for proper output
        if(strpos($field->name,'ID') !== false){
            $pos = strpos($field->name,'ID');
            $newstr = substr_replace($field->name, " ", $pos, 0);
            echo "<th>$newstr</th>\n";
        }else{
            echo "<th>". str_replace('_',' ',$field->name) ."</th>\n";
        }

        //create forms body
        if(strpos($field->name,"ID") !== false OR strpos($field->name,'fk_') !== false or $field->name == "age"){
            echo "<td align=\"center\">Do not use</td>";
        }elseif(strpos($field->name,'student_1') !== false){
            echo "<td>";
            echo "<select style='width:100%;' class='selectpicker' name='$field->name'>";
            echo "<option value=$id selected>$name</option>";
            foreach($s_data as $row){
                $fname = $row['first_name'];
                $lname = $row['last_name'];
                $sid = $row['studentID'];
                $name = "$lname, $fname";
                echo "<option value=$sid>$name</option>";
            }
            echo "</td>\n";
        }elseif(strpos($field->name,'student_') !== false){
            echo "<td>";
            echo "<select style='width:100%;' class='selectpicker' name='$field->name'>";
            echo "<option selected></option>";
            foreach($s_data as $row){
                $fname = $row['first_name'];
                $lname = $row['last_name'];
                $sid = $row['studentID'];
                $name = "$lname, $fname";
                echo "<option value=$sid>$name</option>";
            }
            echo "</td>\n";
        }elseif($field->name == 'autopay'){
            echo "<td>";
            echo "<div><input style='position:relative; float:left;width:20px;height:20px;' type='checkbox' name='$field->name' value='yes'> YES / ";
            echo "<input style='position:relative; float:left; width:20px;height:20px;' type='checkbox' name='$field->name' value='' checked> NO</div>";
            echo "</td>";
        }elseif($field->name == 'drop_in'){
            echo "<td>";
            echo "<div><input style='position:relative; float:left;width:20px; height:20px;'  type='checkbox' name='$field->name' value='yes'> YES /";
            echo "<input style='position:relaative; float:left; width:20px; height: 20px; ' type='checkbox' name='$field->name' value='' checked> NO</div>";
            echo "</td>";
        }elseif($field->name == 'status'){
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>
                <option>active</option>
                <option>inactive</option>";
            echo "</td>";
        }elseif($field->name == 'authorization'){
            $auth_type = $sdata['data'][0]['auth_type'];
            echo "<td align='center'><select style='width:100%;' class='selectpicker' name='$field->name'>
                <option>$auth_type</option>
                <option>P1</option>
                <option>P2</option>
                <option>CITC</option>
                <option>OCS</option>
                <option>SELF</option>
                <option>P1-n</option>
                <option>P2-n</option>
                <option>CITC-n</option>
                <option>OCS-n</option>";
            echo "</td>";
        }else{
            echo "<td align=\"center\"><input style='width:100%;' type=\"text\" name=\"$field->name\"></td>";
        }

        //output if field is required or not
        if($field->flags & 1 AND strpos($field->name,'ID') == false){
            echo "<td><b>REQUIRED</b></td>";
        }
        echo "</tr>";
    }

    //Close the table and form
    echo "</TABLE><br>\n";
}

//show detailed Student view
function showDetailedStudent($db,$sid,$year=null){
    $data = getStudentByID($db,$sid);

    echo "<form method='POST'>";
    echo "<table class='detailview'>";
    echo "<tr>";
    echo "<td rowspan=3>";
    echo "<table class='data' style='float:left; margin-left:5%;'  align=\"left\">";

    //display the student information
    echo "<tr nowrap><th colspan=2 nowrap><font size='5'>Student Information</font>\n";
    echo "<button formaction='/student/update/search_update.php' name='id' value=$sid><img style='width: 30px; height: 30px;' src=\"/images/edit.png\"></button>\n";
    echo "<button formaction='/student/delete/search_delete.php' name='id' value=$sid><img style='width:30px; height:30px;' src=\"/images/x_mark.png\"></button>\n";
    echo "</th></tr>";
    foreach($data['fields'] as $f){
        echo "<tr onclick=\"post('/student/update/search_update.php',{'id':$sid})\">\n";
        //display the field
        if(strpos($f->name,'ID') !== false){
            $pos = strpos($f->name,'ID');
            $newstr = substr_replace($f->name, " ", $pos, 0);
            echo "<th>". $newstr ."</th>\n";
        }else{
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
        }

        //display value
        echo "<td>";
        if(strpos($f->name,'date') !== false or $f->name == 'DOB'){
            $str = $data['data'][0][$f->name];
            if($str != ''){
                $date = new DateTime($str);
                $val = $date ? $date->format('m-d-Y') : "";
            }else
                $val = '';
        }elseif($f->name == 'mailing_address' or strpos($f->name,'mailing_address') !== false){
            $val = $row[$f->name];
        }else{
            $val = $data['data'][0][$f->name];
        }
        echo "$val</td>";
        echo "</tr>";
    }

    echo "</table>\n";
    echo "</div>";
    echo "</td>";
    
    //end view row
    echo "</tr>";

    //display the account information
    echo "<tr><td style='align:top;'>";
    $aid = $data['data'][0]['fk_accountID'];
        echo "<div>";
        echo "<h2>NO FINANCIAL ACCOUNT EXISTS</h2>";
        echo "<br><br><button class='button' formaction ='/account/add/addAccount_page.php' name='id' value=$sid>Create New Account</button>";
        echo "</div>";
    echo "</td></tr>";

    echo "</table>";
}

?>
