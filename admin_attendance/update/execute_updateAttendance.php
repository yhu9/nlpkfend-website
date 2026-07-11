<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Attendance Table</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../attendance.php">Attendance Table</a></span>
                <span ><a class="button" href="../delete/deleteAttendance_page.php">Delete Attendance</a></span>
                <span ><a class="button" href="../update/updateAttendance_page.php">Edit Attendance</a></span>
                <span ><a class="button" href="../search/searchAttendance_page.php">Search Attendance</a></span>
                <span ><a class="button" href="../view/view_navigation.php">View Attendance</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <br><br>

        <?php
        //connect to database
        include("../../config.php");
        include("../queries.php");
        $db = connect();
        checkSession();

        //initialize variables
        $count = (int)$_POST['count'];
        $attendanceIDs = array();
        $statements = array();
        $statuses = array();

        //get attendance fields
        $tmpsql = "SELECT date,time,verifier,type,room_name,Attendance.auth_type AS auth_type,Attendance.tuition_type AS tuition_type FROM Student,Attendance";
        $result = mysqli_query($db,$tmpsql);
        $finfo = $result->fetch_fields();

        //create the sql statements to execute
        for($i = 0; $i < $count; $i++){

            //get the id
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);
            $postname = "status$id";
            $status = mysqli_real_escape_string($db,$_POST["$postname"]);

            //Initialize sql statement sections
            $sql1 = "";
            $sql3 = "";
            if($status == "Employee"){
                $sql1 = "UPDATE Employee_Attendance";
                $sql3 = "WHERE employee_attendanceID = ";
            }elseif($status == "Student"){
                $sql1 = "UPDATE Attendance";
                $sql3 = "WHERE attendanceID = ";
            }
            $sql2 = "SET ";

            //get the id
            $id = mysqli_real_escape_string($db,$_POST["row$i"]);

            //create change flag
            $change = false;

            //check if the row is updated at all and create the body of the sql update statement
            if($result !== false){
                $firstpass = 1;
                foreach($finfo as $field){
                    if($status == "Employee" and $field->name != 'auth_type' and $field->name != 'tuition_type')
                        $predata = getFieldValue($db,"Employee_Attendance",$field->name,$id);
                    elseif($status == "Student")
                        $predata = getFieldValue($db,"Attendance",$field->name,$id);
                    else
                        $predata = "none";

                    if($predata == 'none')
                        $preval = 'none';
                    else
                        $preval = $predata['data'][0][$field->name];
                    
                    $fieldname = "$field->name$id";

                    if($field->name == "DOB" or strpos($field->name,'date') !== false){
                        $tmp = mysqli_real_escape_string($db,implode('-',(array)($_POST[$fieldname] ?? [])));
                        if($tmp != '--'){
                            $date = DateTime::createFromFormat("m-d-Y",$tmp);
                            $newval = $date ? $date->format('Y-m-d') : "";
                        }else
                            $newval = "";
                    }elseif($field->name == 'time'){
                        $str_time = implode(':',(array)($_POST[$fieldname] ?? []));
                        if($str_time == ":"){
                            $newval = "";
                        }else{
                            $ext = mysqli_real_escape_string($db,$_POST["time_ext$id"]);
                            $str_time = "$str_time $ext";
                            $newval = mysqli_real_escape_string($db,date('H:i:s',strtotime($str_time)));
                        }
                    }else
                        $newval = mysqli_real_escape_string($db,$_POST[$fieldname]);

                    //look for a change
                    if(strpos($field->name,'ID') == false AND $preval != $newval){
                        $change = true;
                    }

                    //if change was found in the row
                    if($change){
                        $condition = "";
                        if($firstpass != 1)
                            $condition .= ',';
                        else
                            $firstpass = 0;
                        
                        //if field is a numeric
                        if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                            $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                            $field->type == 246)
                        { 
                            $condition .= "$field->name = $newval";
                        //Otherwise it needs quotes
                        }else{
                            $condition .= "$field->name = '$newval'";
                        }
                        if($status != 'employee' and !($field->name == 'tuition_type' or $field->name == 'auth_type'))
                            $sql2 .= "$condition";
                    }
                }
            }

            //create sql3
            $sql3 .= " $id";

            //combine and push combined sql to statement
            if($change){
                array_push($attendanceIDs,$id);
                array_push($statuses,$status);
                $sql = "$sql1 $sql2 $sql3";
                array_push($statements,$sql);  
            }
        }

        //execute update query
        $count = 0;
        foreach($statements as $sql){
            $id = $attendanceIDs[$count];
            $status = $statuses[$count];
            $result = mysqli_query($db,$sql);
            if($result !== false){
                echo "Updated the Following Attendance!";
                $attendanceData = getAttendanceByID($db,$id,$status);
                showData($attendanceData['data'],$attendanceData['fields']);
            }else{
                echo "Error with sql statement: $sql <br>\n";
            }

            $count++;
        }

        if($count == 0){
            echo "<h1>Nothing Changed</h1>\n";
        }

        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
        ?>
</body>
</html> 
