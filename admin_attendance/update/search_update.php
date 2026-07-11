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
        $sql = "SELECT first_name,last_name,Attendance.* FROM Attendance,Student";
        $result = mysqli_query($db,$sql);
        if($result !== false){
            //get pid from post
            $pid = $_POST['id'];

            $sql1 = "SELECT attendanceID as ID, last_name,first_name,date,time,verifier,type,room_name,Attendance.auth_type AS auth_type,Attendance.tuition_type AS tuition_type, 'Student' AS status FROM Student,Attendance";
            $sql2 = "WHERE studentID = fk_studentID AND attendanceID = $pid";
            $sql3 = "UNION";
            $sql4 = "SELECT employee_attendanceID as ID,last_name,first_name,date,time,verifier,type,room_name,'none' AS auth_type,'none' AS tuition_type, 'Employee' AS status FROM Employee,Employee_Attendance";
            $sql5 = "WHERE employeeID = fk_employeeID AND employee_attendanceID = $pid";
            $sql6 = "ORDER BY date,time";
            
            $sql = "$sql1 $sql2 $sql3 $sql4 $sql5 $sql6";
            $result = mysqli_query($db,$sql);

            $finfo = $result->fetch_fields();
            //Show the results and save the values as hidden fields
            //hidden fields:
            //count
            //row1,row2,row3,row4,...
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if($result !== false){
                echo "<form action=\"execute_updateAttendance.php\" method=\"POST\">\n";
                $found = ($result instanceof mysqli_result ? mysqli_num_rows($result) : 0);
                echo "<input type='hidden' name='count' value=$found>\n";

                //get the data
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
        
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
    ?>



</body>
</html> 


