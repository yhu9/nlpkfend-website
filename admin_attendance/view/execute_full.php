<!DOCTYPE html>
<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    <style>
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

		<script> 
		function download(){
			var a = document.body.appendChild(
				document.createElement("a")
			);
			a.download = "export.html";
			a.href = "data:text/html," + document.getElementById("content").innerHTML; // Grab the HTML
			a.click(); // Trigger a click on the element
		}
        </script>
        <div id='content'>
        <?php
            //connect to database
            include("../../config.php");
            include("../queries.php");
            $db = connect();

            $month = $_POST['month'];
            $year = $_POST['year'];
            $total = showMonthlyAttendance($db,$month,$year);
            echo "<br><br><br>\n";
        ?>
        </div>
		<button id='download' onclick='download()'>Download</button>
    </body>
</html>
