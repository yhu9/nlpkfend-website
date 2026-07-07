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
        <form action="search_updateAttendance.php" method="post">
            <h3>Search for the attendance or attendances to delete</h3>
            <u>NOTE!</u><br>
            1. Empty fields will not be used<br>
            <br><br>
                <select class="selectpicker" name="orderby">
                    <option value="" selected>Order By</option>
                    <option>attendanceID</option>
                    <option>first_name</option>
                    <option>last_name</option>
                    <option>start_date</option>
                </select><br>

                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkSession();

                    //create query
                    $sql = "SELECT * FROM Attendance";
                    $result = mysqli_query($db,$sql);
                    $data = array();

                    //Query Successful
                    if($result !== false){
                        $fields = mysqli_fetch_fields($result);
                        while($row = mysqli_fetch_array($result))
                            $data[] = $row;

                        showSearchForm($data,$fields);                       

                    //Query FAILED
                    }else{
                        echo("Error Description: ".mysqli_error($db));
                    }

                    $result->free();
                    $db->close();
                ?>
            <input type="submit" action="search_updateAttendance.php" value="Search For Attendances To Update">
        </form>
    </body>
</html>
