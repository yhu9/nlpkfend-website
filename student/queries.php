
<?php

function student_basic(){
    
    $sql1 = "SELECT * FROM Student";
    $sql3 = "ORDER BY status ASC,last_name ASC";

    $sql = "$sql1 $sql2 $sql3";

    return($sql);
}

function getStudentByRoom($db){
	$query = "SELECT fk_accountID,studentID,last_name,first_name,sex,DOB,age,start_date,end_date,physical_date,status,room,auth_type,tuition_type,allow_picture,phone_number,email
	FROM Student WHERE status = 'active'
	ORDER BY room";

    $data = array();

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>";
        echo "error: ". mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}


function getAllergies($db){
	$query = "SELECT fk_accountID,studentID,last_name,first_name,sex,DOB,age,physical_date as 'physical',status,phone_number as phone,email,allergy
	FROM Student WHERE status = 'active'
	ORDER BY last_name";

    $data = array();

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>";
        echo "error: ". mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function getStudentBasic($db){
	$query = "SELECT fk_accountID,studentID,last_name,first_name,sex,DOB,age,start_date as 'start',physical_date as 'physical',auth_type as 'auth',tuition_type as 'tuition',allow_picture as 'picture',CONCAT(parent1,' / ',IFNULL(parent2,'')) as parents,mailing_address,phone_number as phone,email
	FROM Student WHERE status = 'active'
	ORDER BY last_name";

    $data = array();

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>";
        echo "error: ". mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}


function getStudentsAll($db){
	$sql1 = "SELECT fk_accountID,studentID,last_name,first_name,sex,DOB,age,start_date,end_date,physical_date,status,room,auth_type,tuition_type,allow_picture,parent1,parent2,phone_number,email
	FROM Student";
    $sql3 = "ORDER BY status ASC, last_name ASC";

    $sql = "$sql1 $sql3";
    $data = array();

    $result = mysqli_query($db,$sql);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>";
        echo "error: ". mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function getStudentInactive($db){
	$query= "SELECT fk_accountID, studentID,last_name,first_name,sex,DOB,age,start_date,end_date,physical_date,room,auth_type,tuition_type,allow_picture,phone_number,email
	FROM Student WHERE status = 'inactive'
	ORDER BY last_name";

    $data = array();
    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>";
        echo "error: ". mysqli_error($db);
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function queryLateAuthorization($db){
	$query = "SELECT fk_accountID,Student.studentID,first_name,last_name,Student.age,Student.DOB,Student.room,Student.auth_type,Account.authorization,Account.note,`cca end date` FROM Student
	LEFT OUTER JOIN
	(
		SELECT MAX(end_date) as `cca end date`,fk_studentID FROM CCA
		GROUP BY fk_studentID
	) cca_table ON Student.studentID = cca_table.fk_studentID
	INNER JOIN Account ON Student.fk_accountID = accountID
	WHERE Student.status = 'active'
	ORDER BY last_name,`cca end date`";

    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result)){
            $data[] = $row;
        }
    }else{
        echo "Error getting Account Data!<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function getExpiredPhysical($db){
	$query = 
	"SELECT fk_accountID,studentID,last_name,first_name,sex,DOB,age,start_date,physical_date,room,phone_number,email FROM Student
	WHERE
		age <= 6 AND
		physical_date < DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), INTERVAL 1 YEAR) AND
		status = 'active'
	UNION
	SELECT fk_accountID,studentID,last_name,first_name,sex,DOB,age,start_date,physical_date,room,phone_number,email FROM Student
	WHERE
		age > 6 AND
		physical_date < DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 1 MONTH), INTERVAL 2 YEAR) AND
		status = 'active'";

    $data = array();
    $result = mysqli_query($db,$query);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>";
        echo "mysql error: ". mysqli_error($db)."<br>\n";
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

function getLastInsertData($db){
    $sql = "SELECT * FROM Student WHERE studentID = LAST_INSERT_ID()";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "<h1>";
        echo "query: $sql<br>";
        echo "Error getting last Insert Data!<br>\n";
        echo "</h1>";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//show the students with expiring or expired authorization
function showLateAuthorization($db){

    $data = queryLateAuthorization($db);
    $found = count($data['data']);

    echo "<u>$found Accounts found</u><br>\n";
    echo "<form method='post'>\n";
    echo "<table class='data' align=\"center\">";
    
    //display the fields
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
    echo "</tr>";

    //display the body 
    foreach($data['data'] as $row){
        $note = $row['note'];
        $sid = $row['fk_accountID'];
        echo "<tr class='data' onclick=\"post('/account/viewDetails.php',{id:$sid})\">\n";
        $strtime = $row['cca end date'];
        $time = new DateTime($strtime);
        $now = new DateTime();
        $nextmonth = new DateTime();
        $nextmonth->add(new DateInterval('P21D'));
        foreach($data['fields'] as $f){

            if(strpos($f->name,'date') !== false OR $f->name == 'DOB' OR $f->name == 'cca end date'){
                if($row[$f->name] != ''){
                    $date = new DateTime($row[$f->name]);
                    $output = $date->format('m-d-Y');
                }else
                    $output = '';
            }else
                $output = $row[$f->name];

            if(($strtime == '' or $time < $now) and strtolower($note) != 'drop in'){
                echo "<td style='background-color: #fc5a5a;'>".$output."</td>\n";
            }elseif(strtolower($note) != 'drop in' and $time < $nextmonth){
                echo "<td style='background-color:yellow;'>".$output."</td>\n";
            }else{
                echo "<td style='background-color:white;'>".$output."</td>\n";
            }
        }

        //get the account id
        echo "</tr>\n";
    }

    echo "</table>\n";
    echo "</form>\n";
}

//Show the attendance sheet
function showDeleteableStudent($db,$data,$fields){

    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
        echo "<th>DELETE THIS STUDENT</th>\n";
    echo "</tr>";
        
    foreach($data as $row){
        echo "<tr>\n";
        $idData = getStudentByName($db,$row['first_name'],$row['last_name']);
        $id = $idData['data'][0]['studentID'];
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


function showCCA($data,$fields){
    $found = count($data);
    echo "<u>$found records found</u><br>\n";
    echo "<table class='data' align=\"center\">";
    $numfields = count($fields) + 2;
    echo "<tr><th colspan=$numfields >Contracts Associated with the Student</th></tr>";
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
    echo "<td colspan=2></td>";
    echo "</tr>";
    foreach($data as $row){
        //get the row id
        $id = $row[$fields[0]->name];

        echo "<tr class='data' onclick=\"post('cca/update/search_update.php',{id:$id})\">\n";
        foreach($fields as $f){
            $title = str_replace('_',' ',$f->name);

            if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
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
        echo "<td title='Update Row'><button formaction='cca/update/search_update.php' name='id' value=$id><img style='width: 15px; height: 15px;' src='/images/edit.png'></button></td>\n";
        echo "<td title='Delete Row'><button formaction='cca/delete/search_delete.php' name='id' value=$id><img style='width: 15px; height: 15px;' src='/images/x_mark.png'></button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>";
}
?>
