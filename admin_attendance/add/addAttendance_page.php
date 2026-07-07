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
                <span ><a class="button" href="../update/updateAttendance_page.php">Edit Attendance</a></span>
                <span ><a class="button" href="../search/searchAttendance_page.php">Search Attendance</a></span>
                <span ><a class="button" href="../view/view_navigation.php">View Attendance</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
            <h3>Fill Out Your Attendance to Add</h3>
            <br><br>

                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkSession();

                    //create query
                    $sql = "SELECT '' AS last_name,'' AS first_name,date,time,type,verifier,room_name FROM Attendance";
                    $result = mysqli_query($db,$sql);

                    //Query Successful
                    if($result !== false){
                        $fields = mysqli_fetch_fields($result);
                        $data = array();
                        while($row = mysqli_fetch_array($result))
                            $data[] = $row;

                        //show the add Attendance Form
                        echo "<form action=\"executeAddAttendance.php\" method=\"post\">";
                        showAddForm($data,$fields);
                        echo "<input type=\"submit\" action=\"executeAddAttendance.php\" value=\"Add Student Attendance\">\n";
                        echo "<input type=\"submit\" formaction=\"executeAddEmployeeAttendance.php\" value=\"Add Employee Attendance\">\n";
                        echo "</form>\n";

                    //Query FAILED
                    }else{
                        echo("Error Description: ".mysqli_error($db));
                    }

                    $result->free();
                    $db->close();
                ?>
    </body>
</html>
