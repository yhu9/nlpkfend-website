<html>
    <meta http-equiv="refresh" content="1;url=sa_1-3.php" />
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Attendance Table</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../attendance_home.php">Attendance Home</a></span>
                <span ><a class="button" href="sa_1-3.php">S.A 1-3 room</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        
        <br><br>

        <?php
        //connect to database
        include("../../config.php");
        include("queries.php");
        $db = connect();
        checkSession();

        //get post variables
        $id = mysqli_real_escape_string($db,$_POST['id']);
        $type = mysqli_real_escape_string($db,$_POST['type']);
        $verifier = mysqli_real_escape_string($db,$_POST['name']);
        $room_name = "S.A 1-3";
        
        //create the sql statements, execute inserts, and show results
        //insert into attendance
        insertEmployeeAttendance($db,$type,$verifier,$room_name,$id);

        //get last insert data
        $insert_data = getLastInsertData($db,"Employee_Attendance");

        //show last insert data
        showData($insert_data["data"],$insert_data["fields"]);

        //result
        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
        ?>

</body>
</html> 


