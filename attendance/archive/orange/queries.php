
<?php


//basid attendance query
function attendance_basic($db){
    
    $sql1 = "SELECT last_name,first_name FROM Student";
    $sql2 = "WHERE room = 'orange' and status='active'";
    $sql3 = "ORDER BY last_name";

    $sql = "$sql1 $sql2 $sql3";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Attendance Data!<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//get all students
function all_attendance($db){
    
    $sql1 = "SELECT studentID,last_name,first_name,room FROM Student";
    $sql2 = "WHERE status = 'active'";
    $sql3 = "ORDER BY last_name,first_name";

    $sql = "$sql1 $sql2 $sql3";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Attendance Data!<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//get all employee
function all_employee($db){
    
    $sql1 = "SELECT employeeID,last_name,first_name FROM Employee";
    $sql2 = "WHERE status = 'active'";
    $sql3 = "ORDER BY last_name,first_name";

    $sql = "$sql1 $sql2 $sql3";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Attendance Data!<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//get different students
function attendance_other($db){
    $sql1 = "SELECT DISTINCT last_name,first_name FROM Student,Attendance";
    $sql2 = "WHERE studentID = fk_studentID AND room <> 'orange' AND room_name ='orange' AND date = DATE(NOW()) ORDER BY last_name";

    $sql = "$sql1 $sql2";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Other Attendance Data!<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//show attendance for teacher
function attendance_employee($db){
    $sql1 = "SELECT DISTINCT last_name,first_name FROM Employee,Employee_Attendance";
    $sql2 = "WHERE employeeID = fk_employeeID AND room_name = 'Orange' AND date = DATE(NOW()) ORDER BY last_name";

    $sql = "$sql1 $sql2";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Other Attendance Data!<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//get the attendance for a particular student
function getAttendance($db,$first_name,$last_name,$type){
    $sql = "SELECT verifier, MAX(time) as `time` FROM Attendance,Student WHERE studentID = fk_studentID AND first_name = \"$first_name\" AND last_name = \"$last_name\" AND type = \"$type\" AND room_name = \"Orange\" AND date = DATE(NOW()) GROUP BY verifier ORDER BY `time` DESC";


    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Attendance Data!<br>\n";
        echo "query: $sql<br>\n";
        echo "error: " . mysqli_error($db) . "<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//get the attendance for a particular employee
function getEmployeeAttendance($db,$first_name,$last_name,$type){
    $sql = "SELECT MAX(time) as `time`,verifier FROM Employee_Attendance,Employee WHERE employeeID = fk_employeeID AND first_name = \"$first_name\" AND last_name = \"$last_name\" AND type = \"$type\" AND room_name = \"Orange\" AND DATE(NOW()) = date GROUP BY verifier ORDER BY `time` DESC";

    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Attendance Data!<br>\n";
        echo "query: $sql<br>\n";
        echo "error: " . mysqli_error($db) . "<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;

    return $return;
}

//Show the attendance sheet
function showAttendance($db){
    $studentData = attendance_basic($db);
    $otherData = attendance_other($db);
    $employeeData = attendance_employee($db);

    $found = count($studentData['data']);
    echo "<u>$found records found</u><br>\n";
    echo "<form method='POST' action='search.php'>\n";

    echo "<table style='margin-left:auto;margin-right:auto;border-spacing:10px;' align='center'>\n";
    echo "<tr>";
    echo "<th style='border-style:none;'><u>Employees</u></th>";
    echo "<th style='border-style:none;'><u>Other Students</u></th>";
    echo "<th style='border-style:none;'><u>Homeroom Students</u></th>";
    echo "</tr>";
    echo "<tr style='vertical-align:top;'>\n";
    
    /////////////////////////////////////////////////////////////////////////
    //show the employee attendance for the room
    echo "<td>\n";
    echo "<table class='data' style='width:100%; margin-top:0px;'>\n";
    echo "<tr>\n";
    foreach ($employeeData['fields'] as $f){
        echo "<th style='vertical-align:top'>". str_replace('_',' ',$f->name) ."</th>\n";
    }
    echo "<th>time in</th>\n";
    echo "<th>time out</th>\n";
    echo "</tr>";

    //display the employee name
    foreach($employeeData['data'] as $row){
        echo "<tr>\n";
        $idData = getEmployeeData($db,$row['first_name'],$row['last_name']);
        $id = $idData['data'][0]['employeeID'];
        foreach($employeeData['fields'] as $f){
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

        //display the most recent clockin/clockout data
        $clockinData = getEmployeeAttendance($db,$row['first_name'],$row['last_name'],'sign in');
        $clockoutData = getEmployeeAttendance($db,$row['first_name'],$row['last_name'],'sign out');
        if(count($clockinData['data']) == 0){
            echo "<td></td>\n";
        }else{
            $str = $clockinData['data'][0]['time'];
            if($str != ''){
                $time = new DateTime($str);
                echo "<td>" . $time->format('h:i:s A')."</td>\n";
            }else
                echo "<td></td>\n";
        }
        if(count($clockoutData['data']) == 0){
            echo "<td></td>\n";
        }else{
            $strin = $clockinData['data'][0]['time'];
            $strout = $clockoutData['data'][0]['time'];
            $timein = new DateTime($strin);
            $timeout = new DateTime($strout);

            if($strout != '' AND $timein < $timeout){
                echo "<td>" . $timeout->format('h:i:s A')."</td>\n";
            }else
                echo "<td></td>\n";
        }

        echo "<td><button class='circular' name='id' formaction='searchEmployee.php' value='$id'>+</button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</td>\n";

        
    /////////////////////////////////////////////////////////////////////////
    //show the other student attendance for the room
    echo "<td>\n";
    echo "<table class='data' style='margin-top:0px;'>\n";
    echo "<tr>\n";
    foreach ($otherData['fields'] as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
    echo "<th>time in</th>\n";
    echo "<th>initial</th>\n";
    echo "<th>time out</th>\n";
    echo "<th>initial</th>\n";
    echo "</tr>";
    
    foreach($otherData['data'] as $row){
        echo "<tr>\n";
        $idData = getStudentData($db,$row['first_name'],$row['last_name']);
        $id = $idData['data'][0]['studentID'];
        foreach($otherData['fields'] as $f){
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

        //show most recent clockin/clockout
        $clockinData = getAttendance($db,$row['first_name'],$row['last_name'],'sign in');
        $clockoutData = getAttendance($db,$row['first_name'],$row['last_name'],'sign out');
        if(count($clockinData['data']) == 0){
            echo "<td></td>\n";
            echo "<td></td>\n";
        }else{
            $str = $clockinData['data'][0]['time'];
            if($str != ''){
                $time = new DateTime($str);
                echo "<td>" . $time->format('h:i:s A')."</td>\n";
            }else
                echo "<td></td>\n";
            $str = $clockinData['data'][0]['verifier'];
            if($str != ''){
                echo "<td>$str</td>\n";
            }else
                echo "<td></td>\n";
        }
        if(count($clockoutData['data']) == 0){
            echo "<td></td>\n";
            echo "<td></td>\n";
        }else{
            $strin = $clockinData['data'][0]['time'];
            $strout = $clockoutData['data'][0]['time'];
            $timein = new DateTime($strin);
            $timeout = new DateTime($strout);

            if($strout != '' AND $timein < $timeout){
                $time = new DateTime($strout);
                echo "<td>" . $time->format('h:i:s A')."</td>\n";
            }else
                echo "<td></td>\n";
            $strout = $clockinData['data'][0]['verifier'];
            if($strout != '' AND $timein < $timeout){
                echo "<td>$strout</td>\n";
            }else
                echo "<td></td>\n";
        }

        echo "<td><button class='circular' name='id' value='$id'>+</button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</td>\n";

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    //show the normal student attendance for the room
    echo "<td>\n";
    echo "<table class='data' style='vertical-align:top; margin-top:0px;'>";
    echo "<tr>\n";
    foreach ($studentData['fields'] as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
    echo "<th>time in</th>\n";
    echo "<th>initial</th>\n";
    echo "<th>time out</th>\n";
    echo "<th>initial</th>\n";
    echo "</tr>";
        
    foreach($studentData['data'] as $row){
        echo "<tr>\n";
        $idData = getStudentData($db,$row['first_name'],$row['last_name']);
        $id = $idData['data'][0]['studentID'];
        foreach($studentData['fields'] as $f){
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
        
        $clockinData = getAttendance($db,$row['first_name'],$row['last_name'],'sign in');
        $clockoutData = getAttendance($db,$row['first_name'],$row['last_name'],'sign out');
        if(count($clockinData['data']) == 0){
            echo "<td></td>\n";
            echo "<td></td>\n";
        }else{
            $str = $clockinData['data'][0]['time'];
            if($str != ''){
                $time = new DateTime($str);
                echo "<td>" . $time->format('h:i:s A')."</td>\n";
            }else
                echo "<td></td>\n";
            $str = $clockinData['data'][0]['verifier'];
            if($str != ''){
                echo "<td>$str</td>\n";
            }else
                echo "<td></td>\n";
        }
        if(count($clockoutData['data']) == 0){
            echo "<td></td>\n";
            echo "<td></td>\n";
        }else{
            $strout = $clockoutData['data'][0]['time'];
            $strin = $clockinData['data'][0]['time'];
            $timein = new DateTime($strin);
            $timeout = new DateTime($strout);

            if($strout != '' AND $timein < $timeout){
                $time = new DateTime($strout);
                echo "<td>" . $time->format('h:i:s A')."</td>\n";
            }else
                echo "<td></td>\n";
            $strout = $clockoutData['data'][0]['verifier'];
            if($strout != '' AND $timein < $timeout){
                echo "<td>$strout</td>\n";
            }else
                echo "<td></td>\n";
        }

        echo "<td><button class='circular' name='id' value='$id'>+</button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    echo "<tr>\n";
    
    //show add employee button
    echo "<td>\n";
    echo "<input class='rectangular' style='background-color: #ffffff;' type='submit' formaction='employee.php' value='Search Employee'>\n";
    echo "</td>\n";

    //show add other student form
    echo "<td>\n";
    echo "</td>\n";

    //show add student button
    echo "<td>";
    echo "<input class='rectangular' style='background-color: #ffffff;' type='submit' formaction='all.php' style='width:200px; height:50px;' value='Search Student'>\n";
    echo "</td>";
    echo "</tr>\n";

    //closure
    echo "</table\n";
    echo "</form>\n";
}


//Show the attendance sheet
function showAllEmployee($db){
    $employeeData = all_employee($db);

    $found = count($employeeData['data']);
    echo "<u>$found records found</u><br>\n";
    echo "<form method='POST' action='searchEmployee.php'>\n";

    echo "<table style='margin-left:20px;margin-right:auto;float:left'>\n";
    echo "<caption>All Employees</caption>";
    echo "<tr style='vertical-align:top;'>\n";
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    //show the normal employee attendance for the room
    echo "<td>\n";
    echo "<table class='data' style='vertical-align:top; margin-top:0px;'>";
    echo "<tr>\n";
    foreach ($employeeData['fields'] as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
    echo "<th>time in</th>\n";
    echo "<th>initial</th>\n";
    echo "<th>time out</th>\n";
    echo "<th>initial</th>\n";
    echo "<th>total-time</th>\n";
    echo "</tr>";
        
    foreach($employeeData['data'] as $row){
        echo "<tr>\n";
        $id= $row['employeeID'];

        foreach($employeeData['fields'] as $f){
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

        $clockinData = getAttendance($db,$id,'sign in');
        $clockoutData = getAttendance($db,$id,'sign out');
        if(count($clockinData['data']) == 0){
            echo "<td></td>\n";
            echo "<td></td>\n";
        }else{
            $str = $clockinData['data'][0]['time'];
            if($str != ''){
                $time = new DateTime($str);
                echo "<td>" . $time->format('h:i:s A')."</td>\n";
            }else
                echo "<td></td>\n";
            $str = $clockinData['data'][0]['verifier'];
            if($str != ''){
                echo "<td>$str</td>\n";
            }else
                echo "<td></td>\n";
        }

        $strout = $clockoutData['data'][0]['time'];
        $strin = $clockinData['data'][0]['time'];
        $timein = new DateTime($strin);
        $timeout = new DateTime($strout);
        if(count($clockoutData['data']) == 0){
            echo "<td></td>\n";
            echo "<td></td>\n";
        }else{
            if($strout != '' AND $timein < $timeout){
                $time = new DateTime($strout);
                echo "<td>" . $time->format('h:i:s A')."</td>\n";
            }else
                echo "<td></td>\n";
            $strout = $clockoutData['data'][0]['verifier'];
            if($strout != '' AND $timein < $timeout){
                echo "<td>$strout</td>\n";
            }else
                echo "<td></td>\n";
        }

        $total = new DateTime('00:00:00');
        if($strin == '')
            echo "<td>Absent</td>\n";
        elseif($strout == '' and $strin != ''){
            $timeend = new DateTime(date('H:i:s'));
            $diff = $timein->diff($timeend);
            $total->add($diff);

            echo "<td>".$total->format('H:i:s')."</td>\n";
        }
        elseif($strout != '' and $strin != ''){
            $diff = $timein->diff($timeout);
            $total->add($diff);

            echo "<td>".$total->format('H:i:s')."</td>\n";
        }

        echo "<td><button class='circular' name='id' value='$id'>+</button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";

    //closure
    echo "</table\n";
    echo "</form>\n";
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //show add other employee form
    echo "<table class='form' style='width:float:right; ' border=\"1\">";
    echo "<caption>Search Form</caption>";
            //query successful
            //show form for adding an emergency_contact
            echo "<tr>";
                echo "<td><b>first name</b></td>\n";
                echo "<td align=\"center\"><input type=\"text\" style='width:100%;height:50px;' name='first_name'></td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
                echo "<td><b>last name</b></td>\n";
                echo "<td align=\"center\"><input type=\"text\" style='width:100%; height:50px;' name='last_name'></td>\n";
                echo "</tr>";
                echo "<tr>\n";
    echo "<td></td>\n";
    echo "<td>";
    echo "<input class='rectangular' type='submit' style='width:100%; height:50px;' formaction='searchEmployee.php' value='Search Employee'>\n";
    echo "</td>\n";
                echo "</tr>\n";
    echo "</table>\n";

    //show add employee button

}
//Show the attendance sheet
function showAttendanceAll($db){
    $studentData = all_attendance($db);

    $found = count($studentData['data']);
    echo "<u>$found records found</u><br>\n";
    echo "<form method='POST' action='search.php'>\n";

    echo "<table style='margin-left:20px;margin-right:auto;float:left'>\n";
    echo "<caption>All Students</caption>";
    echo "<tr style='vertical-align:top;'>\n";
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    //show the normal student attendance for the room
    echo "<td>\n";
    echo "<table class='data' style='vertical-align:top; margin-top:0px;'>";
    echo "<tr>\n";
    foreach ($studentData['fields'] as $f){
        echo "<th>". str_replace('_',' ',$f->name) ."</th>\n";
    }
    echo "<th>time in</th>\n";
    echo "<th>initial</th>\n";
    echo "<th>time out</th>\n";
    echo "<th>initial</th>\n";
    echo "<th>total-time</th>\n";
    echo "</tr>";
        
    foreach($studentData['data'] as $row){
        echo "<tr>\n";
        $id= $row['studentID'];

        foreach($studentData['fields'] as $f){
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

        $clockinData = getAttendance($db,$id,'sign in');
        $clockoutData = getAttendance($db,$id,'sign out');
        if(count($clockinData['data']) == 0){
            echo "<td></td>\n";
            echo "<td></td>\n";
        }else{
            $str = $clockinData['data'][0]['time'];
            if($str != ''){
                $time = new DateTime($str);
                echo "<td>" . $time->format('h:i:s A')."</td>\n";
            }else
                echo "<td></td>\n";
            $str = $clockinData['data'][0]['verifier'];
            if($str != ''){
                echo "<td>$str</td>\n";
            }else
                echo "<td></td>\n";
        }

        $strout = $clockoutData['data'][0]['time'];
        $strin = $clockinData['data'][0]['time'];
        $timein = new DateTime($strin);
        $timeout = new DateTime($strout);
        if(count($clockoutData['data']) == 0){
            echo "<td></td>\n";
            echo "<td></td>\n";
        }else{
            if($strout != '' AND $timein < $timeout){
                $time = new DateTime($strout);
                echo "<td>" . $time->format('h:i:s A')."</td>\n";
            }else
                echo "<td></td>\n";
            $strout = $clockoutData['data'][0]['verifier'];
            if($strout != '' AND $timein < $timeout){
                echo "<td>$strout</td>\n";
            }else
                echo "<td></td>\n";
        }

        $total = new DateTime('00:00:00');
        if($strin == '')
            echo "<td>Absent</td>\n";
        elseif($strout == '' and $strin != ''){
            $timeend = new DateTime(date('H:i:s'));
            $diff = $timein->diff($timeend);
            $total->add($diff);

            echo "<td>".$total->format('H:i:s')."</td>\n";
        }
        elseif($strout != '' and $strin != ''){
            $diff = $timein->diff($timeout);
            $total->add($diff);

            echo "<td>".$total->format('H:i:s')."</td>\n";
        }

        echo "<td><button class='circular' name='id' value='$id'>+</button></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
    echo "</td>\n";
    echo "</tr>\n";

    //closure
    echo "</table\n";
    echo "</form>\n";
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    //show add other student form
    echo "<table class='form' style='width:float:right; ' border=\"1\">";
    echo "<caption>Search Form</caption>";
            //query successful
            //show form for adding an emergency_contact
            echo "<tr>";
                echo "<td><b>first name</b></td>\n";
                echo "<td align=\"center\"><input type=\"text\" style='width:100%;height:50px;' name='first_name'></td>\n";
            echo "</tr>\n";
            echo "<tr>\n";
                echo "<td><b>last name</b></td>\n";
                echo "<td align=\"center\"><input type=\"text\" style='width:100%; height:50px;' name='last_name'></td>\n";
                echo "</tr>";
                echo "<tr>\n";
    echo "<td></td>\n";
    echo "<td>";
    echo "<input class='rectangular' type='submit' style='width:100%; height:50px;' formaction='search.php' value='Search Student'>\n";
    echo "</td>\n";
                echo "</tr>\n";
    echo "</table>\n";

    //show add student button

}

//Insert into attendance type,verifier,and id
function insertAttendance($db,$type,$verifier,$room_name,$studentID){
    $studentData = getStudentByID($db,$studentID);
    $auth_type = $studentData['data'][0]['auth_type'];
    $tuition_type = $studentData['data'][0]['tuition_type'];
    $sql1 = "INSERT INTO Attendance (date,time,type,verifier,room_name,tuition_type,auth_type,fk_studentID)";
    $sql2 = "VALUES (DATE(NOW()),TIME(NOW()),'$type','$verifier','$room_name','$tuition_type','$auth_type',$studentID)";

    $sql = "$sql1 $sql2";

    //execute delete query
    $success = 1;
    $result = mysqli_query($db,$sql);
    if($result !== false){
        echo "<h1>Successfully Added Attendance</h1>";
    }else{
        $success = 0;
        echo "query: $sql <br>\n";
        echo "Error Adding Attendance: ". mysqli_error($db) ."<br>\n";
    }

    $result->free();

    return $success;
}

//Insert into attendance type,verifier,and id
function insertEmployeeAttendance($db,$type,$verifier,$room_name,$employeeID){
    $sql1 = "INSERT INTO Employee_Attendance (date,time,type,verifier,room_name,fk_employeeID)";
    $sql2 = "VALUES (DATE(NOW()),TIME(NOW()),'$type','$verifier','$room_name',$employeeID)";

    $sql = "$sql1 $sql2";

    //execute delete query
    $success = 1;
    $result = mysqli_query($db,$sql);
    if($result !== false){
        echo "<h1>Successfully Added Attendance</h1>";
    }else{
        $success = 0;
        echo "query: $sql <br>\n";
        echo "Error Adding Attendance: ". mysqli_error($db) ."<br>\n";
    }

    $result->free();

    return $success;
}

//gets the last inserted data from attendance
function getLastInsertData($db,$table){
    $id_name = strtolower($table);
    $id_name .= "ID";
    $sql = "SELECT $id_name,first_name,last_name FROM $table WHERE $id_name = LAST_INSERT_ID()";
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

//gets the student data given a first and last name
function getStudentData($db,$first_name,$last_name){
    $sql1 = "SELECT studentID,first_name,last_name,(TIME(NOW())) AS `current-time` FROM Student";
    $sql2 = "";

    if($first_name != '' and $last_name != ''){
        $sql2 = "WHERE first_name = \"$first_name\" and last_name = \"$last_name\"";
    }elseif($first_name != ''){
        $sql2 = "WHERE first_name = \"$first_name\"";
    }elseif($last_name != ''){
        $sql2 = "WHERE last_name = \"$last_name\"";
    }

    $sql = "$sql1 $sql2";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Searching result for student<br>\n";
        echo "query: $sql<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}

//get the employee data given a first and last name
function getEmployeeData($db,$first_name,$last_name){

    $sql1 = "SELECT employeeID, first_name,last_name FROM Employee";
    $sql2 = "WHERE";
    if($first_name != '' and $last_name != ''){
        $sql2 .= " first_name = \"$first_name\" and last_name = \"$last_name\"";
    }elseif($first_name != ''){
        $sql2 .= " first_name = \"$first_name\"";
    }elseif($last_name != ''){
        $sql2 .= " last_name = \"$last_name\"";
    }
    $sql = "$sql1 $sql2";
    $result = mysqli_query($db,$sql);
    $data = array();
    if($result !== false){
        $fields = mysqli_fetch_fields($result);
        while($row = mysqli_fetch_array($result))
            $data[] = $row;
    }else{
        echo "Error getting Searching result for employee<br>\n";
        echo "query: $sql<br>\n";
    }

    $return = array();
    $return["data"] = $data;
    $return["fields"] = $fields;
    $result->free();

    return $return;
}


?>
