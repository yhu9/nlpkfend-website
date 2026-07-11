
<?php

function getattendanceBasic($db){
   	$sql1 = "SELECT Attendance.attendanceID as ID, last_name,first_name,date,time,verifier,type,room_name,Attendance.auth_type AS authorization,Attendance.tuition_type AS tuition, 'Student' AS status FROM Student,Attendance";
	$sql2 = "WHERE studentID = fk_studentID AND date = DATE(NOW())";
	$sql3 = "UNION";
    $sql4 = "SELECT Employee_Attendance.employee_attendanceID as ID, last_name,first_name,date,time,verifier,type,room_name,'none' AS authorization, 'none' AS tuition,'Employee' AS status FROM Employee,Employee_Attendance";
	$sql5 = "WHERE employeeID = fk_employeeID AND date = DATE(NOW()) ORDER BY last_name ASC, first_name ASC, date DESC,time DESC"; 

    $sql = "$sql1 $sql2 $sql3 $sql4 $sql5";
    $data = array();

    $result = mysqli_query($db,$sql);
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Attendance Data!<br>\n";
        echo "sql: $sql<br>\n";
        echo "sql: ".mysqli_error($db)."<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    if (isset($result) && $result instanceof mysqli_result) $result->free();

    return $return;
}

//get the student attendance data
function getDailyAttendance($db,$first_name,$last_name,$date){
    $sql1 = "SELECT DISTINCT last_name,first_name,date,type,Attendance.tuition_type AS tuition_type,Attendance.auth_type AS auth_type FROM Attendance,Student";
    $sql2 = "WHERE studentID = fk_studentID AND first_name = \"$first_name\" AND last_name = \"$last_name\" AND date='$date' AND type = 'sign in'"; 

    $sql = "$sql1 $sql2";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting student Attendance!<br>\n";
        echo "ERROR DISCRIPTION:".mysqli_error($db) ."<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//getAttendance
function getAttendance($db,$first_name,$last_name,$status){
    $sql = "";
    if($status == "Student"){
        $sql = "SELECT attendanceID as ID FROM Attendance,Student WHERE studentID = fk_studentID AND first_name = '$first_name' AND last_name = '$last_name'";
    }elseif($status == "Employee"){
        $sql = "SELECT employee_attendanceID as ID FROM Employee_Attendance,Employee WHERE employeeID = fk_employeeID AND first_name = '$first_name' AND last_name = '$last_name'";
    }

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
function showDeleteableAttendance($db,$data,$fields){

    $found = count($data ?? []);
    echo "<u>$found records found</u><br>\n";
    echo "<form method='POST' action='search_deleteAttendance.php'>\n";
    echo "<table class='data' align=\"center\">";
    echo "<tr>\n";
    foreach ($fields as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
        echo "<th>DELETE THIS ATTENDANCE</th>\n";
    echo "</tr>";
        
    foreach($data as $row){
        echo "<tr>\n";
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $status = $row['status'];
        $idData = getAttendance($db,$first_name,$last_name,$status);
        $id = $idData['data'][0]['ID'];
        foreach($fields as $f){
            if(strpos($f->name,'date') !== false OR $f->name == 'DOB'){
                $date = new DateTime($row[$f->name]);
                echo "<td>" . $date->format('m-d-Y')  ."</td>\n";
            }elseif(strpos($f->name,'time') !== false OR strpos($f->name,'Time') !== false){
                $time = new DateTime($row[$f->name]);
                echo "<td>" . $time->format('h:i:s A')  ."</td>\n";
            }else{
                echo "<td>" . $row[$f->name] ."</td>\n";
            }
        }
        
        echo "<td><button class='circularsmall' name='id' value='$status $id'>--</button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</form>\n";
}

//get all student attendance for the month of a particular year
function getMonthlyStudents($db,$month,$year){
    $sql1 = "SELECT DISTINCT last_name,first_name,studentID FROM Attendance,Student";
    $sql2 = "WHERE studentID = fk_studentID AND MONTH(date) = '$month' AND YEAR(date) = '$year' AND type = 'sign in'";
    $sql3 = "ORDER BY last_name,first_name";

    $sql = "$sql1 $sql2 $sql3";
    $result = mysqli_query($db,$sql);
    $data = array();
    $fields=array();
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

function getActiveStudents($db){
    $sql= "SELECT studentID,last_name,first_name FROM Student WHERE status = 'active'";
    $result = mysqli_query($db,$sql);
    $data = array();
    $fields=array();
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

//show Editable Attendance For the Month 
function showMonthlyEditable($db,$month,$year){
    $data = getMonthlyStudents($db,$month,$year);
    $allStudents = getActiveStudents($db);

    $monthNames = array();
    $monthNames[] = "January";
    $monthNames[] = "February";
    $monthNames[] = "March";
    $monthNames[] = "April";
    $monthNames[] = "May";
    $monthNames[] = "June";
    $monthNames[] = "July";
    $monthNames[] = "August";
    $monthNames[] = "September";
    $monthNames[] = "October";
    $monthNames[] = "November";
    $monthNames[] = "December";
    $weekNames = array();
    $weekNames[] = "M";
    $weekNames[] = "Tu";
    $weekNames[] = "W";
    $weekNames[] = "Th";
    $weekNames[] = "F";
    $weekNames[] = "Sa";
    $weekNames[] = "Su";
    
    //get the total number of FTE days
    //This method of calculating the total FTE days copies the show content portion of this function
    $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $total = 0;
    foreach($data['data'] as $row){
        $studentData = getStudentByID($db,$row['studentID']);
        $auth_type = $studentData['data'][0]['auth_type'];
        $tuition_type = $studentData['data'][0]['tuition_type'];
        $curstr = date('Y-m-d');
        $cur_date = new DateTime($curstr);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];

        $row_total = 0;
        for($i = 1; $i <= $days;$i++){
            $datestr = "$year-$month-$i";
            $date = new DateTime($datestr);
            $daily_data = getDailyAttendance($db,$first_name,$last_name,$datestr);
            $auth_type = $daily_data['data'][0]['auth_type'];
            $tuition_type = $daily_data['data'][0]['tuition_type'];
            if(!($date > $cur_date or $date->format('N') == 7)){
                $num = count($daily_data['data'] ?? []);
                if($num != 0){
                    if($tuition_type[0] == 'F' or $tuition_type[0] == 'f'){
                        $row_total += 1;
                    }
                    elseif($tuition_type[0] == 'P' or $tution_type[0] == 'p'){
                        $row_total += 0.5;
                    }
                }
            }
        }
        $total += $row_total;
    }

    //header for the page
    echo "<h1 align='center'>Attendance for the Month of ".$monthNames[$month - 1]." $year</h1>\n";
    echo "<h5 align='center'>CHILD CARE PROGRAM OFFICE</h5>\n";
    echo "<h5 align='center'>3601 C St, Suite 140 ~ PO Box 241809</h5>\n";
    echo "<h5 align='center'>Anchorage, AK 99524-1809</h5>\n";
    echo "<h5 align='center'>Phone:(907)269-4500 Toll Free: (888) 268-4632</h5>\n";
    echo "<h2 align='center'><b>CHILD CARE GRANT (CCG)</b></h2>\n";
    echo "<h2 align='center'><b>ATTENDANCE REPORT</b></h2>\n";
    echo "<table align='center' style='border:1px solid;width:70%;margin-left:15%;margin-right:15%;border-spacing:0px;'>\n";
    echo "<tr>";
    echo "<td style='background-color:white;text-align:left;'>Facility Name:</td\n>";
    echo "<td>Northern Lights Preschool & Childcare</td>\n";
    echo "<td style='text-align:right;'>ICCIS NUMBER/ PVN NUMBER</td>\n";
    echo "<td>10015532</td><td>/</td><td>NLP14238</td>\n";
    echo "</tr>";
    echo "<tr>";
    echo "<td style='background-color:white;text-align:left;'>Mailing Address:</td\n>";
    echo "<td>703 W Northern Lights Blvd Suite 200</td>";
    echo "<td style='text-align:right;'>Report Month/Year</td>\n";
    echo "<td colspan='3'><b>".$monthNames[$month - 1]." $year</b></td>\n";
    echo "</tr>";
    echo "<tr>";
    echo "<td style='background-color:white;text-align:left;'>City,Zip Code</td\n>";
    echo "<td>Anchorage 99503</td>";
    echo "<td><b>TOTAL FTE DAYS: $total</b></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan='3' ><b>Authorization Types: O</b> = OCS Authorizations <b>C</b> = CCAP Authorizations <b>S</b> = Self-Pay or Other</td>";
    echo "<td colspan='3' ><b>Attendance: F</b> = Full-Time <b>P</b> = Part-Time <b>X</b> = Absent, but scheduled to attend</td>";
    echo "</tr>";
    echo "</table>";

    //Initialize the top of the table
    echo "<table class='data' style='float:bottom;width:70%;margin-left:15%;margin-right:15%;' align=\"center\">";
    echo "<tr>\n";
    echo "<th rowspan='2'>last name, first name of child</th>\n";
    echo "<th rowspan='2'>Auth Type</th>\n";
    $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    for($i = 1; $i <= $days; $i++){
        echo "<th>$i</th>\n";
    }
    echo "<th rowspan='2'>Total FTEs</th>\n";
    echo "</tr>";
    echo "<tr>\n";
    $col_total = array();
    for($i = 1; $i <= $days;$i++){
        $col_total[$i] = 0;
        $datestr = "$year-$month-$i";
        $date = new DateTime($datestr);
        echo "<th>".$weekNames[$date->format('N') - 1]."</th>";
    }
    echo "</tr>\n";

    //Create the content of the table
    $total = 0;
    $postnames= array();
    foreach($data['data'] as $row){
        $studentData = getStudentByID($db,$row['studentID']);
        $auth_type = $studentData['data'][0]['auth_type'];
        $tuition_type = $studentData['data'][0]['tuition_type'];
        $curstr = date('Y-m-d');
        $cur_date = new DateTime($curstr);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $studentID = $row['studentID'];

        echo "<tr>\n";
        echo "<th>".$row['last_name'].", ".$row['first_name']."</th>\n";
        echo "<td><input type='text' name='$curstr,$studentID,$auth_type' size=2 style='width:20px;max-length:1;' placeholder='$auth_type'></td>\n";
        $postnames[] = "$curstr,$studentID,$auth_type"; 
        $row_total = 0;
        for($i = 1; $i <= $days;$i++){
            $datestr = "$year-$month-$i";
            $date = new DateTime($datestr);
            $daily_data = getDailyAttendance($db,$first_name,$last_name,$datestr);
            $auth_type = $daily_data['data'][0]['auth_type'];
            $tuition_type = $daily_data['data'][0]['tuition_type'];

            if($date > $cur_date or $date->format('N') == 7)
                echo "<td></td>";
            else{
                $num = count($daily_data['data'] ?? []);
                if($num != 0){
                    if($tuition_type[0] == 'F'){
                        $col_total[$i] += 1;
                        $row_total += 1;
                    }
                    elseif($tuition_type[0] == 'P'){
                        $col_total[$i] += 0.5;
                        $row_total += 0.5;
                    }

                    if($tuition_type == ''){
                        echo "<td><input type='text' name='$datestr,$studentID,?' style='size:4;width:20px; max-length:1; color:red;' placeholder='?'></td>\n";
                        $postnames[] = "$datestr,$studentID,?"; 
                    }else{
                        echo "<td><input type='text' name='$datestr,$studentID,$tuition_type[0]' size=2 style='width:20px;max-length:1;' placeholder='$tuition_type[0]'></td>\n";
                        $postnames[] = "$datestr,$studentID,$tuition_type[0]"; 
                    }
                }else{
                    echo "<td><input type='text' name='$datestr,$studentID,X' size=2 style='width:20px;max-length:1;' placeholder='X'></td>\n";
                    $postnames[] = "$datestr,$studentID,X"; 
                }
            }
        }
        echo "<td>$row_total</td>";
        $total += $row_total;
        echo "</tr>\n";
    }
    echo "<tr>\n";
    echo "<th>totals</th>";
    echo "<th></th>";
    for($i = 1; $i <= $days;$i++){
        echo "<th>".$col_total[$i]."</th>";
    }
    echo "<th>$total</th>";
    echo "</tr>\n";
    echo "</table>\n";

    //save post information for all field values. MAX post is 100,000 entries
    $count = count($postnames ?? []);
    echo "<input type='hidden' name='count' value=$count>\n";
    $i = 0;
    foreach($postnames as $n){
        echo "<input type='hidden' name='row$i' value='$n'>\n";
        $i += 1;
    }

	return $total;
}

//show attendance for month
function showMonthlyAttendance($db,$month,$year){
    $data = getMonthlyStudents($db,$month,$year);
    $allStudents = getActiveStudents($db);

    $monthNames = array();
    $monthNames[] = "January";
    $monthNames[] = "February";
    $monthNames[] = "March";
    $monthNames[] = "April";
    $monthNames[] = "May";
    $monthNames[] = "June";
    $monthNames[] = "July";
    $monthNames[] = "August";
    $monthNames[] = "September";
    $monthNames[] = "October";
    $monthNames[] = "November";
    $monthNames[] = "December";
    $weekNames = array();
    $weekNames[] = "M";
    $weekNames[] = "Tu";
    $weekNames[] = "W";
    $weekNames[] = "Th";
    $weekNames[] = "F";
    $weekNames[] = "Sa";
    $weekNames[] = "Su";
    
    //get the total number of FTE days
    //This method of calculating the total FTE days copies the show content portion of this function
    $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $total = 0;
    $total_O = 0;
    $total_C = 0;
    $total_S = 0;
    foreach($data['data'] as $row){
        $studentData = getStudentByID($db,$row['studentID']);
        $auth_type = $studentData['data'][0]['auth_type'];
        $tuition_type = $studentData['data'][0]['tuition_type'];
        $curstr = date('Y-m-d');
        $cur_date = new DateTime($curstr);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];

        if($auth_type == 'O')
            $total_O += 1;
        elseif($auth_type == 'C')
            $total_C += 1;
        elseif($auth_type == 'S')
            $total_S += 1;

        $row_total = 0;
        for($i = 1; $i <= $days;$i++){
            $datestr = "$year-$month-$i";
            $date = new DateTime($datestr);
            $daily_data = getDailyAttendance($db,$first_name,$last_name,$datestr);
            $auth_type = $daily_data['data'][0]['auth_type'];
            $tuition_type = $daily_data['data'][0]['tuition_type'];
            if(!($date > $cur_date or $date->format('N') == 7)){
                $num = count($daily_data['data'] ?? []);
                if($num != 0){
                    if($tuition_type[0] == 'F'){
                        $row_total += 1;
                    }
                    elseif($tuition_type[0] == 'P'){
                        $row_total += 0.5;
                    }
                }
            }
        }
        $total += $row_total;
    }


    //table style
    echo "<style type='text/css'>";
    echo "    table {";
    echo "       border-collapse: collapse;";
    echo "        border:solid 0px black;";
    echo "    }";
    echo "    tr {border: solid 0px black;}";
    echo "    td {border: solid 1px black;text-align:center;font-size:10px;}";
    echo "    th {border: solid 0px black;font-size:10px;}";
    echo "</style>";


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //attendance sheet
    echo "<table align='center' style='width:100%;'>\n";
    echo "<tr>\n";
    echo "<th style='border-bottom:none;border-right:border-top:none; border-left:none;' colspan='4'></td>";

    //echo "<td colspan='4'></td>";
    echo "<td colspan='3'><h5 align='center'><b>For Office Use Only</b></h5></td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
    echo "<th colspan='2'></th>\n";

    echo "<th colspan='2'>\n";
    echo "<p align=center style='font-size:10px'>CHILD CARE PROGRAM OFFICE<br>\n";
    echo "3601 C St, Suite 140 ~ PO Box 241809<br>\n";
    echo "Anchorage, AK 99524-1809<br>\n";
    echo "Phone:(907)269-4500 Toll Free: (888) 268-4632<br>\n";
    echo "<h4 align='center'><b>CHILD CARE GRANT (CCG)</b></h4>\n";
    echo "<h4 align='center'><b>ATTENDANCE REPORT</b></h4>\n";
    echo "</th>\n";

    echo "<td colspan='3'></td>\n";
    echo "</tr>\n";

    echo "<tr><th></th></tr>\n";

    echo "<tr>";
    echo "<td colspan='2' style='background-color:white;text-align:left;'>Facility Name:</td\n>";
    echo "<td style='text-align:center;'>Northern Lights Preschool & Childcare</td>\n";
    echo "<td style='text-align:right;'>ICCIS NUMBER/ PVN NUMBER</td>\n";
    echo "<td style='text-align:right;'>10015532</td><td>/</td><td style='text-align:left;'>NLP14238</td>\n";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan='2' style='background-color:white;text-align:left;'>Mailing Address:</td\n>";
    echo "<td>703 W Northern Lights Blvd Suite 200</td>";
    echo "<td style='text-align:right;'>Report Month/Year</td>\n";
    echo "<td colspan='3'><b>".$monthNames[$month - 1]." $year</b></td>\n";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan='2' style='background-color:white;text-align:left;'>City,Zip Code</td\n>";
    echo "<td>Anchorage 99503</td>";
    echo "<td><b>TOTAL FTE DAYS: $total</b></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan='3' ><b>Authorization Types: O</b> = OCS Authorizations <b>C</b> = CCAP Authorizations <b>S</b> = Self-Pay or Other</td>";
    echo "<td colspan='4' ><b>Attendance: F</b> = Full-Time <b>P</b> = Part-Time <b>X</b> = Absent, but scheduled to attend</td>";
    echo "</tr>";
    echo "</table>";
    echo "<br>";


    //Initialize the top of the table
    echo "<table class='data' style='width:100%;' align=\"center\">";
    echo "<tr>\n";
    echo "<td rowspan='2'><b>last name, first name of child</b></td>\n";
    echo "<td rowspan='2'><b>Auth Type</b></td>\n";
    $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    for($i = 1; $i <= $days; $i++){
        echo "<td><b>$i</b></td>\n";
    }
    echo "<td rowspan='2'><b>Total FTEs</b></td>\n";
    echo "</tr>";
    echo "<tr>\n";
    $col_total = array();
    for($i = 1; $i <= $days;$i++){
        $col_total[$i] = 0;
        $datestr = "$year-$month-$i";
        $date = new DateTime($datestr);
        echo "<td style='border-bottom:2px solid;'><b>".$weekNames[$date->format('N') - 1]."</b></td>";
    }
    echo "</tr>\n";

    //Create the content of the table
    $count = 0;
    $total = 0.0;
    foreach($data['data'] as $row){
        if($count == 30){
            echo "</table>";
            echo "<p style='page-break-before: always;'>&nbsp;</p>";
            echo "<table class='data' style='width:100%;' align=\"center\">";
            echo "<tr>\n";
            echo "<td rowspan='2'><b>last name, first name of child</b></td>\n";
            echo "<td rowspan='2'><b>Auth Type</b></td>\n";
            for($i = 1; $i <= $days; $i++){
                echo "<td><b>$i</b></td>\n";
            }
            echo "<td rowspan='2'><b>Total FTEs</b></td>\n";
            echo "</tr>";
            echo "<tr>\n";
            for($i = 1; $i <= $days;$i++){
                $datestr = "$year-$month-$i";
                $date = new DateTime($datestr);
                echo "<td style='border-bottom:2px solid;'><b>".$weekNames[$date->format('N') - 1]."</b></td>";
            }
            echo "</tr>\n";
        }elseif(($count % 50) == 0 and $count > 0){
            echo "</table>";
            echo "<p style='page-break-after: always;'>&nbsp;</p>";
            echo "<table class='data' style='width:100%;' align=\"center\">";
            echo "<tr>\n";
            echo "<td rowspan='2'><b>last name, first name of child</b></td>\n";
            echo "<td rowspan='2'><b>Auth Type</b></td>\n";
            for($i = 1; $i <= $days; $i++){
                echo "<td><b>$i</b></td>\n";
            }
            echo "<td rowspan='2'><b>Total FTEs</b></td>\n";
            echo "</tr>";
            echo "<tr>\n";
            for($i = 1; $i <= $days;$i++){
                $datestr = "$year-$month-$i";
                $date = new DateTime($datestr);
                echo "<td style='border-bottom:2px solid;'><b>".$weekNames[$date->format('N') - 1]."</b></td>";
            }
            echo "</tr>\n";
        }
        $count += 1;

        $studentData = getStudentByID($db,$row['studentID']);
        $auth_type = $studentData['data'][0]['auth_type'];
        $tuition_type = $studentData['data'][0]['tuition_type'];
        $curstr = date('Y-m-d');
        $cur_date = new DateTime($curstr);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];

        echo "<tr>\n";
        echo "<td>".$row['last_name'].", ".$row['first_name']."</td>\n";
        echo "<td style='border-right:2px solid;' align=center>$auth_type</td>\n";
        $row_total = 0.0;
        for($i = 1; $i <= $days;$i++){
            $datestr = "$year-$month-$i";
            $date = new DateTime($datestr);
            $daily_data = getDailyAttendance($db,$first_name,$last_name,$datestr);
            $auth_type = $daily_data['data'][0]['auth_type'];
            $tuition_type = $daily_data['data'][0]['tuition_type'];

            if($date > $cur_date or $date->format('N') == 7)
                echo "<td></td>";
            else{
                $num = count($daily_data['data'] ?? []);
                if($num != 0){
                    if($tuition_type[0] == 'F'){
                        $col_total[$i] += 1.0;
                        $row_total += 1.0;
                    }
                    elseif($tuition_type[0] == 'P'){
                        $col_total[$i] += 0.5;
                        $row_total += 0.5;
                    }

                    if($tuition_type == ''){
                        echo "<td style='color:red;'>?</td>";    
                    }else
                        echo "<td>".$tuition_type[0]."</td>";
                }else
                    echo "<td>X</td>";
            }
        }
        echo "<td style='border-left:2px solid;' align=center>$row_total</td>";
        $total += $row_total;
        echo "</tr>\n";
    }
    echo "<tr>\n";
    echo "<td>totals</td>";
    echo "<td></td>";
    for($i = 1; $i <= $days;$i++){
        echo "<td style='border-top:2px solid;'>".$col_total[$i]."</td>";
    }
    echo "<td align=center>$total</td>";
    echo "</tr>\n";
    echo "</table>\n";

	return $total;
}

//show attendance for month
function showMonthlyCoverPage($db,$month,$year){
    $data = getMonthlyStudents($db,$month,$year);
    $allStudents = getActiveStudents($db);

    $monthNames = array();
    $monthNames[] = "January";
    $monthNames[] = "February";
    $monthNames[] = "March";
    $monthNames[] = "April";
    $monthNames[] = "May";
    $monthNames[] = "June";
    $monthNames[] = "July";
    $monthNames[] = "August";
    $monthNames[] = "September";
    $monthNames[] = "October";
    $monthNames[] = "November";
    $monthNames[] = "December";
    $weekNames = array();
    $weekNames[] = "M";
    $weekNames[] = "Tu";
    $weekNames[] = "W";
    $weekNames[] = "Th";
    $weekNames[] = "F";
    $weekNames[] = "Sa";
    $weekNames[] = "Su";
    
    //get the total number of FTE days
    //This method of calculating the total FTE days copies the show content portion of this function
    $days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    $total = 0;
    $total_O = 0;
    $total_C = 0;
    $total_S = 0;
    foreach($data['data'] as $row){
        $studentData = getStudentByID($db,$row['studentID']);
        $auth_type = $studentData['data'][0]['auth_type'];
        $tuition_type = $studentData['data'][0]['tuition_type'];
        $curstr = date('Y-m-d');
        $cur_date = new DateTime($curstr);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];

        if($auth_type == 'O')
            $total_O += 1;
        elseif($auth_type == 'C')
            $total_C += 1;
        elseif($auth_type == 'S')
            $total_S += 1;

        $row_total = 0;
        for($i = 1; $i <= $days;$i++){
            $datestr = "$year-$month-$i";
            $date = new DateTime($datestr);
            $daily_data = getDailyAttendance($db,$first_name,$last_name,$datestr);
            $auth_type = $daily_data['data'][0]['auth_type'];
            $tuition_type = $daily_data['data'][0]['tuition_type'];
            if(!($date > $cur_date or $date->format('N') == 7)){
                $num = count($daily_data['data'] ?? []);
                if($num != 0){
                    if($tuition_type[0] == 'F'){
                        $row_total += 1;
                    }
                    elseif($tuition_type[0] == 'P'){
                        $row_total += 0.5;
                    }
                }
            }
        }
        $total += $row_total;
    }


    //table style
    echo "<style type='text/css'>";
    echo "    table {";
    echo "       border-collapse: collapse;";
    echo "        border:solid 0px black;";
    echo "    }";
    echo "    tr {border: solid 0px black;}";
    echo "    td {border: solid 1px black;text-align:center;font-size:10px;}";
    echo "    th {border: solid 0px black;font-size:10px;}";
    echo "</style>";

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //start cover page
    echo "<table align='center' style='width:100%;'>\n";
    echo "<tr>\n";
    echo "<th style='border-bottom:none;border-right:border-top:none; border-left:none;' colspan='4'></th>";
    echo "<td colspan='3'><h5 align='center'><b>For Office Use Only</b></h5></td>\n";
    echo "</tr>\n";

    echo "<tr>\n";
    echo "<th style='width:10%;'></th>\n";
    echo "<th style='width:70%;' colspan='3'>\n";
    echo "<p align=center style='font-size:10px;'>CHILD CARE PROGRAM OFFICE<br>\n";
    echo "3601 C St, Suite 140 ~ PO Box 241809<br>\n";
    echo "Anchorage, AK 99524-1809<br>\n";
    echo "Phone:(907)269-4500 Toll Free: (888) 268-4632\n</p>";
    echo "<h4 align='center'><b>CHILD CARE GRANT (CCG) REIMBURSEMENT REQUEST</b></h4>\n";
    echo "</th>\n";
    echo "<td style='width:20%;' colspan='3'></td>\n";
    echo "</tr>\n";

    echo "<tr><th><br></th></tr>\n";

    echo "<tr>";
    echo "<th style='text-align:left;'>ICCIS NUMBER/ PVN NUMBER:</th>\n";
    echo "<td style='text-align:center;width:30%;'>10015532 / NLP14238</td>\n";
    echo "<th></th>";
    echo "</tr>";

    echo "<tr>";
    echo "<th style='text-align:left;'>Facility Name:</th>\n";
    echo "<td style='text-align:center;'>Northern Lights Preschool & Childcare</td>\n";
    echo "<th></th>";
    echo "<th style='text-align:left;'>Report Month/Year:</th>\n";
    echo "<td colspan='3'><b>".$monthNames[$month - 1]." $year</b></td>\n";
    echo "<tr>\n";

    echo "<tr>";
    echo "<th style='text-align:left;'>Mailing Address:</th>\n";
    echo "<td>703 W Northern Lights Blvd Suite 200</td>";
    echo "<th></th>";
    echo "<th style='text-align:left;'>6. Number of children with CCAP authorizations (C)</th>\n";
    echo "<td>$total_C</td>";
    echo "</tr>";

    
    echo "<tr>";
    echo "<th style='text-align:left;'>City,Zip Code:</th>\n";
    echo "<td>Anchorage 99503</td>";
    echo "<th></th>";
    echo "<th style='text-align:left;'>7. Number of children with OCS authorizations (O)</th>\n";
    echo "<td>$total_O</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<th style='text-align:left;'>Physical Address:</th>\n";
    echo "<td>703 W Northern Lights Blvd Suite 200</td>";
    echo "<th></th>";
    echo "<th style='text-align:left;'>8. Number of all other children (S)</th>\n";
    echo "<td>$total_S</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<th style='text-align:left;'>City,Zip Code:</th>\n";
    echo "<td>Anchorage 99503</td>";
    echo "<th></th>";
    echo "<th style='text-align:left;'>9. Total children in care (total of lines 6 through 8)</th>\n";
    $total_kids= $total_C + $total_O + $total_S;
    echo "<td>$total_kids</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<th><br><br></th>";
    echo "<th></th>";
    echo "<th></th>";
    echo "<th style='text-align:left;'>10. ATTENDANCE MINIMUM</th>";
    echo "<td> 6 </td>";
    echo "</tr>";

    echo "<tr>";
    echo "<th colspan='2' style='text-align:left;'>1. Write the number of full-time equivalent children in care for the report month</th>";
    echo "<td>$total</td>";
    echo "<th style='text-align:left;'>11. Specify how Child Care Grant Funds were spent <br>during the report month (check all that apply and enter amount):</th>";
    echo "</tr>";

    echo "<tr>";
    echo "<th colspan='2' style='text-align:left;'>2. Divide <b>Line 1</b> by 21.7 (Average Daily Attendance): </th>";
    $val1 = $total / 21.7;
    echo "<td>".round($val1,2)."</td>";
    echo "<th colspan='2' rowspan='5'>";
    echo "<table style='width:90%;border:1px solid;margin-left:5%;margin-right:5%;'>";
    echo "<tr><th style='width:30px;'></th><th>Expenditure Category</th><th></th><th>Amount</th></tr>";
    echo "<tr><td></td><td>staff salaries & benefits</td><th></th><td></td></tr>";
    echo "<tr><td></td><td>Substitue care, cost associated with providing</td><th></th><td></td></tr>";
    echo "<tr><td></td><td>Supplies, equipment & activities costs for children</td><th></th><td></td></tr>";
    echo "<tr><td></td><td>Health & safety costs</td><th></th><td></td></tr>";
    echo "<tr><td></td><td>Child development education & training for staff</td><th></th><td></td></tr>";
    echo "<tr><td></td><td>OTHER: Requires CCPO Pre-approval</td><th></th><td></td></tr>";
    echo "<tr><th></th><th></th><th></th><th>Total</th></tr>";
    echo "<tr><td></td><td></td><th></th><td>$</td></tr>";
    echo "</table>";
    echo "</th>";
    echo "</tr>";

    echo "<tr>";
    echo "<th colspan='2' style='text-align:left;'>3. Enter the geographically adjusted rate for your community from the CCG Rate Schedule</th>";
    $val2 = 30.00;
    echo "<td>$".round($val2,2)."</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th colspan='2' style='text-align:left;'>4. Multiply <b>Line 2</b> by <b>Line 3</b>. This is your maximum qualifying reimbursement amount:</th>";
    $val3 = $val1 * $val2;
    echo "<td style='border:2px solid;'>$".round($val3,2)."</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<th colspan='2' style='text-align:left;'>5. (CCPO USE ONLY) CCG reimbursement amount approved for payment and supported by attached receipts</th>";
    echo "<td></td>";
    echo "</tr>";

    echo "</table>";
    echo "<br><br>";

    echo "<table style='width:100%';>";
    echo "<tr>";
    echo "<td colspan='4'>";
    echo "<p align=left>";
    echo "<b>STATEMENT OF TRUTH:</b> Under penalty of perjury or unsworn falsification, I certify that the information provided on this form and all accompanying daily CCG Attendance forms for the period indicated are true and accurate. I understand that if I provide false information on or with this form, any money obtained as a result must be paid back to the State of Alaska and I may not be able to participate in the Child Care Grant Program in the future. <b>I understand that this payment request must be received by the last day of the month following the report month or payment will be denied. <b>\n";
    echo "</p>";
    echo "</td>";
    echo "</tr>";
    echo "<td><br><br><br><hr style='width:80%; margin-left:10%; margin-right:10%;'>Printed Name of Individual with Signatory Authority</td>";
    echo "<td><br><br><br><hr style='width:80%; margin-left:10%; margin-right:10%;'>Signature of Individual With Signatory Authority</td>";
    echo "<td><br><br><br><hr style='width:80%; margin-left:10%; margin-right:10%;'>Contact Telephone Number</td>";
    echo "<td style='width:10%;'><br><br><br><hr style='width:80%; margin-right:10%; margin-left:10%;'>Date</td>";
    echo "</tr>";
    echo "</table>";

    echo "<br><hr><br>";

    echo "<p style='page-break-before: always;'>&nbsp;</p>";


	return $total;
}
?>
