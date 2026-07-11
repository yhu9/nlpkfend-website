<html>
<head>
<link rel="stylesheet" type="text/css" href="../../mystyle.css">
</head>
<body>
<h1>Schedule Table</h1>
        <a href="../../logout.php">Logout</a>
        <div class="menu_color">
        <hr>
            <div class="menu">
                    <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                    <span ><a class="button" href="../schedule.php">Schedule Table</a></span>
                    <span ><a class="button" href="../add/addSchedule_page.php">Add Schedule</a></span>
                    <span ><a class="button" href="../search/searchSchedule_page.php">Search Schedule</a></span>
                    <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        
        <br><br>

        <?php
        //connect to database
        include("../../config.php");
        include("../queries.php");
        $db = connect();

        //get post variables
        $oneID = $_POST['id'];
        echo "$oneID<br>";

        //execute delete query
        if($oneID != ""){
            $sql = "DELETE FROM Schedule WHERE scheduleID = $oneID";

            $result = mysqli_query($db,$sql);
            if($result !== false)
                echo "<br>Successfully Deleted Record<br>\n";
            else
                echo "Could not delete the reord!<br>\n";
        }

        if (isset($result) && $result instanceof mysqli_result) $result->free();
        $db->close();
        ?>
</body>
</html> 


