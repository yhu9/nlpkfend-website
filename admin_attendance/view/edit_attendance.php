<!DOCTYPE html>
<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
        <style type='text/css'>
            td {border: solid 1px lightgrey;}
            th {border: solid 1px lightgrey;}
            table {
                border-collapse: collapse;
                border:solid 1px black;
            }
            tr {border: solid 1px black;}
        </style>
    </head>
    <body>
        <h1>Attendance Sheet</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../attendance.php">Attendance Table</a></span>
                <span ><a class="button" href="../delete/deleteAttendance_page.php">Delete Attendance</a></span>
                <span ><a class="button" href="../update/updateAttendance_page.php">Edit Attendance</a></span>
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

            $month = $_POST['month'];
            $year = $_POST['year'];
            echo "<form action='execute_edit.php' method='post'>\n";
            $total = showMonthlyEditable($db,$month,$year);
            echo "<br><br><br>\n";
		    echo "<input class='rectangular' style='width:200px;height:50px;' type='submit' value='Update Now'>\n";
		    echo "</form>\n";
		?>
    </body>
</html>
