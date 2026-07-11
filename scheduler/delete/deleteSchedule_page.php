<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../../mystyle.css">
    </head>
    <body>
        <h1>Schedule Table</h1>
        <div class="menu_color">
        <hr>
            <div class="menu">
                <span ><a class="button" href="../../homepage.php">Homepage</a></span>
                <span ><a class="button" href="../schedule.php">Schedule Table</a></span>
                <span ><a class="button" href="../add/addSchedule_page.php">Add Schedule</a></span>
                <span ><a class="button" href="../delete/deleteSchedule_page.php">Delete Schedule</a></span>
                <span ><a class="button" href="../update/updateSchedule_page.php">Edit Schedule</a></span>
                <span ><a class="button" href="../search/searchSchedule_page.php">Search Schedule</a></span>
                <span ><a class="button" href="../../logout.php">Logout</a></span>
            </div>
        <hr>
        </div>
        <form action="search_deleteSchedule.php" method="post">
            <h3>Search for the schedule or schedules to delete</h3>
            <u>NOTE!</u><br>
            1. Empty fields will not be used<br>
            <br><br>
            <form action="searchSchedule.php" method="post">

                <?php
                    //connect to database
                    include("../../config.php");
                    $db = connect();
                    checkSession();

                    //create query
                    $sql = "SELECT * FROM Schedule";
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

                    if (isset($result) && $result instanceof mysqli_result) $result->free();
                    $db->close();
                ?>
            <input type="submit" action="search_deleteSchedule.php" value="Search for schedules to delete">
        </form>
    </body>
</html>
