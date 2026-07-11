
<?php

function basic_query(){

    $sql1 = "SELECT ccaID,fk_studentID,first_name,last_name,assistance,NLPS_tuition,state_payment,CCA.start_date,CCA.end_date,state_payment,PT,FT FROM CCA";
    $sql2 = "INNER JOIN Student ON fk_studentID = studentID";
    $sql3 = "ORDER BY last_name,end_date DESC";

    return "$sql1 $sql2 $sql3";
}

//get late authorizations
function queryExpiringAuthorization($db){

	$query= "SELECT fk_accountID,studentID,monthly_tuition.ccaID,first_name,last_name,status,auth_type,FT,PT,monthly_tuition.start_date as 'cca start', monthly_tuition.end_date as 'cca end',assistance,NLPS_tuition,state_payment,tuition
	FROM Student
	LEFT OUTER JOIN
		(
			SELECT DISTINCT(t1.fk_studentID),t1.ccaID,t1.NLPS_tuition,t1.assistance,t1.state_payment,t1.FT,t1.PT,t1.start_date,t1.end_date,(t1.NLPS_tuition - t1.state_payment) as tuition FROM CCA as t1
			INNER JOIN
			(
                SELECT fk_studentID,MAX(end_date) as `last_auth` FROM CCA
				GROUP BY fk_studentID
			) t2 ON t1.fk_studentID = t2.fk_studentID AND `last_auth` = t1.end_date
            WHERE t1.end_date >= DATE(NOW())
		) monthly_tuition ON studentID = fk_studentID
	WHERE status = 'active' AND (monthly_tuition.end_date <= DATE_ADD(NOW(), INTERVAL 21 DAY) OR monthly_tuition.end_date IS NULL)
    ORDER BY last_name,monthly_tuition.end_date DESC";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
        echo "Error: " .mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

//get current authorizations
function getFutureCCA($db){
	$sql = "SELECT fk_accountID,studentID,monthly_tuition.ccaID,last_name,first_name,status,auth_type,assistance,state_payment,NLPS_tuition,FT,PT,monthly_tuition.start_date as 'cca start', monthly_tuition.end_date as 'cca end',tuition
	FROM Student
	LEFT OUTER JOIN
		(
			SELECT DISTINCT(t1.fk_studentID),t1.ccaID,t1.NLPS_tuition,t1.assistance,t1.state_payment,t1.FT,t1.PT,t1.start_date,t1.end_date,(t1.NLPS_tuition - t1.state_payment) as tuition FROM CCA as t1
			INNER JOIN
			(
                SELECT fk_studentID,MAX(end_date) as `last_auth` FROM CCA
                WHERE end_date >= DATE(NOW())
                GROUP BY fk_studentID
			) t2 ON t1.fk_studentID = t2.fk_studentID AND `last_auth` = t1.end_date
		) monthly_tuition ON studentID = fk_studentID
	WHERE status = 'active'
	ORDER BY last_name,monthly_tuition.end_date DESC";

    $data = array();
    $result = mysqli_query($db,$sql);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting CCA Data!<br>\n";
        echo "Error: " . mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function getCurrentCCA($db){
	$sql = "SELECT fk_accountID,studentID,monthly_tuition.ccaID,last_name,first_name,status,auth_type,assistance,state_payment,NLPS_tuition,FT,PT,monthly_tuition.start_date as 'cca start', monthly_tuition.end_date as 'cca end',tuition
	FROM Student
	LEFT OUTER JOIN
		(
			SELECT DISTINCT(t1.fk_studentID),t1.ccaID,t1.NLPS_tuition,t1.assistance,t1.state_payment,t1.FT,t1.PT,t1.start_date,t1.end_date,(t1.NLPS_tuition - t1.state_payment) as tuition FROM CCA as t1
			INNER JOIN
			(
				SELECT fk_studentID,MAX(end_date) as `last_auth` FROM CCA
                WHERE end_date >= DATE(NOW()) AND start_date <= DATE(NOW())
                GROUP BY fk_studentID
			) t2 ON t1.fk_studentID = t2.fk_studentID AND `last_auth` = t1.end_date
		) monthly_tuition ON studentID = fk_studentID
	WHERE status = 'active' 
	ORDER BY last_name,monthly_tuition.end_date DESC";

    $data = array();
    $result = mysqli_query($db,$sql);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting CCA Data!<br>\n";
        echo "Error: " . mysqli_error($db);
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function getCCAInactive($db){

	$sql = "SELECT fk_accountID,studentID,monthly_tuition.ccaID,first_name,last_name,status,auth_type,FT,PT,monthly_tuition.start_date as 'cca start', monthly_tuition.end_date as 'cca end',assistance,NLPS_tuition,state_payment,tuition
	FROM Student
	LEFT OUTER JOIN
		(
			SELECT DISTINCT(t1.fk_studentID),t1.ccaID,t1.NLPS_tuition,t1.assistance,t1.state_payment,t1.FT,t1.PT,t1.start_date,t1.end_date,(t1.NLPS_tuition - t1.state_payment) as tuition FROM CCA as t1
			INNER JOIN
			(
				SELECT fk_studentID,MAX(end_date) as `last_auth` FROM CCA
				GROUP BY fk_studentID
			) t2 ON t1.fk_studentID = t2.fk_studentID AND `last_auth` = t1.end_date
		) monthly_tuition ON studentID = fk_studentID
	WHERE status = 'inactive'
	ORDER BY last_name,monthly_tuition.end_date DESC";
    $data = array();

    $result = mysqli_query($db,$sql);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting CCA Data!<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}


//query students with same account
function queryStudentAccount($db,$id){

    $sql = "SELECT last_name,first_name,sex,DOB,age,start_date,room,status,auth_type,fk_accountID FROM Student
        WHERE fk_accountID = $id ORDER BY last_name";

    $data = array();

    $result = mysqli_query($db,$sql);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting CCA Data!<br>\n";
        echo "Error: ". mysqli_error($db)."<br>\n";
        echo "$sql<br>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function getLastInsertData($db){
    $sql = "SELECT * FROM CCA WHERE ccaID = LAST_INSERT_ID()";
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
function showDeleteableCCA($db,$data,$fields){

    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
        echo "<th>DELETE THIS cca</th>\n";
    echo "</tr>";
        
    foreach($data as $row){
        echo "<tr>\n";
        $id = $data['data'][0]['ccaID'];
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


//improved show data function which links to the update table if it exists
function showCCAData($data,$fields){
    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    echo "<form action='/upload.php' method='POST' >\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        if(strpos($f->name,'ID') !== false){
            $pos = strpos($f->name,'ID');
            $newstr = substr_replace($f->name, " ", $pos, 0);
            echo "<th>". $newstr ."</th>\n";
        }else{
            echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
        }
    }
    echo "</tr>";
    foreach($data as $row){
        //get the row id
        $id = $row['ccaID'];
        $aid = $row['fk_accountID'];

        echo "<tr class='data' onclick=\"post('/account/viewDetails.php',{id:$aid})\">\n";
        foreach($fields as $f){
            $title = str_replace('_',' ',$f->name);

            if(strpos($f->name,'date') !== false OR $f->name == 'cca start' OR $f->name == 'cca end' OR $f->name == 'DOB'){
                if($row[$f->name] != ''){
                    $date = new DateTime($row[$f->name]);
                    echo "<td title='$title'>" . $date->format('m-d-Y')  ."</td>\n";
                }else
                    echo "<td></td>\n";
            }elseif($f->name == 'file_location'){
                echo "<td style='width:160px;'>";
                if($row[$f->name] == ''){
                    echo "".$row[$f->name];
                }else{
                    $f_location = "/".$row[$f->name];
                    echo "<a href=\"$f_location\">".$row[$f->name]."</a><br>";
                }
                echo "</td>";
            }elseif($f->name == 'time' or strpos($f->name,'time') !== false or strpos($f->name,'Time') !== false){
                if($row[$f->name] != ''){
                    $time = new DateTime($row[$f->name]);
                    echo "<td title='$title'>" . $time->format('h:i:s A')  ."</td>\n";
                }else
                    echo "<td></td>\n";
            }else{
                echo "<td title='$title'>" . $row[$f->name] ."</td>\n";
            }
        }

        //Button to edit the data. Requires that there be an update file

        echo "</tr>\n";
    }
    echo "</table>";
    echo "</form>";
}

//show add cca form
function showAddCCA($db){
}
?>
