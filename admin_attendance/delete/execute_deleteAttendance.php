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

        //get post variables
        $count = (int)$_POST['count'];
        $vars = explode(" ",$_POST['id']);
        $status = mysqli_real_escape_string($db,$vars[0]);
        $id = mysqli_real_escape_string($db,$vars[1]);

        //execute delete query
        $sql = "";
        if($status == "Employee")
            $sql = "DELETE FROM Employee_Attendance WHERE employee_attendanceID = $id";
        elseif($status == "Student")
            $sql = "DELETE FROM Attendance WHERE attendanceID = $id";

        //show the record we are deleting
        $attendanceData = getAttendanceByID($db,$id,$status);
        showData($attendanceData['data'],$attendanceData['fields']);

        //delete the record
        mysqli_query($db,$sql);
        if($result !== false)
            echo "<br>Successfully Deleted Record<br>\n";
        else
            echo "Could not delete the reord!<br>\n";

        $result->free();
        $db->close();
        ?>
</body>
</html> 


