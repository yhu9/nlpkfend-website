<html>
<head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>attendance Table</h1>
        <a href="../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../homepage.php">Homepage</a></span>
                <span ><a class="button" href="attendance.php">Attendance Table</a></span>
                <span ><a class="button" href="delete/deleteAttendance_page.php">Delete Attendance</a></span>
                <span ><a class="button" href="update/updateAttendance_page.php">Edit Attendance</a></span>
                <span ><a class="button" href="search/searchAttendance_page.php">Search Attendance</a></span>
                <span ><a class="button" href="view/view_navigation.php">View Attendance</a></span>
                <span ><a class="button" href="../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>

<?php
include("../config.php");
$db = connect();
checkAdvancedSession(3);

//Create the query
include("queries.php");

//This shows the attendances for today
echo "<br>This shows the attendances for today: ".date("m/d/Y")."<br>\n";
//get the attendance Data basic query
$attendanceData = getattendanceBasic($db);


//Show the results
$num_rows = count($attendanceData['data'] ?? []);
showData2($attendanceData['data'],$attendanceData['fields']);

////////////////////////////////////////////////////////////////////////////
if (isset($result) && $result instanceof mysqli_result) $result->free();
$db->close();

?>
</body>
</html> 
