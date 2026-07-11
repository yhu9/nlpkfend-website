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
                <span ><a class="button" href="../add/addAttendance_page.php">Add Attendance</a></span>
                <span ><a class="button" href="../delete/deleteAttendance_page.php">Delete Attendance</a></span>
                <span ><a class="button" href="../update/updateAttendance_page.php">Update Attendance</a></span>
                <span ><a class="button" href="../search/searchAttendance_page.php">Search Attendance</a></span>
                <span ><a class="button" href="../view/view_navigation.php">View Attendance</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

        <?php
            //connect to database
            include("../../config.php");
            include("../queries.php");
            $db = connect();
            checkSession();

            //get field values for attendance to add
            //names from POST are Table column names
            $sql = "SELECT * FROM Employee_Attendance";
            $result = mysqli_query($db,$sql);
            $values = array();
            if($result !== false){
                $finfo = $result->fetch_fields();

                //Initialize and create the add attendance sql statement
                $sql1 = "INSERT INTO Employee_Attendance (";
                $sql2 = "VALUES (";
                $is_first = 1;
                foreach($finfo as $field){
                    if($field->name != "attendanceID"){
                        $str_fieldname = mysqli_real_escape_string($db,$field->name);
                        $val = "";
                        if(strpos($field->name,'date') !== false or $field->name == "DOB"){
                            $tmp = mysqli_real_escape_string($db,implode('-',$_POST[$field->name]));
                            $date = DateTime::createFromFormat("m-d-Y",$tmp);
                            $val = $date->format('Y-m-d');
                        }elseif($field->name == 'time'){
                            $str_time = implode(':',$_POST['time']);
                            if($str_time == ":"){
                                $val = "";
                            }else{
                                $ext = mysqli_real_escape_string($db,$_POST['time_ext']);
                                $str_time = "$str_time $ext";
                                $val = mysqli_real_escape_string($db,date('H:i:s',strtotime($str_time)));
                            }
                        }elseif($field->name == "age"){
                            $tmp = mysqli_real_escape_string($db,implode('-',$_POST['DOB']));
                            $date = DateTime::createFromFormat("m-d-Y",$tmp);
                            $DOB = $date->format('Y-m-d');
                            $val = mysqli_real_escape_string($db,"(SELECT TRUNCATE(DATEDIFF(NOW(),'$DOB') / 365.25, 2) as age)");
                            $val = str_replace(array('"'), '', stripslashes($val));
                        }elseif(strpos($field->name,'fk_') !== false){
                            if (isset($result) && $result instanceof mysqli_result) $result->free();
                            $first_name = mysqli_real_escape_string($db,$_POST["first_name"]);
                            $last_name = mysqli_real_escape_string($db,$_POST["last_name"]);
                            $employeeID = 0;
                            $employee_data = getEmployeeByName($db,$first_name,$last_name);
                            if(count($employee_data['data']) == 1)
                            {
                                echo "<h2>employee for which attendance is added<br></h2>";
                                showData($employee_data['data'],$employee_data['fields']);
                                $employeeID = $employee_data['data'][0]['employeeID'];
                            }else{
                                echo "Could not find Employee<br>\n";
                                echo "firstname: $first_name<br>\n";
                                echo "lastname: $last_name<br>\n";
                            }
                                
                            $val = "".$employeeID;
                        }
                        else
                            $val = mysqli_real_escape_string($db,$_POST[$field->name]);

                        if($val != "" and $val != "--"){
                            if($field->type == 16 OR $field->type == 1 OR $field->type == 2 OR $field->type == 3 OR
                                $field->type == 8 OR $field->type == 9 OR $field->type == 4 OR $field->type == 5 OR
                                $field->type == 246){
                                if($is_first == 1){
                                    $sql1 .= "$str_fieldname";
                                    $sql2 .= "$val";
                                    $is_first= 0;
                                }
                                else{
                                    $sql1 .= ",$str_fieldname";
                                    $sql2 .= ",$val";
                                }
                            }else{
                                if($is_first == 1){
                                    $sql1 .= "$str_fieldname";
                                    $sql2 .= "'$val'";
                                    $is_first= 0;
                                }
                                else{
                                    $sql1 .= ",$str_fieldname";
                                    $sql2 .= ",'$val'";
                                }
                            } 
                        }
                    }
                }
                $sql1 .= ")";
                $sql2 .= ")";

                //Create the combined sql statement and execute the addition of the new attendance
                if (isset($result) && $result instanceof mysqli_result) $result->free();
                $sql = "$sql1 $sql2";
                $result = mysqli_query($db,$sql);

                //Check to make sure the INSERT statement executed
                if($result !== false){
                    echo "<h3 align=\"center\">Successfully added new attendance!</h3>";

                    //get last inserted attendance
                    $attendanceData = getLastInsert($db,"Employee_Attendance");

                    //show attendance data inserted
                    showData($attendanceData['data'],$attendanceData['fields']);

                }else{
                    echo("sql statement: " .$sql);
                    echo "<br>";
                    echo("Could not add the new attendance: <b>" .mysqli_error($db). "</b>");
                }
            }else{
                echo("sql statement: ".$sql);
                echo "<br>";
                echo("Could not access database fields: <b>" .mysqli_error($db). "</b>");
            }
            
            if (isset($result) && $result instanceof mysqli_result) $result->free();
            $db->close();
        ?>
    

    </body>
</html>
