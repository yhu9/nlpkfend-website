<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Attendance Table</h1>
        <a href="../../logout.php">Logout</a>
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
        $db = connect();
        checkSession();

        //get field values for attendance table
        //names from POST are Table column names
        $sql = "SELECT first_name,last_name,Attendance.* FROM Attendance,Student";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            $sql1 = "SELECT attendanceID as ID, last_name,first_name,date,time,verifier,type,room_name,Attendance.auth_type AS auth_type,Attendance.tuition_type AS tuition_type, 'Student' AS status FROM Student,Attendance";
            $sql2 = "WHERE studentID= fk_studentID";
            $sql3 = "UNION";
            $sql4 = "SELECT employee_attendanceID as ID,last_name,first_name,date,time,verifier,type,room_name,'none' AS auth_type,'none' AS tuition_type, 'Employee' AS status FROM Employee,Employee_Attendance";
            $sql5 = "WHERE employeeID = fk_employeeID";
            $sql6 = "ORDER BY date,time";
            
            $finfo = $result->fetch_fields();

            //Intialize and create line $sql2
            foreach($finfo as $field){
                $val_postname = "text_$field->name";
                $eq_postname = "eq_$field->name";
                $val = mysqli_real_escape_string($db,$_POST[$val_postname]);
                $eq = mysqli_real_escape_string($db,$_POST[$eq_postname]);
                if(strpos($field->name,'date') !== false or $field->name == "DOB"){
                    $tmp = mysqli_real_escape_string($db,implode('-',$_POST[$field->name]));
                    if($tmp != '--'){
                        $date = DateTime::createFromFormat("m-d-Y",$tmp);
                        $val = $date->format('Y-m-d');
                    }else
                        $val = "";
                }elseif($field->name == 'time'){
                    $str_time = implode(':',$_POST['time']);
                    if($str_time == ":"){
                        $val = "";
                    }else{
                        $ext = mysqli_real_escape_string($db,$_POST['time_ext']);
                        $str_time = "$str_time $ext";
                        $val = mysqli_real_escape_string($db,date('H:i:s',strtotime($str_time)));
                    }
                }else
                    $val = mysqli_real_escape_string($db,$_POST[$val_postname]);

                $condition = "";
                if($val != "" and $val != "--"){
                    //if field is a numeric
                    if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                        $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                        $field->type == 246)
                    { 
                        $condition = "$field->name $eq $val";
                    //Otherwise it needs quotes
                    }else{
                        $condition = "$field->name $eq '$val'";
                    }

                    $sql2 .= " AND $condition";
                    $sql5 .= " AND $condition";
                }
            }

            //create line $sql3
            $ORDERBY = mysqli_real_escape_string($db,$_POST['orderby']);
            if($ORDERBY != "")
                $sql6 = "ORDER BY $ORDERBY";

            $result->free();
            $sql = "$sql1 $sql2 $sql3 $sql4 $sql5 $sql6";
            $result = mysqli_query($db,$sql);

            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            echo "<form action=\"execute_updateAttendance.php\" method=\"POST\">\n";
            if($result !== false){
                $found = mysqli_num_rows($result);
                echo "<input type='hidden' name='count' value=$found>\n";

                //get the data
                $finfo = $result->fetch_fields();
                $data = array();
                while($row = mysqli_fetch_array($result)){
                    $data[]=$row;
                }

                //save post information
                $tmp = 0;
                foreach($data as $row){
                    $val = $row["ID"];
                    echo "<input type='hidden' name=\"row$tmp\" value=$val>\n";
                    $tmp++;
                }

                //show the results
                showEditableData($data,$finfo);
                echo "<input type='submit' value='Update Values'>\n";

                echo "</form>\n";

            }
            else{
                echo("Query: $sql <br>");
                echo("Error searching: ". mysqli_error($db));
            }

        }else{
            echo "query: $sql <br>\n";
            echo "Could not access database: ". mysqli_error($db);
        }
        
        $result->free();
        $db->close();
    ?>



</body>
</html> 


